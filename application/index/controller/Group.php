<?php
namespace app\index\controller;
use Think\Controller;
use Think\Db;
use app\index\controller\Common;
use app\index\logic\Api as serverApi;
class Group extends Common
{
    
    /*群组列表*/
    public function index(){
        $pageParam    = ['query' =>[]];
        $ser=$where=[];
        $ser['name']=input('name','');
        $ser['id']=input("id","");
        $pageParam['query']=$ser;
        if(!empty($ser['name'])){
            $where['g.name']=array('like','%'.$ser['name'].'%');
            $pageParam['page']=1;
        }
        if (!empty($ser['id'])){
            $where['g.id']=$ser['id'];
            $pageParam['page']=1;
        }
        $this->assign('ser',$ser);
        $data=db('group')->alias('g')->where($where)->join('__USER__ u','g.createrId=u.id')->field('g.*,u.name as oname,u.mobile as ophone,u.id oid')->paginate(15, false, $pageParam);
        $this->assign('data',$data);
        return view();
    }
    
    
    public function configs(){
        $pageParam    = ['query' =>[]];
        $ser=$where=[];
        $pageParam['query']=$ser;
        $this->assign('ser',$ser);
        $data=db('GroupConfig')->alias('gc')->where($where)->field('gc.*')->paginate(15, false, $pageParam);
        $this->assign('data',$data);
        return view();
    }
    
    public function  addConfig(){
        if (request()->isAjax()){
            $addData=[
                'level'=>input('level/d',''),
                'fee'=>input('fee/f',''),
                'expire'=>input('expire/d',''),
            ];
            if (db('GroupConfig')->where(array('level'=>$addData['level']))->find())$this->error("该级别已经设置");
            if (!is_integer($addData['level'])||$addData['level']<=0)$this->error("费用格式不正确");
            if (!is_numeric($addData['fee'])||$addData['fee']<=0)$this->error("费用格式不正确");
            if (!is_numeric($addData['expire'])||$addData['expire']<=0)$this->error("有效时长格式不正确");
            $id=db('GroupConfig')->insertGetId($addData);
            if ($id){
                $this->success("添加成功");
            }else{
                $this->error("添加失败");
            }
        }
        return view();
    }
    
    public function configEdit($id=0){
        if (request()->isAjax()){
            $eid=input('eid/d');
            $addData=[
                'level'=>input('level/d',''),
                'fee'=>input('fee/f',''),
                'expire'=>input('expire/d',''),
            ];
            if (!db('GroupConfig')->where(array('level'=>$addData['level']))->find())$this->error("未查到级别配置");
            if (!is_integer($addData['level'])||$addData['level']<=0)$this->error("费用格式不正确");
            if (!is_numeric($addData['fee'])||$addData['fee']<=0)$this->error("费用格式不正确");
            if (!is_numeric($addData['expire'])||$addData['expire']<=0)$this->error("有效时长格式不正确");
            $id=db('GroupConfig')->where('id',$eid)->update($addData);
            if ($id){
                $this->success("编辑成功");
            }else{
                $this->error("编辑失败");
            }
        }else{
            $info=db('GroupConfig')->where('id',$id)->find();
            $this->assign("info",$info);
            return view();
        }
    }
    
    public function configDel($eid=0){
        $r=db('GroupConfig')->where('id',$eid)->delete();
        if ($r){
            $this->success("删除成功");
        }else{
            $this->error("删除失败");
        }
    }
    
    public function updateFrozen($gid,$sta=1){
        $data=[
            "groupId"=>$gid,
            "sta"=>$sta
        ];
        $res=serverApi::apiRequest(serverApi::frozeGroup,$data);
        if (isset($res['code'])&&$res['code']==1){
            $this->success("操作成功");
        }else{
            $this->error("操作失败");
        }
    }
    
    public function dissGroup($gid){
        $data=[
            "groupId"=>$gid
        ];
        $res=serverApi::apiRequest(serverApi::dissGroup,$data);
        if (isset($res['code'])&&$res['code']==1){
            $this->success("操作成功");
        }else{
            $this->error("操作失败");
        }
    }

}
