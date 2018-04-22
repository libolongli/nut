<?php
namespace app\Index\controller;

use think\Controller;
class Speech extends Controller {

    public function recive(){
        \think\Log::write(json_encode($_POST));
    }
}
