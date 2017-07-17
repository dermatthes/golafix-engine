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

                $context->addTemplatePath(GOLAFIX_TEMPLATE_PATH);
                
                
                
                $context[DotGolafixYml::class] = $context->factory(function () {
                    return new DotGolafixYml(GOLAFIX_YAML_FILE);
                });
                
                $context->route->add("/about", function () use ($context) {
                    echo ( $context["tpl.page.login"] )();
                });
                
                $context->route->add("/::path", function ($path, DotGolafixYml $dotGolafixYml) use ($context) {
                    $route = $dotGolafixYml->getRouter()->getBestRoute(implode ("/", $path));
                    $context["page.cur"] = $context->template($dotGolafixYml->absolutePath($route->target));

                    echo ( $context["page.cur"] )();
                });
            }
        }

    }