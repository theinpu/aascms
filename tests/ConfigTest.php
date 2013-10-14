<?php
/**
 * User: anubis
 * Date: 13.10.13
 * Time: 14:18
 */

namespace aascms\tests\lib;


use aascms\lib\Config;

class ConfigTest extends \PHPUnit_Framework_TestCase {

    public function testDefaultConfig() {
        $this->assertEquals('root', Config::getPDOUser());
    }
}
