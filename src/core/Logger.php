<?php

namespace Core;

use Exceptions\LoggerException;

class Logger
{
    protected $path;

    /**
     * Set path to log file
     *
     * @param $path
    */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * Get path to log file
     *
     * @return mixed
    */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @return bool
     * @throws LoggerException
    */
    protected function checkConfigFile()
    {
        $path = $this->getPath();

        if (!file_exists($path)) {
            throw new LoggerException('File ' . $path . ' does not exist.');
        }

        $handle = fopen($path, 'a+');

        if (!$handle) {
            throw new LoggerException('File ' . $path . ' cannot be open successfully.');
        }

        return true;
    }

    /**
     * Save log text to file
     *
     * @param $message
     * @param $value
     * @return bool
    */
    public function log($message, $value)
    {
        $this->checkConfigFile();

        $ip = Helpers::getIP();
        $date = Helpers::getDate();

        $log = '[ ' . $date . ' ] ' . $message . ' | ' . $value . ' | ' . $ip;

        return file_put_contents($this->getPath(), $log . PHP_EOL, FILE_APPEND) !== false;
    }
}
