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
    use Golafix\Conf\GolafixRouter;
    use Symfony\Component\Yaml\Yaml;

    class DotGolafixYml extends EYAML implements GoAssetContainer {
        use GoAssetContainerTrait;

        private $data = null;

        private $mDi;

        
        public function __construct(DiContainer $di, $filename) {
            $this->mDi = $di;
            $this->__asset_container_init($di, dirname($filename));



            $this->data = $this->parseFile($filename);
        }



        public function getRouter () : GolafixRouter {
            return new GolafixRouter($this->data["routes"]);
        }


    }