<?php
namespace app\common\model;
class Group extends \think\Model{
    protected $field = ['id','name','descriptions','detail','createrId','createTime','headUrl'];

    public function member(){
        return $this->hasMany('GroupMember','id','id');
    }

    public function note(){
        return $this->hasMany('GroupNote','id','id');
    }

    public function getNameAttr($value){
        return empty($value)?'':$value;
    }

    public function getDescriptionsAttr($value){
        return empty($value)?'':$value;
    }

    public function getDetailAttr($value){
        return empty($value)?'':$value;
    }

    public function getCreaterIdAttr($value){
        return empty($value)?'':$value;
    }

    public function getHeadUrlAttr($value){
        return empty($value)?'':$value;
    }

    public function setHeadUrlAttr($value){
        return empty($value)?'':$value;
    }


}