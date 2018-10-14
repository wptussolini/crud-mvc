<?php

namespace App\Models;

use App\src\Connection;

class Model
{
    public function __construct()
    {
        if (!isset($this->id)) {
            foreach ($this->fillable as $key => $value) {
                $this->$value = '';
            }
        }
        unset($this->fillable);
    }

    /**
     * @return array|bool
     */
    public static function all()
    {
        $table = self::getStaticTable();
        $query = "SELECT * FROM {$table}";
        $stmt = Connection::getInstance()->prepare($query);
        $stmt->execute();
        if ($stmt->execute()) {
            return $stmt->fetchAll(\PDO::FETCH_CLASS, get_called_class());
        }
        return false;
    }

    /**
     * @param  $id
     * @return bool|mixed
     */
    public static function find($id)
    {
        $table = self::getStaticTable();
        $query = "SELECT * FROM {$table} WHERE id=:id";
        $stmt = Connection::getInstance()->prepare($query);
        $stmt->bindValue('id', $id);
        if ($stmt->execute()) {
            if ($stmt->rowCount() > 0) {
                $result = $stmt->fetchObject(get_called_class());
                if ($result) {
                    return $result;
                }
            }
        }
        return false;
    }

    /**
     * @param  array $params
     * @return bool|mixed
     */
    public static function select(array $params)
    {
        $table = self::getStaticTable();
        $query = "SELECT * FROM {$table} WHERE";
        $i = 1;
        foreach ($params as $key => $value) {
            $ot = $i > 1 ? ' AND ' : ' ';
            $query .= $ot . $key . '=:' . $key;
            $i++;
        }
        $stmt = Connection::getInstance()->prepare($query);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        if ($stmt->execute()) {
            if ($stmt->rowCount() > 0) {
                $result = $stmt->fetchObject(get_called_class());
                if ($result) {
                    return $result;
                }
            }
        }
        return false;
    }

    /**
     * @return $this
     * @throws \Exception
     */
    public function save()
    {
        if (!empty($this->id)) {
            $this->update();
            return $this;
        }
        $this->insert();
        return $this;
    }

    /**
     * @return $this
     */
    public function insert()
    {
        $fields = [];
        $fieldsToBind = [];
        foreach ($this as $key => $value) {
            $fields[] = $key;
            $fieldsToBind[] = ':' . $key;
        }
        $fields = implode(', ', $fields);
        $fieldsToBind = implode(', ', $fieldsToBind);
        $query = "INSERT INTO {$this->getTable()} ({$fields}) VALUES ({$fieldsToBind})";
        $stmt = Connection::getInstance()->prepare($query);
        foreach ($this as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        $this->id = Connection::getInstance()->lastInsertId();
        return $this;
    }

    /**
     * @return $this
     */
    public function update()
    {
        $fields = [];
        foreach ($this as $key => $value) {
            $fields[] = $key.'=:'.$key;
        }
        $fields = implode(', ', $fields);
        $query = "UPDATE {$this->getTable()} SET {$fields} WHERE id=:id";
        $stmt = Connection::getInstance()->prepare($query);
        foreach ($this as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        return $this;
    }

    /**
     * @return bool
     */
    public function delete()
    {
        $query = "DELETE FROM {$this->getTable()} WHERE id={$this->id}";
        $stmt = Connection::getInstance()->prepare($query);
        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    /**
     * @param  $model
     * @param  null  $foreign_key
     * @return array|bool
     */
    public function hasOne($model, $foreign_key = null)
    {
        $table = (new $model)->getTable();
        $query = "SELECT * FROM {$table} WHERE {$foreign_key} = '{$this->id}'";
        $stmt = Connection::getInstance()->prepare($query);
        if ($stmt->execute()) {
            if ($stmt->rowCount() > 0) {
                $result = $stmt->fetchAll(\PDO::FETCH_CLASS, get_called_class());
                if ($result) {
                    return $result;
                }
            }
        }
        return false;
    }

    /**
     * @return array|mixed|string
     */
    public function getTable()
    {
        $model = explode('\\', get_called_class());
        $model = array_pop($model);
        $model = strtolower($model .= 's');
        return $model;
    }

    /**
     * @return array|mixed|string
     */
    public static function getStaticTable()
    {
        $model = explode('\\', get_called_class());
        $model = array_pop($model);
        $model = strtolower($model .= 's');
        return $model;
    }
}