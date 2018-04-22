<?php
namespace app\home\controller;
use think\Controller;
class Apps extends \think\Controller{
    
    function __construct(){
        parent::__construct();
    }
    
    protected function displayError(){
        return view("public/error");
    }
}