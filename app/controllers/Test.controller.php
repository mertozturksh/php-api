<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Enums\OutputEngine;

class TestController extends BaseController
{

    public function test($variables)
    {
        $variables['filters'] = json_decode($variables['filters']);
        return OutputEngine::getKeys();
        return $variables;
    }

}
