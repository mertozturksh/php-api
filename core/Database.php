<?php

namespace Core;

use PDO;
use Exception;
use App\Models\BaseModel;

class Database {
    protected $db;

    public function __construct($configFile)
    {
        $config = parse_ini_file($configFile);
        $dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']};port={$config['port']}";
        
        try {
            $this->db = new PDO($dsn, $config['username'], $config['password']);
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (Exception $e) {
            die('Database connection failed: ' . $e->getMessage());
        }
    }

    public function get(BaseModel $model, $id)
    {
        $primaryKey = $model->getPrimaryKey();
        $tableName = $model->getTableName();

        $stmt = $this->db->prepare("SELECT * FROM $tableName WHERE $primaryKey = :id");
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            $model->fill($result);
            return $model;
        }
        return null;
    }

    public function getAll(BaseModel $model)
    {
        $tableName = $model->getTableName();
        $stmt = $this->db->query("SELECT * FROM $tableName");
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $models = [];
        foreach ($results as $result) {
            $instance = clone $model;
            $instance->fill($result);
            $models[] = $instance;
        }

        return $models;
    }

    public function insert(BaseModel $model)
    {
        $tableName = $model->getTableName();
        $data = $model->getFieldsData();
        $fields = array_keys($data);
        $placeholders = array_map(function ($field) {
            return ":$field";
        }, $fields);

        $sql = "INSERT INTO $tableName (" . implode(', ', $fields) . ") VALUES (" . implode(', ', $placeholders) . ")";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data);
    }

    public function update(BaseModel $model, $id)
    {
        $primaryKey = $model->getPrimaryKey();
        $tableName = $model->getTableName();
        $data = $model->getFieldsData();

        $fields = array_keys($data);
        $placeholders = implode(', ', array_map(function ($field) {
            return "$field = :$field";
        }, $fields));

        $sql = "UPDATE $tableName SET $placeholders WHERE $primaryKey = :id";
        $stmt = $this->db->prepare($sql);
        $data['id'] = $id;
        return $stmt->execute($data);
    }

    public function delete(BaseModel $model, $id)
    {
        $primaryKey = $model->getPrimaryKey();
        $tableName = $model->getTableName();

        $stmt = $this->db->prepare("DELETE FROM $tableName WHERE $primaryKey = :id");
        return $stmt->execute(['id' => $id]);
    }
}
