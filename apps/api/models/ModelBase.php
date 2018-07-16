<?php
namespace Apps\Api\Models;
use Apps\Common\Models\Model;

class ModelBase
{
    private $DB_OBJ;    // 必须设置私有
    private $TableName; // 必须设置私有
    public function __get($name) {
        $this->DB_OBJ->setSource($this->TableName);
        return $this->$name;
    }
    public function __construct()
    {
        $this->DB_OBJ = Model::getInstance(DB_CONFIG['user']);
        $this->TableName = $this->setSource();
    }
    // 必须设置保护,子类可重置此方法来设置表名
    protected function setSource(){
        $table = explode('\\',get_class($this));
        $name = $table[count($table)-1];
        $tableName = preg_replace_callback('/([A-Z]{1})/',function($matches){
            return '_'.strtolower($matches[0]);
        },$name);
        return substr($tableName, 1 , -6);
    }
}