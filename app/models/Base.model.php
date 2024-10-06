<?php

namespace App\Models;

class BaseModel
{

    protected $tableName;
    protected $primaryKey;

    public function getFieldsData()
    {
        $data = [];
        $properties = get_object_vars($this);
        
        foreach ($properties as $field => $value) {
            if ($field !== 'tableName' && $field !== 'primaryKey') {
                $data[$field] = $value;
            }
        }

        return $data;
    }
    public function fill(array $data)
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }

    public function getTableName()
    {
        return $this->tableName;
    }
    public function getPrimaryKey()
    {
        return $this->primaryKey;
    }
}
