<?php
namespace app\common\model;
class User extends \think\Model{
    
    public function wallet(){
        return $this->hasOne("Wallet","userId",'id');
    }
}