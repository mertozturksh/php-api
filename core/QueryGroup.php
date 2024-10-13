<?php

namespace Core;

class QueryGroup
{
    protected $conditions = [];
    protected $connector;

    private static $allowedConnectors = ['AND', 'OR'];

    public function __construct($connector = 'AND')
    {
        $conn = strtoupper($connector);
        if (!in_array($conn, self::$allowedConnectors)) {
            throw new \InvalidArgumentException("Invalid connector: $connector");
        }
        $this->connector = $conn;
    }

    public function addCondition($condition)
    {
        if ($condition instanceof QueryOperator || $condition instanceof QueryGroup) {
            $this->conditions[] = $condition;
        } else {
            throw new \InvalidArgumentException("Condition must be QueryOperator or QueryGroup.");
        }
    }

    public function toSql()
    {
        if (empty($this->conditions)) {
            return '';
        }

        $sqlParts = [];
        foreach ($this->conditions as $condition) {
            $sqlParts[] = '(' . $condition->toSql() . ')';
        }
        return implode(" {$this->connector} ", $sqlParts);
    }

    public function getValues()
    {
        $values = [];
        foreach ($this->conditions as $condition) {
            $values = array_merge($values, $condition->getValues());
        }
        return $values;
    }

    public function getConnector()
    {
        return $this->connector;
    }
}
