<?php
    /**
     * Created by PhpStorm.
     * User: matthes
     * Date: 17.07.17
     * Time: 20:19
     */

    namespace Golafix\Conf;



    class GolafixRouter {

        private $routes;
        
        public function __construct(array $routes) {
            foreach ($routes as $key => $value) {
                $this->routes[$key] = new GolafixRoute($value);
            }
        }
        
        
        public function getBestRoute (string $request) : GolafixRoute {
            if (isset ($this->routes[$request]))
                return $this->routes[$request];
            if (isset ($this->routes["E404"]))
                return $this->routes["E404"];
            throw new \Exception("Unknown route for request '$request' - No error page was defined.");
        }


    }