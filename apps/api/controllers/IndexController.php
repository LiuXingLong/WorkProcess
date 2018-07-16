<?php
namespace Apps\Api\Controllers;
use Apps\Api\Controllers\ControllerBase;
use Apps\Common\Models\Model;

class IndexController extends ControllerBase
{
    public function index()
    {        
        $this->assign(['title' => '个人框架']);
        $this->show('index');
    }
    public function test()
    {
        echo json_encode(array('Api: test','aaa'));
    }
}