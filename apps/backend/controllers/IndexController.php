<?php
namespace Apps\Backend\Controllers;
use Apps\Backend\Controllers\ControllerBase;

class IndexController extends ControllerBase
{
    public function index()
    {
        $this->assign(['title' => '个人框架']);
        $this->show('index');
    }
    public function test()
    {
        echo json_encode(array('Backend: test','bbb'));
    }
}