<?php
namespace Apps\Backend\Controllers;
use Apps\Backend\Controllers\BaseController;
use Apps\Backend\Models\UserModel;

class IndexController extends BaseController
{
    public function index()
    {
        $User = new UserModel();
        $User->getUser();
        die;
        
        $this->assign(['title' => '个人框架']);
        $this->show('index');
    }
    public function test()
    {
        echo json_encode(array('Backend: test','bbb'));
    }
}