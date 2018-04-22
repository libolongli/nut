<?php
namespace app\wap\controller;
use app\common\controller\Base;
use app\common\model\Group as GroupMod;
use app\common\util\BaseUtil;
class Group extends Base{

    private $GroupMod;

    protected function _initialize(){
        parent::_initialize();
        // 非post用户不给访问
//         if (!request()->isPost()){
//             BaseUtil::createSimpleSuccess('服务器异常');
//         }
        $this->GroupMod=new GroupMod();
    }


    public function report($groupId="0",$userId="0"){
        return  $this->fetch("mobile/group/report");
    }

}