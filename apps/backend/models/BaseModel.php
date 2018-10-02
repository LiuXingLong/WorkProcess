<?php
namespace Apps\Backend\Models;
use Process\Models\BaseModel as Model;

class BaseModel extends Model
{
    public function __construct(){
        parent::__construct(DB_CONFIG['admin']);
    }
}