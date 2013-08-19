<?php

namespace Kernel;

abstract class Model
{
    public $errors = [];

    public function hasErrors()
    {
        return count($this->errors) > 0;
    }

    public static function getColumns()
    {
        $class = get_called_class();
        return $class::$columns;
    }

    public static function getTableName()
    {
        $class = get_called_class();
        return $class::$tablename;
    }

    public function create()
    {
        $columns = [];
        $values = [];
        $params = [];
        foreach (self::getColumns() as $column) {
            if ($column == 'id' && empty($this->column))
                continue;
            $columns[] = $column;
            $values[] = '?';
            $params[] = $this->$column;
        }

        $query = \Kernel\App::db()->prepare(
            'INSERT INTO ' . self::getTableName() .
            ' (' . implode(',', $columns) . ')' .
            ' VALUES (' . implode(',', $values) . ')'
        );
        $query->execute($params);
        $this->id = \Kernel\App::db()->lastInsertId();
    }

    public function update($attributes)
    {
        $set = [];
        $params = ['id' => $this->id];
        foreach ($attributes as $attribute) {
            $set[] = "$attribute = :$attribute";
            $params[":$attribute"] = $this->$attribute;
        }
        $sql = "UPDATE " . self::getTableName() . " set " . implode(", ", $set) . " WHERE id = :id";
        $query = \Kernel\App::db()->prepare($sql);
        $query->execute($params);
    }

    public static function findAll($attributes = [], $suffix = '')
    {
        $where = [];
        $params = [];
        foreach ($attributes as $key => $value) {
            $where[] = "$key = :$key";
            $params[":$key"] = $value;
        }

        $sql = 'SELECT * FROM ' . self::getTableName();
        if (count($attributes))
            $sql .= ' WHERE ' . implode(' AND ', $where);
        $sql .= " $suffix";
        $query = \Kernel\App::db()->prepare($sql);
        $query->execute($params);

        $result = [];
        while ($row = $query->fetch(\PDO::FETCH_ASSOC)) {
            $class = get_called_class();
            $instance = new $class();
            foreach ($row as $key => $value)
                $instance->$key = $value;
            $result[] = $instance;
        }
        return $result;
    }

    public static function findOne($attributes)
    {
        $result = self::findAll($attributes, 'LIMIT 1');
        return count($result) ? $result[0] : null;
    }
}