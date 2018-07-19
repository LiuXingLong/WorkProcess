<?php
namespace Apps\Api\Models;
use Apps\Api\Models\BaseModel;

class UserModel extends BaseModel
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
        $rs = $this->DB_OBJ->find(['columns'=>'id,name','conditions'=>'name=?1 and password=?2 limit 2','bind'=>[1=>'tyler.liu',2=>'1234']]);
        var_dump($rs);
        //var_dump($this->DB_OBJ);
    }
}