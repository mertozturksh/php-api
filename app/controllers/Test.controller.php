<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Enums\OutputEngineEnum;

class TestController extends BaseController
{

    public function test($variables)
    {
        $variables['filters'] = json_decode($variables['filters']);
        return OutputEngineEnum::getKeys();
        return $variables;
    }

}
