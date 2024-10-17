<?php

namespace App\Models;

use Core\QueryGroup;
use Core\QueryOperator;

class BaseModel
{

    protected $tableName;
    protected $primaryKey;
    protected $queryOptions = [
        'columns' => ['*'],     // [selected columns]
        'join' => null,         // [join expressions]
        'where' => [],          // [conditions]
        'limit' => null,        // [offset, limit]
        'orderBy' => null,      // [orderBy fields]
        'groupBy' => null,      // [groupBy fields]
    ];

    public function getTableName()
    {
        return $this->tableName;
    }
    public function getPrimaryKey()
    {
        return $this->primaryKey;
    }

    public function getFieldsData()
    {
        $data = [];
        $properties = get_object_vars($this);

        foreach ($properties as $field => $value) {
            if ($field !== 'tableName' && $field !== 'primaryKey' && $field !== 'queryOptions') {
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



    public function setColumns(array $columns)
    {
        if (empty($columns)) {
            $this->queryOptions['columns'] = ['*'];
        } else {
            $this->queryOptions['columns'] = $columns;
        }
    }
    public function setJoin($type, $table, $on)
    {
        if (!in_array(strtoupper($type), ['INNER', 'LEFT', 'RIGHT', 'FULL'])) {
            throw new \InvalidArgumentException("Invalid join type: $type");
        }

        $this->queryOptions['joins'][] = strtoupper($type) . " JOIN $table ON $on";
    }
    public function setWhere($condition)
    {
        if ($condition instanceof QueryOperator || $condition instanceof QueryGroup) {
            $this->queryOptions['where'][] = $condition;
        } else {
            throw new \InvalidArgumentException("Condition must be QueryOperator or QueryGroup.");
        }
    }
    public function setLimit(int $offset, int $limit)
    {
        $this->queryOptions['limit'] = [$offset, $limit];
    }
    public function setOrderBy($field, $direction = 'ASC')
    {
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $field)) {
            throw new \InvalidArgumentException("Invalid field name for order by: $field");
        }
        $direction = strtoupper($direction);
        if (!in_array($direction, ['ASC', 'DESC'])) {
            throw new \InvalidArgumentException("Invalid order direction: $direction");
        }
        $this->queryOptions['orderBy'] = "$field $direction";
    }
    public function setGroupBy($field)
    {
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $field)) {
            throw new \InvalidArgumentException("Invalid field name for group by: $field");
        }
        $this->queryOptions['groupBy'] = $field;
    }
    public function getQueryOptions()
    {
        return $this->queryOptions;
    }
    public function resetQueryOptions()
    {
        $this->queryOptions = [
            'where' => [],
            'limit' => null,
            'orderBy' => null,
            'groupBy' => null,
        ];
    }

    public function getWhereClause()
    {
        if (empty($this->queryOptions['where'])) {
            return ['sql' => '', 'params' => []];
        }

        $whereSql = '';
        $params = [];

        foreach ($this->queryOptions['where'] as $index => $condition) {
            /** @var QueryOperator|QueryGroup $condition */
            $sqlPart = $condition->toSql();
            $values = $condition->getValues();

            if ($index > 0 && $condition instanceof QueryGroup) {
                $whereSql .= ' ' . $condition->getConnector() . ' ';
            } elseif ($index > 0) {
                $whereSql .= ' AND ';
            }
            $whereSql .= $sqlPart;
            $params = array_merge($params, $values);
        }
        return ['sql' => $whereSql, 'params' => $params];
    }
    public function getJoinClause()
    {
        return implode(' ', $this->queryOptions['join']);
    }
}
