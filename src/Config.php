<?php

namespace Mixset\ConfigManager;

use Mixset\ConfigManager\Exceptions\ConfigException;

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
    private $path;

    /**
     * Config file extension
     *
     * @var string
    */
    const CONFIG_FILE_EXTENSION = 'ini';

    /**
     * @param $path
    */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * @return mixed
    */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Basic Config settings
     *
     * @throws ConfigException
    */
    public function init()
    {
        $filePath = $this->getPath();

        if (empty($filePath)) {
            throw new ConfigException('File path is not specified.');
        }

        if (pathinfo($filePath, PATHINFO_EXTENSION) !== self::CONFIG_FILE_EXTENSION) {
            throw new ConfigException('File has to have extension ini.');
        }

        if (!file_exists($filePath)) {
            if (touch($filePath) === false) {
                throw new ConfigException('Error occurred during creating of file ' . $filePath);
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
    public function set($name, $value)
    {
        $file = $this->getPath();

        if ($this->isKeyExist($name, $file)) {
            throw new ConfigException('Given key ( ' . $name . ' ) already exists in a ' . $file);
        }

        $text = '';

        if (is_int($value) || is_float($value) || is_double($value)) {
            $text = $name . ' = ' . $value;
        } elseif (is_string($value)) {
            $text = $name . ' = ' . '"' . $value . '"';
        } elseif (is_bool($value)) {
            $text = $name . ' = ' . ($value ? '"true"' : '"false"');
        }

        if (file_put_contents($file, $text . PHP_EOL, FILE_APPEND) === false) {
            throw new ConfigException('There was a problem with adding new data to file.');
        }

        return true;
    }

    /**
     * @param $key
     * @param $file
     *
     * @return bool
    */
    private function isKeyExist($key, $file)
    {
        return array_key_exists(
            $key,
            parse_ini_file($file)
        );
    }

    /**
     * @param $arrayName
     * @return bool
     *
     * @throws \Exception
    */
    public function setArray($arrayName)
    {
        $path = $this->getPath();

        if ($this->isArrayExist($arrayName, $path) === true) {
            throw new ConfigException('Given array name ( ' . $arrayName . ' ) already exists in a ' . $path);
        }

        $arrayName = '[' . $arrayName . ']';

        $content = filesize($path) !== 0
            ? PHP_EOL . $arrayName
            : $arrayName;

        if (file_put_contents($path, $content . PHP_EOL, FILE_APPEND) === false) {
            throw new ConfigException('There was a problem with adding new data to file.');
        }

        return true;
    }

    /**
     * @param $arrayName
     * @return mixed|null
     * @throws ConfigException
    */
    public function getArray($arrayName)
    {
        if (empty($arrayName)) {
            return null;
        }

        $path = $this->getPath();
        $config = $this->parseIniFile($path, true);

        if ($this->isArrayExist($arrayName, $path)) {
            return $config[$arrayName];
        } else {
            throw new ConfigException('Called array or key: '. $arrayName . ' does not exist.');
        }
    }

    /**
     * @param $search
     * @param array $config
     *
     * @return mixed
     * @throws ConfigException
    */
    private function getWithoutArray($search, array $config, $path)
    {
        if ($this->isKeyExist($search, $path) === false) {
            throw new ConfigException('Key ' . $search . ' is not found.');
        }

        return $config[$search];
    }

    /**
     * @param $explode
     * @param array $config
     * @param $path
     *
     * @return mixed
     * @throws ConfigException
     */
    private function getWithArray($explode, array $config, $path)
    {
        list($arrayName, $key) = $explode;

        if ($this->isArrayExist($arrayName, $path) === false || $this->isKeyExist($key, $path) === false) {
            throw new ConfigException('Called array ' . $arrayName . ' or key '. $key . ' does not exist.');
        }

        return $config[$arrayName][$key];
    }

    /**
     * @param array $key
     * Methods retrieves data from config file
     *
     * @return array
     * @throws ConfigException
    */
    public function get($key = null)
    {
        if (empty($key)) {
            return null;
        }

        $path = $this->getPath();
        $config = $this->parseIniFile($path, true);

        $explode = explode('.', $key);
        $count = count($explode);

        if ($count === 1) {
            return $this->getWithoutArray($explode[0], $config, $path);
        }

        if ($count === 2) {
            return $this->getWithArray($explode, $config, $path);
        }
    }

    /**
     * @param $name
     * @param $path
     *
     * @return bool|null
     */
    private function isArrayExist($name, $path)
    {
        if (file_exists($path)) {
            $file = parse_ini_file($path, true);
            return isset($file[$name]);
        }

        return null;
    }

    /**
     * @param $path
     * @param bool $process_sections
     * @return array|bool
     * @throws ConfigException
    */
    private function parseIniFile($path, $process_sections = false)
    {
        if (!$config = parse_ini_file($path, $process_sections)) {
            throw new ConfigException('Unexpected error occurred while parsing a ini file.');
        }

        return $config;
    }
}
