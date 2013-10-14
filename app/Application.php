<?php
/**
 * User: anubis
 * Date: 12.08.13
 * Time: 14:56
 */

namespace aascms\app;


use aascms\app\ext\TwigWidgetExtension;
use Slim\Extras\Views\Twig;
use Slim\Slim;

class Application {

    /**
     * @var \Slim\Slim
     */
    private $slim;

    public function __construct($settings = array()) {

        Twig::$twigDirectory = dirname(__FILE__).'/../vendor/twig';
        Twig::$twigOptions = array(
            'debug'      => true,
            'autoescape' => false,
            //'cache' => '/tmp/twig'
        );
        Twig::$twigExtensions = array(
            new TwigWidgetExtension($this),
            'Twig_Extensions_Slim',
        );

        $twig = new Twig();

        $settings['view'] = $twig;

        $this->slim = new Slim($settings);
        $this->initRoutes();
    }

    public function run() {
        $this->slim->run();
    }

    protected function initRoutes() {

    }

    /**
     * @return \Slim\Slim
     */
    public final function getSlim() {
        return $this->slim;
    }

    protected function addGetCommand($pattern, Command $command) {
        $command->setApp($this);

        return $this->slim->get($pattern, $command->getCallback());
    }

    protected function addPostCommand($pattern, Command $command) {
        $command->setApp($this);
        $this->slim->post($pattern, $command->getCallback());
    }
}