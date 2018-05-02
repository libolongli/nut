<?php
namespace app\wap\controller;
use app\common\controller\Base;
use app\common\model\Group as GroupMod;
use app\common\logic\ReportItems as reportLogic;
class Report extends Base{

    private $GroupMod;

    private $reportLogic;

    protected function _initialize(){
        parent::_initialize();
        // 非post用户不给访问
//         if (!request()->isPost()){
//             BaseUtil::createSimpleSuccess('服务器异常');
//         }
        $this->GroupMod=new GroupMod();
        $this->reportLogic=new reportLogic();
    }


    public function reportG($groupId="0",$userId="0"){
        $list=$this->reportLogic->getTree(0,'group','cn');
        return  $this->fetch("mobile/report",['title'=>lang('举报群组'),'list'=>$list,'type'=>'group','lang'=>'cn','act'=>request()->action()]);
    }

    public function reportFriend($friendId="0",$userId="0"){
        $list=$this->reportLogic->getTree(0,'user','cn');
        return  $this->fetch("mobile/report",['title'=>lang('举报用户'),'list'=>$list,'type'=>'user','lang'=>'cn','act'=>request()->action()]);
    }

    public function getReport($pid=0,$type="user",$lang='cn'){
        $list=$this->reportLogic->getChildByPid($pid,$type,$lang);
        $this->result($list,1,"获取成功",'json');
    }

}