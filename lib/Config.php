<?php
/**
 * User: anubis
 * Date: 13.10.13
 * Time: 14:06
 */

namespace aascms\lib;

use aascms\model\exceptions\Exception;

class Config {

    private static $cfg = null;


    private static function checkCfg() {
        if(is_null(self::$cfg)) {
            $cfgFile = __DIR__.'/../config/config.ini';
            $defaultCfgFile = $cfgFile.'.default';
            if(!file_exists($cfgFile)) {
                if(!file_exists($defaultCfgFile)) {
                    throw new Exception("Default config file is not found");
                }
                if(!copy($defaultCfgFile, $cfgFile)) {
                    throw new \RuntimeException("on copy config");
                }
            }
            self::$cfg = parse_ini_file($cfgFile, true);
        }
    }

    public static function getPDOUser() {
        self::checkCfg();

        return self::$cfg['PDO']['user'];
    }


    public static function getPDODsn() {
        self::checkCfg();

        return self::$cfg['PDO']['dsn'];
    }

    public static function getPDOPassword() {
        self::checkCfg();

        return self::$cfg['PDO']['password'];
    }

    public static function getPDOEncoding() {
        self::checkCfg();

        return self::$cfg['PDO']['encoding'];
    }

    public static function getConfigs() {
        self::checkCfg();

        return self::$cfg;
    }
}