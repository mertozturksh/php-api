<?php

namespace App\Controllers;

class BaseController {
    public $sdkInstance;

    public function __construct($sdkInstance)
    {
        $this->sdkInstance = $sdkInstance;
    }

    public function sdk() {
        return $this->sdkInstance;
    }
}