<?php
    /**
     * Created by PhpStorm.
     * User: matthes
     * Date: 17.07.17
     * Time: 20:20
     */

    namespace Golafix\Conf;


    class GolafixRoute {

        /**
         * The target File to output
         *
         * @var string
         */
        public $target;

        public function __construct(array $input) {
            $this->target = $input["target"];
        }

    }