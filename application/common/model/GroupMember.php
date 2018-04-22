<?php
namespace app\common\model;
class GroupMember extends \think\Model{
    protected $field = ['id','groupId','userId','markName','role','createTime','receiveTip','isAccept','bgurl'];

    public function group(){
        return $this->belongsTo('Group','id','groupId');
    }
}