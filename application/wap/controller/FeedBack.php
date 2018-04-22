<?php
namespace app\wap\controller;
use app\common\controller\Base;
class FeedBack extends Base{

    protected function _initialize(){
        parent::_initialize();
    }

    public function index(){
        return $this->fetch('mobile/feedback/index');
    }
}