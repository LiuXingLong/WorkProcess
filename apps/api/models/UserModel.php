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
}