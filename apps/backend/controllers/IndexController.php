<?php
namespace Apps\Backend\Controllers;
use Apps\Backend\Controllers\BaseController;
use Apps\Backend\Models\UserModel;
use Apps\Backend\Models\AdminModel;

class IndexController extends BaseController
{
    public function index()
    {
        $Admin = new AdminModel();
        $this->assign(['title' => '个人框架']);
        $this->show('index');
    }
    public function test()
    {
        echo json_encode(array('Backend: test','bbb'));
    }
}