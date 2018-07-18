<?php
namespace Apps\Api\Models;
use Apps\Api\Models\ModelBase;

class UserModel extends ModelBase
{
    /**
     * 重新定义表名
     */
    public function setSource(){
        return 'test';
    }
    public function getUser(){
        
        $this->DB_OBJ->insert();
        //var_dump($this->DB_OBJ);
    }
    public function setUser(){
        $rs = $this->DB_OBJ->delete(['conditions'=>'name = ?name and password = ?password','bind'=>['name'=>'tyler','password'=>666]]);
        var_dump($rs);
        //var_dump($this->DB_OBJ);
    }
}