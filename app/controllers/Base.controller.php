<?php

namespace App\Controllers;
use App\Core\Database;

class BaseController {
    protected $db;

    public function __construct()
    {
        $this->db = new Database(__DIR__ . '/../../config/database.ini');
    }
}