<?php

namespace Mixset\ConfigManager\Tests;

use Mixset\ConfigManager\Exceptions\ConfigException;

require_once 'src/exceptions/ConfigException.php';

class ExceptionTest extends \PHPUnit_Framework_TestCase
{
    private $exception;

    public function setUp()
    {
        $this->exception = new ConfigException();
    }

    public function testCorrectExtend()
    {
        $this->assertTrue(is_subclass_of($this->exception, 'Exception'));
    }
}
