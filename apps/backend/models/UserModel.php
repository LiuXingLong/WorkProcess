<?php
namespace Apps\Backend\Models;
use Apps\Backend\Models\ModelBase;

class UserModel extends ModelBase
{
    /**
     * 重新定义表名
     */
//     public function setSource(){
//         return 'table_name';
//     }
    public function getUser(){
        var_dump($this->DB_OBJ);
    }
}