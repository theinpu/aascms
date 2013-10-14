<?php
/**
 * User: anubis
 * Date: 14.08.13
 * Time: 0:42
 */

namespace aascms\app\controllers;

use aascms\app\Application;

abstract class GenericController {

    /**
     * @var \aascms\app\Application
     */
    private $app;

    public function __construct(Application $app) {
        $this->app = $app;
    }

    protected function template($template) {
        $this->app->getSlim()->view()->display($template.'.twig');
    }

    protected function addData($data) {
        $this->app->getSlim()->view()->appendData($data);
    }

    /**
     * @return \Slim\Slim
     */
    protected final function getSlim() {
        return $this->app->getSlim();
    }

    /**
     * @return \Slim\Http\Request
     */
    protected final function getRequest() {
        return $this->app->getSlim()->request();
    }

}