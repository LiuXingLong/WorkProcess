<?php
namespace Apps\Api\Models;
use Apps\Common\Models\Model;

class ModelBase
{
    public $DB_OBJ;
    public function __construct()
    {
        $this->DB_OBJ = Model::getInstance(DB_CONFIG['user']);
        $tableName = '';
        $this->DB_OBJ->getSource($tableName);
    }
}