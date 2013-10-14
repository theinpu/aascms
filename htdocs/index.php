<?php

error_reporting(E_ALL);

set_include_path(get_include_path().':'.dirname(__FILE__).'/../');

require_once 'vendor/autoload.php';

spl_autoload_register(function ($class) {
    $className = str_replace('aascms\\', '', $class);
    $file = str_replace('\\', DIRECTORY_SEPARATOR, $className).'.php';
    if(file_exists(dirname(__FILE__).'/../'.$file)) {
        require_once $file;
    }
});

$app = new \aascms\app\SimpleApplication();

$app->run();