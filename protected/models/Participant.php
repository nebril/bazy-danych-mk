<?php
class Participant extends PutModel {
    public function __construct() {
        $this->tableName = 'participant';
        $this->setColumns();
    }
    
    public static function model() {
        return new Participant();
    }
}