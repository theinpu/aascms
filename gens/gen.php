<?php
/**
 * User: anubis
 * Date: 11.08.13
 * Time: 15:02
 */

spl_autoload_register(function ($class) {
    $className = str_replace('aascms\\', '', $class);
    $file = str_replace('\\', DIRECTORY_SEPARATOR, $className).'.php';
    require_once $file;
});
error_reporting(E_ALL & ~E_NOTICE);

if($argc <= 1) {
    echo "Usage: gen.php <class> [-f]\r\n";
    exit;
}

$args = $argv;
array_shift($args);

$class = "aascms\\model\\".$args[0];
$toFile = false;
if(isset($args[1])) {
    $toFile = $args[1] == '-f';
}

$gen = new \aascms\gens\Generator($class);

$gen->generate($toFile);