<?php
namespace app\index\controller;
use Think\Controller;
use Think\Db;
use app\index\controller\Common;
class News extends Common
{
    
    /*公告新闻列表*/
    public function index(){
        $pageParam    = ['query' =>[]];
        $ser=$where=[];
        $ser['title']=input('title','');
        $pageParam['query']=$ser;
        if(!empty($ser['title'])){
            $where['n.title']=array('like','%'.$ser['title'].'%');
            $pageParam['page']=1;
        }
        $this->assign('ser',$ser);
        $data=db('news')->alias('n')->where($where)->field('n.*')->order('n.createTime desc')->paginate(15, false, $pageParam);
        $this->assign('data',$data);
        return view();
    }
    
    public function createNews(){
        if ($this->request->isPost()){
            $data=[
                'title'=>input("title",""),
                'content'=>input("content",""),
                'userid'=>session("member")['id'],
                'type'=>input("type",0)
            ];
            $r=db('news')->insertGetId($data);
            if ($r){
                $this->xingePush($r,$data['type'],$data['title']);
                $this->success("发送成功");
            }else{
                $this->error("发送失败");
            }
        }else{
            return view();
        }
    }
    
    private function xingePush($id,$type,$title=""){
        vendor("xg.XingeApp");
      
        //IOS
        $xingeIOS=new \XingeApp("2200280420", "11f86da828f2ebf32c6a71136b5ccf61");
        // $xingeIOS=new \XingeApp("2200282613", "d6c667a2f2e1ce1c3c3c5044acd69e7f");
        $messageIOS=new \MessageIOS();
        $messageIOS->setAlert($title);
        $messageIOS->setBadge(1);
        $messageIOS->setSound("bingo.wav");
        $custom = array('type'=>$type,'nid'=>$id);
        $messageIOS->setCustom($custom);
        $res=$xingeIOS->PushAllDevices(0, $messageIOS,1);
        $res=$xingeIOS->PushAllDevices(0, $messageIOS,2);

        //安卓
        $xinge=new \XingeApp("2100280924", "69ced64bd27442b6d880eb9e659cbece");
        $mess = new \Message();
        $mess->setType(\Message::TYPE_NOTIFICATION);
        $mess->setTitle($title);
        $mess->setContent($title);
        $mess->setExpireTime(86400);
        $style = new \Style(0);
        #含义：样式编号0，响铃，震动，不可从通知栏清除，不影响先前通知
        $style = new \Style(0,1,1,0,0);
        $action = new \ClickAction();
        $action->setActionType(\ClickAction::TYPE_ACTIVITY);
        $action->setActivity("com.nuts.im.nutsim.uis.activities.MyWebViewXGActivity");
        // $action->setActivity("com.NUTSTALK.www.activities.MyWebViewXGActivity");
        #打开url需要用户确认
        $action->setComfirmOnUrl(0);
        $mess->setStyle($style);
        $mess->setAction($action);
        $mess->setCustom(['type'=>$type,'nid'=>$id]);
        $res=$xinge->PushAllDevices(0, $mess);
    }
}
