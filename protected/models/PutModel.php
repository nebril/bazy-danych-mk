<?php
class PutModel {
    public $tableName;
    public $attributes = array();
    public $columns = array();
    
    protected function setColumns() {
	    $q = Yii::app()->db->prepare("DESCRIBE " . $this->tableName);
        $q->execute();
        $this->columns = $q->fetchAll(PDO::FETCH_COLUMN);
    }
    
    public function findById($id) {
        $class = get_class($this);
        $model = new $class();
        $q = Yii::app()->db->prepare('SELECT * FROM ' . $this->tableName . ' WHERE id=:id LIMIT 1');
        $q->bindParam(':id', $id);
        $q->execute();
	    foreach($q->fetchAll() as $row) {
	        foreach($row as $column => $value) {
	            if(!is_numeric($column)) {
	                $model->$column = $value;
	            }
	        }
	    }
	    return $model;
    }
    
    public function getAll() {
        $class = get_class($this);
        $models = array();
        $q = Yii::app()->db->prepare('SELECT * FROM ' . $this->tableName);
        $q->execute();
	    foreach($q->fetchAll() as $row) {
	        $model = new $class();
	        foreach($row as $column => $value) {
	            if(!is_numeric($column)) {
	                $model->$column = $value;
	            }
	        }
	        $models[$model->id] = $model;
	    }
	    return $models;
    }
    
    public function __get($column) {
        if(array_key_exists($column, $this->attributes)) {
            return $this->attributes[$column];
        }else {
            throw new Exception('Column not found:' . $column);
        }
    }
    
    public function __set($column, $value) {
        if(in_array($column, $this->columns)) {
            $this->attributes[$column] = $value;
        }else {
            throw new Exception('Column not found:' . $column);
        }
    }
    
    public function save() {
        $params = array();
        $id = $this->id;

        if(!empty($id)) {
            $sql = 'UPDATE ' . $this->tableName . ' SET ';
            foreach($this->attributes as $column => $value) {
                if($column == 'id'){
                    continue;
                }
                $sql .= $column . '=:' . $column . ', ';
                $params[':' . $column] = $value;
            }
            $sql = rtrim($sql, ', ');
            
            $sql .= ' WHERE id='.$id;
        }else {
            $sql = 'INSERT INTO ' . $this->tableName . ' (' . implode(',', array_keys($this->attributes)) . ') VALUES(';
            foreach($this->attributes as $column => $value) {
                $sql .= ':' . $column . ', ';
                $params[':' . $column] = $value;
            }
            $sql = rtrim($sql, ', ');
            $sql .= ')';
        }
        
        $q = Yii::app()->db->prepare($sql);
        foreach($params as $param => $value){
            $q->bindParam($param, $value);
        }
        
        return $q->execute();
    }
    
    public function delete() {
        $id = $this->id;

        if(!empty($id)) {
            $q = Yii::app()->db->prepare('DELETE FROM ' . $this->tableName . ' WHERE id=:id');
            $q->bindParam(':id', $id);
            return $q->execute();
        }
        return false;
    }
    
    public static function getArrayFromObjects($objectsArray) {
        $response = array();
        foreach($objectsArray as $object) {
            $row = array();
            foreach($object->columns as $column) {
                $row[$column] = $object->$column;
            }
            $response[] = $row;
        }
        return $response;
    }
}