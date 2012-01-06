<?php
class Club extends PutModel {
    public function __construct() {
        $this->tableName = 'club';
        $this->setColumns();
    }
    
    public static function model() {
        return new Club();
    }
}