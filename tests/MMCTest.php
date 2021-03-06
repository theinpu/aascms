<?php
/**
 * User: anubis
 * Date: 20.09.13 11:53
 */

namespace aascms\tests\model;

use aascms\lib\mmc\MemcacheObject;
use aascms\lib\mmc\MMC;
use aascms\model\system\users\User;

/**
 * Class MMCTest
 * @package aascms\tests\model
 * @requires extension memcache
 */
class MMCTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var MemcacheObject
     */
    private $simpleValue;

    public function testSetValue() {
        $this->assertTrue(MMC::set($this->simpleValue));
    }

    /**
     * @depends testSetValue
     */
    public function testGetValue() {
        $this->assertEquals($this->simpleValue->value, MMC::get($this->simpleValue->key));
    }

    /**
     * @depends testSetValue
     */
    public function testDeleteValue() {
        $this->assertNotNull(MMC::get($this->simpleValue->key));
        MMC::del($this->simpleValue->key);
        $this->assertNull(MMC::get($this->simpleValue->key));
    }

    public function testExpire() {
        $expireVal = new MemcacheObject('expire_test', 'value', 1);
        MMC::set($expireVal);
        sleep(1);
        $this->assertFalse(MMC::exists($expireVal->key));
    }

    public function testGetWrong() {
        $this->assertNull(MMC::get('wrong_key'));
    }

    public function testTags() {
        $vals = array(
            new MemcacheObject('key1', 'value1', 3600, array('tag1')),
            new MemcacheObject('key2', 'value2', 3600, array('tag2')),
            new MemcacheObject('key3', 'value3', 3600, array('tag1', 'tag2')),
            new MemcacheObject('key4', 'value4', 1, array('tag1'))
        );

        foreach($vals as $val) {
            MMC::set($val);
        }

        $savedVals = MMC::getByTag('tag1');
        $this->assertEquals(array($vals[0]->value, $vals[2]->value, $vals[3]->value), $savedVals);
        sleep(1);
        $savedVals = MMC::getByTag('tag1');
        $this->assertEquals(array($vals[0]->value, $vals[2]->value), $savedVals);
        MMC::delByTag('tag2');
        $this->assertCount(0, MMC::getByTag('tag2'));
    }

    public function testFlush() {
        MMC::set($this->simpleValue);
        MMC::flush();
        $this->assertNull(MMC::get($this->simpleValue->key));
    }

    public function testObjectSave() {
        $testObject = new \stdClass();
        $testObject->field = 'test';
        $val = new MemcacheObject('test_object', $testObject);
        $this->assertTrue(MMC::set($val));
        $this->assertEquals($testObject, MMC::get('test_object'));
    }

    protected function setUp() {
        $this->simpleValue = new MemcacheObject('test_key', 'value');
    }

    protected function tearDown() {
        $this->simpleValue = null;
    }

    public static function tearDownAfterClass() {
        MMC::flush();
    }

}