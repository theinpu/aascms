<?php
/**
 * User: anubis
 * Date: 13.10.13
 * Time: 17:01
 */

namespace aascms\app\ext;

use aascms\app\Application;
use aascms\widgets\WidgetFactory;
use Twig_Extension;

class TwigWidgetExtension extends Twig_Extension {

    /**
     * @var Application
     */
    private $app;

    function __construct($app) {
        $this->app = $app;
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    function getName() {
        return "TwigWidgetExtension";
    }

    public function getFunctions() {
        return array(
            'widget' => new \Twig_Function_Method($this, 'showWidget')
        );
    }

    public function showWidget($widgetName, $method = '') {
        $widgetName = 'aascms\\widgets\\'.$widgetName;
        if(empty($method)) {
            $method = 'widgetDefault';
        }
        WidgetFactory::show($widgetName, $method);
    }

}