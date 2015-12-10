<?php
/**
@Author: Dominik Ryñko
@Website: http://www.rynko.pl/
@Version: 1.0
@Contact: http://www.rynko.pl/kontakt
 */

/**
 * Class config
 */
class Config
{
    /**
     * @var
     */
    private $file;

    /**
     * @var array
     */
    private $config = [
        'fileLog' => 'logger.log',
        'logger' => false, // true -> save events to logger.log, false -> don't save events to logger.log
        'extension' => 'ini'
    ];

    /**
     * @param $file
     */
    public function __construct($file)
    {
        $this -> file = $file;
    }

    /**
     * @throws Exception
    */
    public function init()
    {
        if (pathinfo($this -> file, PATHINFO_EXTENSION) !== $this -> config['extension']) {
            throw new Exception('File has to have extension ini.');
        }

        if (!file_exists($this -> file)) {
            if(!fopen($this -> file, "a+")) {
                throw new Exception('Automatic creating of file' . $this -> file);
            } else {
                throw new Exception('There was a problem with creating file:' . $this -> file);
            }
        }
        elseif(!is_file($this -> file)) {
            throw new Exception($this -> file . ' is not a file.');
        }
    }

    /**
     * @param $name
     * @param $value
     * @return bool|null
     * @throws Exception
    */
    public function setConfig($name, $value)
    {
        if ($this -> isKeyExist($name, $this -> file)) {
            throw new Exception('Given key ( ' . $name . ' ) already exists in a ' . $this -> file);
        } else {
            $text = '';

            if (is_int($value) || is_float($value) || is_double($value)) {
                $text = $name . ' = ' . $value;
            }
            elseif (is_string($value)) {
                $text = $name . ' = ' . '"' . $value . '"';
            }
            elseif (is_bool($value)) {
                $text = $name . ' = ' . ($value == true) ? "true" : "false";
            }

            if (!file_put_contents($this -> file, $text . PHP_EOL, FILE_APPEND)) {
                throw new Exception('There was a problem with adding new data to file.');
            } else {
                return $this -> config['logger'] == true ? $this -> addLog('New data has been added to file', trim($text)) : null;
            }
        }
    }

    /**
     * @param array $what
     * @return array
     * @throws Exception
    */
    public function getConfig($what = array())
    {
        if (filesize($this -> file) == 0) {
            throw new Exception('File ' . $this -> file . ' can not be empty.');
        }

        if (!$config = parse_ini_file($this -> file)) {
            throw new Exception('There was a problem with parsing a ini file');
        }

        $data = [];

        foreach($what as $key)
        {
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
        $config = parse_ini_file($file);

        return array_key_exists($key, $config) ? true : false;

    }

    /**
     * @param $message
     * @param $value
     * @return bool
    */
    private function addLog($message, $value)
    {
        if(!file_exists($this -> config['fileLog']))
        {
            $handle = fopen($this -> config['fileLog'], 'a+');

            if (!$handle) {
                return false;
            }
        }

        $ip    = $_SERVER['REMOTE_ADDR'];
        $date  = date('d-m-Y, H:i:s');

        $log =  '[ '.$date.' ] '.$message.' | '.$value.' | '.$ip;
        file_put_contents($this -> config['fileLog'], $log.PHP_EOL, FILE_APPEND);
        return true;
    }
}
