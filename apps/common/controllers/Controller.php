<?php
namespace Apps\Common\Controllers;

class Controller
{
    private $_ViewValues;
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
        $FilePath = VIEW_PATH.DS.'backend'.DS.$file.'.html';
        $ViewValue = $this->_ViewValues;
        if (!is_file($FilePath)) {
            throw new \Exception('模板文件'.$file .'不存在!');
        }
        include($FilePath);
    }
}