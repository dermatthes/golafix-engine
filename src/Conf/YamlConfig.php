<?php
    /**
     * Created by PhpStorm.
     * User: matthes
     * Date: 19.07.17
     * Time: 17:11
     */

    namespace Golafix\Conf;


    class YamlConfig {


        private $mRelPath;
        private $mFileName;


        public function __construct(string $fileName) {
            $this->mRelPath = dirname($fileName);
            $this->mFileName = basename($fileName);
        }






        public function parse () {

        }


    }