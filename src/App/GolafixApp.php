<?php

    
    namespace Golafix\App;
    use Gismo\Component\Config\AppConfig;
    use Gismo\Component\HttpFoundation\Request\Request;
    use Gismo\Component\Plugin\App;
    use Gismo\Component\Plugin\Loader\JsonFilePluginLoader;
    use Gismo\Component\Route\Type\RouterRequest;
    use Golafix\Conf\DotGolafixYml;
    use Golafix\Conf\GolafixRouter;
    use Golafix\Conf\ZipPool;

    /**
     * Created by PhpStorm.
     * User: matthes
     * Date: 21.06.17
     * Time: 10:57
     */
    class GolafixApp implements App {
        
        /**
         * @var FrontendContext
         */
        private $mContext;

        private $mYmlFile = null;

        public function __construct(AppConfig $config, $filenamePlugins) {
            $debug = false;
            if ($config->ENVIRONMENT === "DEVELOPMENT")
                $debug = true;
            $this->mContext = $c = new FrontendContext(true);

            $pluginLoader = new JsonFilePluginLoader($this->mContext);
            $pluginLoader->initPluginsFromFile($filenamePlugins);

        }



        public function setGolafixYmlFile ($filename) {
            $this->mYmlFile = $filename;
        }

        public function run(Request $request) {
            $p = $this->mContext;
            $p[Request::class] = $p->constant($request);
            $p[ZipPool::class] = $zipPool = new ZipPool("/tmp", true);

            $golafixFile = $zipPool->verify($this->mYmlFile);

            $p["conf.golafix.yml"] = $p[DotGolafixYml::class] = $golafixYml = new DotGolafixYml($p, $golafixFile);


            $ret = $golafixYml->parseFile($golafixFile);

            //print_r ($ret);

            foreach ($ret as $key => $val) {
                if (preg_match ("|^tpl.|", $key)) {
                    $p[$key] = $p->template($val);
                }
            }

            $p[GolafixRouter::class] = $p->service(function () use (&$ret) {
                return new GolafixRouter($ret["routes"]);
            });

            $p->trigger("event.app.onrequest");
            $routeRequest = RouterRequest::BuildFromRequest($request);
            $p->route->dispatch($routeRequest);
        }
    }