<?php
class Payment extends PutModel {
    public function __construct() {
        $this->tableName = 'payments';
        $this->setColumns();
    }
    
    public static function model() {
        return new Payment();
    }
}