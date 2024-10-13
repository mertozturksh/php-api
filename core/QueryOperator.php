<?php

namespace Core;

class QueryOperator
{
    protected $field;
    protected $operator;
    protected $value;

    private static $allowedOperators = [
        '>',            // greater than
        '>=',           // greater than or equal
        '<',            // less than
        '<=',           // less than or equal
        '=',            // equal
        '!=',           // not equal
        '<=>',          // null-safe equal
        'IN',           // whether a value is within a set of values
        'NOT IN',       // whether a value is not within a set of values
        'IS',           // Test a value against a boolean (NULL, NOT NULL)
        'LIKE',         // pattern matching
        'NOT LIKE',     // negation of pattern matching
        'BETWEEN',      // range (ex. 1 to 1000)
    ];

    public function __construct($field, $operator, $value)
    {
        $op = strtoupper($operator);
        if (!in_array($op, self::$allowedOperators)) {
            throw new \InvalidArgumentException("Invalid operator: $operator");
        }

        $this->field = $field;
        $this->operator = $op;
        $this->value = $value;
    }

    public function toSql()
    {
        if (in_array($this->operator, ['IN', 'NOT IN']) && is_array($this->value)) {
            $placeholders = implode(', ', array_fill(0, count($this->value), '?'));
            return "$this->field $this->operator ($placeholders)";
        } elseif ($this->operator === 'BETWEEN' && is_array($this->value)) {
            return "$this->field BETWEEN ? AND ?";
        } elseif ($this->operator === 'IS' && in_array(strtoupper($this->value), ['NULL', 'NOT NULL'])) {
            return "$this->field IS " . strtoupper($this->value);
        } else {
            return "$this->field $this->operator ?";
        }
    }

    public function getValues()
    {
        if (in_array($this->operator, ['IN', 'NOT IN', 'BETWEEN'])) {
            return is_array($this->value) ? $this->value : [$this->value];
        }

        if ($this->operator === 'IS' && in_array(strtoupper($this->value), ['NULL', 'NOT NULL'])) {
            return [];
        }

        return [$this->value];
    }
}
