<?php
namespace app\index\controller;
use Think\Controller;
use app\index\controller\Common;
class Index extends Common
{
    public function index()
    {
        if (!(session('member')['id'])){
            $this->redirect('/login');
        }
        return view('Index/index');
    }
}
