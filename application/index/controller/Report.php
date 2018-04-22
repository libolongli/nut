<?php
namespace app\index\controller;
use Think\Controller;
use Think\Db;
use app\index\controller\Common;
use app\index\logic\Api as serverApi;
class Report extends Common
{
    
    /*投诉列表*/
    public function index(){
        $pageParam    = ['query' =>[]];
        $ser=$where=[];
        $ser['name']=input('name','');
        $ser['id']=input("id","");
        $pageParam['query']=$ser;
        if(!empty($ser['name'])){
            $where['ur.name']=array('like','%'.$ser['name'].'%');
            $pageParam['page']=1;
        }
        if (!empty($ser['id'])){
            $where['ur.id']=$ser['id'];
            $pageParam['page']=1;
        }
        $this->assign('ser',$ser);
        $data=db('UserReport')->alias('ur')->where($where)->join('__USER__ u','ur.userId=u.id')->order("ur.createTime desc")->field('ur.*,u.name as oname,u.mobile as ophone,u.id oid')->paginate(15, false, $pageParam);
        $this->assign('data',$data);
        return view();
    }
    
    
    public function info($id){
        $info=db('UserReport')->alias('ur')->join('__USER__ u','ur.userId=u.id')->join("__USER__ ub","ub.id=ur.destId","left")->join("__GROUP__ ug","ug.id=ur.destId","left")->field('ur.*,u.name as oname,u.mobile as ophone,u.id oid,ub.name as bname,ub.mobile as bphone,ub.id bid,ug.id as gid,ug.name as gname')->where("ur.id",$id)->find();
        $info['imgs']=[];
        if ($info['report_imgs'])$info['imgs']=explode(",",$info['report_imgs']);
        $this->assign("info",$info);
        return view();
    }
    
    public function feedback(){
        $pageParam    = ['query' =>[]];
        $ser=$where=[];
        $ser['name']=input('name','');
        $ser['id']=input("id","");
        $pageParam['query']=$ser;
        if(!empty($ser['name'])){
            $where['ur.name']=array('like','%'.$ser['name'].'%');
            $pageParam['page']=1;
        }
        if (!empty($ser['id'])){
            $where['ur.id']=$ser['id'];
            $pageParam['page']=1;
        }
        $this->assign('ser',$ser);
        $data=db('FeedbackInformation')->alias('ur')->where($where)->join('__USER__ u','ur.userId=u.id')->order("ur.create_time desc")->field('ur.*,u.name as oname,u.mobile as ophone,u.id oid')->paginate(15, false, $pageParam);
        $this->assign('data',$data);
        return view();
    }
    
}
