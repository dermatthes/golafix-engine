<?php
    
    namespace Golafix\App;
    use Gismo\Component\Application\Context;
    use Gismo\Component\HttpFoundation\Request\Request;
    use Gismo\Component\Plugin\Plugin;
    use Gismo\Component\Route\Type\RouterRequest;
    use Golafix\Conf\DotGolafixYml;
    use Golafix\Conf\GolafixRouter;

    /**
     * Created by PhpStorm.
     * User: matthes
     * Date: 21.06.17
     * Time: 10:59
     */ 
    class BasePlugin implements Plugin {

        public function onContextInit(Context $context) {
            



            // Entwicklerseite
            if ($context instanceof FrontendContext) {

                $context->route->add("::path", function ($path, DotGolafixYml $dotGolafixYml, GolafixRouter $router) use ($context) {
                    $route = $router->getBestRoute($path);
                    $context["page.cur"] = $context->template($dotGolafixYml->getPath() . $route->target);

                    echo ( $context["page.cur"] )();
                });
            }
        }

    }