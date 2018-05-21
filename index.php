<?php

use Config\Core\Config;

ob_clean();

spl_autoload_register(function ($className) {
    # Usually I would just concatenate directly to $file variable below
    # this is just for easy viewing on Stack Overflow)
    $ds = DIRECTORY_SEPARATOR;
    $dir = __DIR__;
    // replace namespace separator with directory separator (prolly not required)
    $className = lcfirst(str_replace('\\', $ds, $className));
    // get full name of file containing the required class
    $file = "{$dir}{$ds}src/{$className}.php";
    // get file if it is readable
    if (is_readable($file)) {
        require_once $file;
    }
});

try {
    $logger = new \Config\Core\Logger();
    $logger->setPath('src/storage/logs/logger.log');

    $cfg = new Config('config.ini', $logger);
    $cfg->init();
    $cfg->setConfig('key', 'value');
} catch (Exception $e) {
    echo $e->getMessage();
}

ob_end_flush();
