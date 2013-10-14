<?php
/**
 * User: anubis
 * Date: 05.08.13
 * Time: 22:57
 */

use aascms\lib\PDOHelper;

$_SERVER['REMOTE_ADDR'] = '127.0.0.1';

require_once 'vendor/autoload.php';

spl_autoload_register(function ($class) {
    $className = str_replace('aascms\\', '', $class);
    $file = str_replace('\\', DIRECTORY_SEPARATOR, $className).'.php';
    if(!file_exists($file)) return;
    require_once $file;
});

function connect() {
}

function cleanUp() {

}