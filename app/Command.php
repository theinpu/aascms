<?php
/**
 * User: anubis
 * Date: 12.08.13
 * Time: 15:03
 */

namespace aascms\app;


use aascms\model\exceptions\ClassNotFoundException;
use aascms\model\exceptions\MethodNotFoundException;

class Command {

    private $controller;
    private $method;
    private $app;

    public function __construct($controller, $method) {
        $this->controller = $controller;
        $this->method = $method;
    }

    /**
     * @throws \aascms\model\exceptions\ClassNotFoundException
     * @throws \aascms\model\exceptions\MethodNotFoundException
     * @return mixed
     */
    public function execute() {
        $className = 'aascms\\app\\controllers\\'.$this->controller;
        if(!class_exists($className)) {
            throw new ClassNotFoundException($className);
        }
        $object = new $className($this->app);
        if(!method_exists($object, $this->method)) {
            throw new MethodNotFoundException($className, $this->method);
        }

        return call_user_func_array(array($object, $this->method), func_get_args());
    }

    public final function getCallback() {
        return array($this, 'execute');
    }

    public function setApp($app) {
        $this->app = $app;
    }

}