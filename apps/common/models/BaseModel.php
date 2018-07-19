<?php
namespace Apps\Common\Models;
use Apps\Common\Models\Model;

class BaseModel
{
    private $DB_OBJ;    // 必须设置私有    子类Model操作时,每次都必须调用此变量操作,已做到每次设置表
    private $TableName; // 必须设置私有
    public function __get($name) {
        $this->DB_OBJ->setSource($this->TableName);
        return $this->$name;
    }    
    public function __construct($dbConfig)
    {
        $this->DB_OBJ = Model::getInstance($dbConfig);
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