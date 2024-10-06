<?php

namespace App\Models;

class CarModel extends BaseModel {

    public $id;
    public $brand;
    public $model;
    public $year;
    public $color;
    public $price;
    public $created_at;
    public $updated_at;


    protected $tableName = "cars";
    protected $primaryKey = "id";
}