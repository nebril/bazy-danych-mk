<?php
class PutDbConnection extends CApplicationComponent {
	public $connectionString;
    public $emulatePrepare;
    public $username;
    public $password;
    public $charset;
    
    private $pdo;
    
    public function init() {
        $this->pdo = new PDO($this->connectionString, $this->username, $this->password);
    }
    
    public function __call($name, $params) {
        return call_user_func_array(array($this->pdo, $name), $params);
    }
}