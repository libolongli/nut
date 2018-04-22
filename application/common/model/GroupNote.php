<?php
namespace app\common\model;
class GroupNote extends \think\Model{
    public function group(){
        return $this->belongsTo('Group','id','groupId');
    }
}