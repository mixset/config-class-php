<?php


use Mixset\ConfigManager\Config;
use Mixset\ConfigManager\Exceptions\ConfigException;

ob_clean();

include '../src/Config.php';
include '../src/exceptions/ConfigException.php';

try {
    $cfg = new Config();
    $cfg->setPath('../src/config/config.ini');
    $cfg->init();

    $cfg->setArray('test'); // creates [test] array
    $cfg->getArray('test'); // returns test array

    $cfg->set('key', 'value'); // sets key = "value"
    $cfg->get('test.key'); // prints value
} catch (ConfigException $e) {
    echo $e->getMessage();
}

ob_end_flush();
