<?php
/**
 * User: anubis
 * Date: 13.10.13
 * Time: 17:47
 */

namespace aascms\widgets;


use Slim\Extras\Views\Twig;

class WidgetFactory {

    /**
     * @var Twig
     */
    private static $twig = null;

    public static function show($widgetName, $method) {
        self::checkTwig();
        $path = __DIR__.'/';
        $class = str_replace(__NAMESPACE__.'\\', '', $widgetName);
        $class = explode('\\', $class);
        $path .= $class[0].'/tpl/';
        self::$twig->setTemplatesDirectory($path);
        $widget = new $widgetName(self::$twig);
        $widget->$method();
    }

    private static function checkTwig() {
        if(is_null(self::$twig)) {
            self::$twig = new Twig();
        }
    }
}