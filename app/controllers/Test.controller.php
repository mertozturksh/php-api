<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class TestController extends BaseController
{

    public function test($variables)
    {
        $variables['filters'] = json_decode($variables['filters']);
        return $variables;
    }

}
