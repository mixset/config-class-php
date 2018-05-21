<?php

namespace Config\Core;

use Config\Exceptions\ConfigException;

/**
 * Class Config
 * @package Config\Core
*/
class Config
{
    /**
     * Name of config file
     *
     * @var
    */
    private $file;

    /**
     * Config file extension
     *
     * @var string
    */
    protected $fileExt = '.ini';

    /**
     * Logger instance
     *
     * @var Logger
    */
    protected $logger;

    /**
     * @param $file
     * @param Logger $logger
    */
    public function __construct($file, Logger $logger)
    {
        $this->file = $file;
        $this->logger = $logger;
    }

    /**
     * Basic Config settings
     *
     * @throws ConfigException
    */
    public function init()
    {
        $this->file = 'src/config' . $this->file;

        if (pathinfo($this->file, PATHINFO_EXTENSION) !== $this->fileExt) {
            throw new ConfigException('File has to have extension ini.');
        }

        if (!file_exists($this->file)) {
            if (touch($this->file) === false) {
                throw new ConfigException('Error occurred during creating of file ' . $this->file);
            }
        }
    }

    /**
     * Set new key and value in config file
     *
     * @param $name
     * @param $value
     * @return bool|null
     * @throws ConfigException
    */
    public function setConfig($name, $value)
    {
        if ($this->isKeyExist($name, $this->file)) {
            throw new ConfigException('Given key ( ' . $name . ' ) already exists in a ' . $this->file);
        }

        $text = '';

        if (is_int($value) || is_float($value) || is_double($value)) {
            $text = $name . ' = ' . $value;
        } elseif (is_string($value)) {
            $text = $name . ' = ' . '"' . $value . '"';
        } elseif (is_bool($value)) {
            $text = $name . ' = ' . ($value == true) ? "true" : "false";
        }

        if (file_put_contents($this->file, $text . PHP_EOL, FILE_APPEND) === false) {
            throw new ConfigException('There was a problem with adding new data to file.');
        } else {
            $this->logger->log('New data has been added to file', trim($text));
        }
    }

    /**
     * @param array $search
     *
     * @return array
     * @throws ConfigException
    */
    public function getConfig($search = [])
    {
        if (!$config = parse_ini_file($this->file)) {
            throw new ConfigException('There was a problem with parsing a ini file');
        }

        $data = [];

        foreach ($search as $key) {
            $data[] = $config[$key];
        }

        return $data;
    }

    /**
     * @param $key
     * @param $file
     * @return bool
    */
    private function isKeyExist($key, $file)
    {
        return array_key_exists(
            $key,
            parse_ini_file($file)
        );
    }
}
