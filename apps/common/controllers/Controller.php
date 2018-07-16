<?php
namespace Apps\Common\Controllers;
use Apps\Common\Template\View;
class Controller
{
    private $_ViewValues = [];
    public $param = PARAMS;
    public function __construct()
    {
    
    }
    public function assign($values){
        if(is_array($values)){
            $this->_ViewValues = $values;
        }else{
            throw new \Exception('控制器分配给视图的值必须为数组 array(key => val)');
        }
    }
    public function show($file){
        new View($file, $this->_ViewValues);
    }
}