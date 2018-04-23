<?php
namespace app\home\controller;
use think\Controller;
class Index extends \think\Controller{
    
    function __construct(){
        parent::__construct();
    }
    
    /*小程序过渡404页面*/
    public function program_404(){
        exit('敬请期待！');
    }
}