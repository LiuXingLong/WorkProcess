<?php
namespace Apps\Api\Controllers;
use Apps\Api\Controllers\BaseController;
use Apps\Api\Models\UserModel;

class IndexController extends BaseController
{
    public function index()
    {       
        $User = new UserModel();
        $this->assign(['title' => '个人框架']);
        $this->show('index');
    }
    public function test()
    {
        echo json_encode(array('Api: test','aaa'));
    }
}