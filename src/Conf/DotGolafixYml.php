<?php
    /**
     * Created by PhpStorm.
     * User: matthes
     * Date: 17.07.17
     * Time: 20:10
     */

    namespace Golafix\Conf;


    use Gismo\Component\Application\Assets\GoAssetContainer;
    use Gismo\Component\Application\Assets\GoAssetContainerTrait;
    use Gismo\Component\Di\DiContainer;
    use Golafix\Conf\GolafixRouter;
    use Symfony\Component\Yaml\Yaml;

    class DotGolafixYml implements GoAssetContainer {
        use GoAssetContainerTrait;

        private $data = null;

        
        
        public function __construct(DiContainer $di, $filename) {
            $this->__asset_container_init($di, dirname($filename));
            $this->data = Yaml::parse(file_get_contents($filename));
        }


        /**
         * @param string $relativePath
         * @return string
         */
        public function absolutePath (string $relativePath) : string {
            return $this->_getAssetPath() . "/" . $relativePath;
        }


        public function getRouter () : GolafixRouter {
            return new GolafixRouter($this->data["routes"]);
        }


    }