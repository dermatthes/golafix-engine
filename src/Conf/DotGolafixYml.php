<?php
    /**
     * Created by PhpStorm.
     * User: matthes
     * Date: 17.07.17
     * Time: 20:10
     */

    namespace Golafix\Conf;


    use EYAML\EYAML;
    use Gismo\Component\Application\Assets\GoAssetContainer;
    use Gismo\Component\Application\Assets\GoAssetContainerTrait;
    use Gismo\Component\Di\DiContainer;
    use Golafix\App\FrontendContext;
    use Golafix\Conf\GolafixRouter;
    use Symfony\Component\Yaml\Yaml;

    class DotGolafixYml extends EYAML implements GoAssetContainer {
        use GoAssetContainerTrait;


        private $mDi;

        
        public function __construct(FrontendContext $di, $filename) {
            $this->mDi = $di;
            $this->__asset_container_init($di, dirname($filename));



            // Funktionen in YAML definieren
            $this->registerIncludeFunction(function ($fileName, EYAML $parser) use ($di) {
                $parser = clone $parser;
                if (preg_match ("|^https?://|", $fileName)) {
                    $zipPool = $di[ZipPool::class];
                    /* @var $zipPool \Golafix\Conf\ZipPool */
                    $newPath = $zipPool->verify($fileName);
                } else {
                    $newPath = $parser->getPath() . "/" . $fileName;
                }
                $parser->checkRecursion($newPath);
                //$this->mDi["conf.yaml." . basename($fileName) . "." . sha1($fileName)] = $parser;
                return $parser->parseFile($newPath);
            });

            $this->registerFunction("asset", function (array $params) {
                return $this->getAssetLinkUrl($params[0]);
            });
            $this->registerFunction("file", function (array $params, EYAML $parser) {
                return $parser->getPath() . "/" . $params[0];
            });
        }






    }