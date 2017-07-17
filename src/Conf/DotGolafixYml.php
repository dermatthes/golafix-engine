<?php
    /**
     * Created by PhpStorm.
     * User: matthes
     * Date: 17.07.17
     * Time: 20:10
     */

    namespace Golafix\Conf;


    use Golafix\Conf\GolafixRouter;
    use Symfony\Component\Yaml\Yaml;

    class DotGolafixYml {

        private $data = null;

        private $absolutePath = "";

        public function __construct($filename) {
            $this->absolutePath = dirname($filename);
            $this->data = Yaml::parse(file_get_contents($filename));
        }


        /**
         * @param string $relativePath
         * @return string
         */
        public function absolutePath (string $relativePath) : string {
            return $this->absolutePath . "/" . $relativePath;
        }


        public function getRouter () : GolafixRouter {
            return new GolafixRouter($this->data["routes"]);
        }

        
        

    }