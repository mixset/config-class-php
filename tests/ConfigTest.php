<?php

namespace Mixset\ConfigManager\Tests;

use Mixset\ConfigManager\Config;
use Mixset\ConfigManager\Exceptions\ConfigException;
use ReflectionMethod;

require 'src/Config.php';
require 'src/exceptions/ConfigException.php';

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    private $config;

    const DEFAULT_INI_PATH = 'src/config/config.ini';

    public function setUp()
    {
        $this->config = new Config();
        $this->config->setPath(self::DEFAULT_INI_PATH);
    }

    public function testSetPath()
    {
        $this->assertEquals(self::DEFAULT_INI_PATH, $this->config->getPath());
    }

    public function testEmptyPath()
    {
        $this->expectException(ConfigException::class);
        $this->expectExceptionMessage('File path is not specified.');

        $this->config->setPath('');
        $this->config->init();
    }

    public function testConfigExtensionConstant()
    {
        $this->assertEquals('ini', Config::CONFIG_FILE_EXTENSION);
    }

    public function testInitializeMethod()
    {
        $this->expectException(ConfigException::class);
        $this->expectExceptionMessage('File has to have extension ini.');

        $this->config->setPath('src/config/config.php');
        $this->config->init();
    }

    public function testsetMethodString()
    {
        $this->assertTrue($this->config->set('key', 'value'));
    }

    public function testsetMethodInt()
    {
        $this->assertTrue($this->config->set('key', 1));
    }

    public function testsetBoolean()
    {
        $this->assertTrue($this->config->set('key', true));
    }

    public function testsetException()
    {
        $this->expectException(ConfigException::class);
        $this->expectExceptionMessage('Given key ( test ) already exists in a src/config/config.ini');

        $this->config->set('test', 'value');
        $this->config->set('test', 'bar');
    }

    public function testsetWithArrayException()
    {
        $this->expectException(ConfigException::class);
        $this->expectExceptionMessage('Given key ( test ) already exists in a src/config/config.ini');

        $this->config->set('test', 'value');
        $this->config->set('test', 'bar');
    }

    public function testsetArray()
    {
        $this->assertTrue($this->config->setArray('test'));
    }

    public function testsetArrayExistsException()
    {
        $this->expectException(ConfigException::class);
        $this->expectExceptionMessage('Given array name ( test ) already exists in a src/config/config.ini');

        $this->config->setArray('test');
        $this->config->setArray('test');
    }

    public function testCorrectArrayFormat()
    {
        $this->config->setArray('test');

        $this->assertEquals(
            '[test]',
            trim(file_get_contents(self::DEFAULT_INI_PATH))
        );
    }

    public function testNonExistingFileArrayExists()
    {
        unlink(self::DEFAULT_INI_PATH);

        $this->assertNull(
            $this->makeMethodAccessible(
                Config::class,
                'isArrayExist',
                ['test', self::DEFAULT_INI_PATH]
            )
        );
    }

    public function testArrayExists()
    {
        $this->config->setArray('test');
        $this->assertTrue(
            $this->makeMethodAccessible(
                Config::class,
                'isArrayExist',
                ['test', self::DEFAULT_INI_PATH]
            )
        );
    }

    public function testArrayNonExisting()
    {
        $this->config->setArray('key');
        $this->assertFalse(
            $this->makeMethodAccessible(
                Config::class,
                'isArrayExist',
                ['foo', self::DEFAULT_INI_PATH]
            )
        );
    }

    public function testGetNullKey()
    {
        $this->assertNull($this->config->get());
    }

    public function testGetArray()
    {
        $this->config->setArray('test');
        $this->config->set('key', 'value');

        $this->assertTrue(is_array($this->config->getArray('test')));
    }

    public function testGetKeyOneParamArgument()
    {
        $this->expectException(ConfigException::class);
        $this->expectExceptionMessage('Key non_existing_key is not found.');

        $this->config->setArray('test');
        $this->config->set('key', 'value');
        $this->config->get('non_existing_key');
    }

    public function testGetNonExistingKey()
    {
        $this->expectException(ConfigException::class);
        $this->expectExceptionMessage('Called array test or key non_existing_key does not exist.');

        $this->config->setArray('test');
        $this->config->set('key', 'value');

        $this->assertEquals('some_value', $this->config->get('test.non_existing_key'));
    }

    public function testGetKey()
    {
        $this->config->setArray('test');
        $this->config->set('key', 'value');

        $this->assertEquals('value', $this->config->get('test.key'));
    }

    public function testGetNonExistingKeyInArray()
    {
        $this->expectException(ConfigException::class);
        $this->expectExceptionMessage('Called array test or key non_existing_key does not exist.');

        $this->config->setArray('test');
        $this->config->set('key', 'value');
        $this->config->get('test.non_existing_key');
    }

    public function makeMethodAccessible($object, $method, $args = [])
    {
        $reflector = new ReflectionMethod($object, $method);
        $reflector->setAccessible(true);
        return $reflector->invokeArgs($this->config, $args);
    }

    public function tearDown()
    {
        unset($this->config);

        if (file_exists(self::DEFAULT_INI_PATH)) {
            file_put_contents(self::DEFAULT_INI_PATH, '');
        } else {
            touch(self::DEFAULT_INI_PATH);
        }
    }
}
