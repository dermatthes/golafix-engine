<?php
/**
 * Created by PhpStorm.
 * User: matthes
 * Date: 25.07.17
 * Time: 09:31
 */

namespace Golafix\Conf;


use Curl\Curl;

class ZipPool
{

    private $mPath;
    private $mAllowLocalFile;

    private $mDownloadAliases = [];

    private $mConf = [
        "limit.max_download_size" => 50 * 1024 * 1024,  // Default: 50 MB
        "limit.max_lock_time" => 60
    ];

    public function __construct(string $storePath, $allowLocalFile=true)
    {
        $this->mPath = $storePath;
        $this->mAllowLocalFile = $allowLocalFile;

        // https://github.com/symfony/yaml.git
        $this->addDownloadAlias("%^https?\\://github\\.com/(?<user>[a-z0-9-_\\.]+)/(?<repo>[a-z0-9-_\\.]+)\\.git(|\\:(?<branch>[a-z0-9\\.\\-_]+))$%i",
            function ($matches, &$basePath) {
                if (@$matches["branch"] == null)
                    $matches["branch"] = "master";
                $basePath = "" . $matches["repo"] . "-" . $matches["branch"] . "/";
                return "https://github.com/{$matches["user"]}/{$matches["repo"]}/archive/{$matches["branch"]}.zip";
            }
        );
    }


    /**
     * @param string   $regEx
     * @param callable $fn
     *
     * @return $this
     */
    public function addDownloadAlias(string $regEx, callable $fn) {
        $this->mDownloadAliases[$regEx] = $fn;
        return $this;
    }


    public function download($url, $targetFileName) {
        $lockfile = $targetFileName . ".lock";
        if (file_exists($lockfile)) {
            if (((int)file_get_contents($lockfile)) > ( time() - $this->mConf["limit.max_lock_time"]) ) {
                throw new \Exception("Download lock active. ({$this->mConf["limit.max_lock_time"]}sec) ");
            }

        }
        file_put_contents($lockfile, time());

        $curl = new Curl();

        $tmpname = tempnam("/tmp", "ZpDown");
        $out = fopen($tmpname, "w+");
        if ($out === false)
            throw new \Exception("Cannot open output stream.");
        $size = 0;
        $curl->setOpt(CURLOPT_WRITEFUNCTION, function ($ch, $str) use (&$size) {
            $size += strlen($str);
            if ($size > $this->mConf["limit.max_download_size"]) {
                throw new \Exception("File-size limit ({$this->mConf["max.download_size"]} Bytes) reached.");
            }

            return strlen($str);
        });
        $curl->setOpt(CURLOPT_FILE, $out);
        $curl->setOpt(CURLOPT_HEADER, 0);
        $curl->setOpt(CURLOPT_FOLLOWLOCATION, 1);

        try {
            $curl->get($url);
            fclose($out);
        } catch (\Exception $e) {
            throw $e;
        }


        $archive = new \ZipArchive();
        $zipErr = $archive->open($tmpname, \ZipArchive::CHECKCONS);
        if ($zipErr !== true) {
            throw new \Exception("Zip Archive invalid. (#$zipErr) Source: $url");
        }
        $archive->close();

        if ( ! rename($tmpname, $targetFileName))
            throw new \Exception("Can't move downloaded file to pool.");
        unlink($lockfile);
    }


    /**
     * @param string $uri
     *
     * @return string
     * @throws \Exception
     */
    public function verify(string $uri) {
        if (preg_match ("|^https?\\://|", $uri)) {
            $path = "";
            if (strpos($uri, "#") !== false) {
                list ($uri, $path) = explode("#", $uri, 2);
            }

            $archiveBasePath = "";
            foreach ($this->mDownloadAliases as $regEx => $fn) {
                if (preg_match($regEx, $uri, $matches)) {
                    $uri = $fn($matches, $archiveBasePath);
                    break;
                }
            }

            $storeId = sha1($uri);
            $storePath = $this->mPath . "/$storeId.zip";
            $retUrl = "zip://$storePath#{$archiveBasePath}{$path}";
            if ( ! file_exists($storePath))
                $this->download($uri, $storePath);
            return $retUrl;
        } else {
            if ( ! $this->mAllowLocalFile) {
                throw new \Exception(
                    "Local files are not permitted. (Accessing file: $uri"
                );
            }
            return $uri;
        }

    }


}