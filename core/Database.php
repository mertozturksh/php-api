<?php

namespace Core;

use App\Enums\AggregateFunctionEnum;
use App\Models\BaseModel;
use PDO;
use Exception;

class Database
{
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
    public function disconnect()
    {
        $this->db = null;
    }

    public function beginTransaction()
    {
        $this->db->beginTransaction();
    }
    public function commit()
    {
        $this->db->commit();
    }
    public function rollBack()
    {
        $this->db->rollBack();
    }


    public function select(BaseModel $model)
    {

        $tableName = $model->getTableName();
        $queryOptions = $model->getQueryOptions();

        // set columns
        $columns = implode(', ', $queryOptions['columns']);

        // create sql query
        $sql = "SELECT $columns FROM $tableName";

        // WHERE conditions
        $whereClause = $model->getWhereClause();
        if (!empty($whereClause['sql'])) {
            $sql .= " WHERE " . $whereClause['sql'];
        }

        // Group By
        if (!empty($queryOptions['groupBy'])) {
            $sql .= " GROUP BY " . $queryOptions['groupBy'];
        }

        // Order By
        if (!empty($queryOptions['orderBy'])) {
            $sql .= " ORDER BY " . $queryOptions['orderBy'];
        }

        // Limit
        if (!empty($queryOptions['limit'])) {
            $sql .= " LIMIT " . implode(', ', $queryOptions['limit']);
        }

        try {
            // prepare the query and execute
            $stmt = $this->db->prepare($sql);
            $stmt->execute($whereClause['params']);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // return data
            $models = [];
            foreach ($results as $result) {
                $instance = clone $model;
                $instance->resetQueryOptions();
                $instance->fill($result);
                $models[] = $instance;
            }
            return $models;
        } catch (Exception $e) {
            throw new Exception("Select query failed: " . $e->getMessage());
            return [];
        }
    }
    public function insert(BaseModel $model)
    {
        $tableName = $model->getTableName();
        $data = $model->getFieldsData();

        // exclude primary key field
        if (array_key_exists($model->getPrimaryKey(), $data)) {
            unset($data[$model->getPrimaryKey()]);
        }

        // exclude null fields
        $data = array_filter($data, function ($value) {
            return !is_null($value);
        });

        $fields = array_keys($data);
        $placeholders = array_map(function ($field) {
            return ":$field";
        }, $fields);

        $sql = "INSERT INTO $tableName (" . implode(', ', $fields) . ") VALUES (" . implode(', ', $placeholders) . ")";
        try {
            $stmt = $this->db->prepare($sql);
            $success = $stmt->execute($data);
            return $success;
        } catch (Exception $e) {
            throw new Exception("Insert query failed: " . $e->getMessage());
            return false;
        }
    }
    public function update(BaseModel $model)    // TODO FIXIT
    {
        $tableName = $model->getTableName();
        $data = $model->getFieldsData();

        // exclude NULL values
        $data = array_filter($data, function ($value) {
            return !is_null($value);
        });

        // fields
        $fields = array_keys($data);
        $placeholders = implode(', ', array_map(function ($field) {
            return "$field = :$field";
        }, $fields));

        // SQL query
        $sql = "UPDATE $tableName SET $placeholders";

        // WHERE conditions
        $whereClause = $model->getWhereClause();
        if (!empty($whereClause['sql'])) {
            $sql .= " WHERE " . $whereClause['sql'];
        } else {
            throw new Exception("Update operation must have WHERE conditions.");
        }

        $params = array_merge($data, $whereClause['params']);

        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute($params);
        } catch (Exception $e) {
            throw new Exception("Update query failed: " . $e->getMessage());
        }
    }
    public function delete(BaseModel $model)
    {
        $tableName = $model->getTableName();

        // SQL query
        $sql = "DELETE FROM $tableName";

        // WHERE conditions
        $whereClause = $model->getWhereClause();
        if (!empty($whereClause['sql'])) {
            $sql .= " WHERE " . $whereClause['sql'];
        } else {
            throw new Exception("Delete operation must have WHERE conditions.");
        }

        try {
            $stmt = $this->db->prepare($sql);
            $success = $stmt->execute($whereClause['params']);
            return $success;
        } catch (Exception $e) {
            throw new Exception("Delete query failed: " . $e->getMessage());
            return false;
        }
    }
    public function exists(BaseModel $model)
    {
        $tableName = $model->getTableName();
        $queryOptions = $model->getQueryOptions();

        $sql = "SELECT 1 FROM $tableName";

        // WHERE conditions
        $whereClause = $model->getWhereClause();
        if (!empty($whereClause['sql'])) {
            $sql .= " WHERE " . $whereClause['sql'];
        }
        $sql .= " LIMIT 1";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($whereClause['params']);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            return $result !== false;
        } catch (Exception $e) {
            throw new Exception("Exists query failed: " . $e->getMessage());
            return false;
        }
    }
    public function aggregate(BaseModel $model, string $function, string $field)
    {
        $tableName = $model->getTableName();
        $queryOptions = $model->getQueryOptions();

        // supported functions
        if (!AggregateFunctionEnum::isValidKey($function)) {
            throw new Exception("Unsupported aggregate function: $function");
        }

        $sql = "SELECT $function($field) as aggregate_value FROM $tableName";

        // WHERE conditions
        $whereClause = $model->getWhereClause();
        if (!empty($whereClause['sql'])) {
            $sql .= " WHERE " . $whereClause['sql'];
        }

        // Group By (optional)
        if (!empty($queryOptions['groupBy'])) {
            $sql .= " GROUP BY " . $queryOptions['groupBy'];
        }

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($whereClause['params']);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            return $result ? $result['aggregate_value'] : null;
        } catch (Exception $e) {
            throw new Exception("Aggregate query failed: " . $e->getMessage());
            return null;
        }
    }
}
