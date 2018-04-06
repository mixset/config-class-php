<?php

namespace Core;

class Engine
{
    public function __construct()
    {
        $this->security();
    }

    /**
     * Set security env
    */
    private function security()
    {
        // Set error reporting
        error_reporting(E_ALL ^ E_NOTICE);

        // Error display_error value
        ini_set('display_error', 1);
    }
}