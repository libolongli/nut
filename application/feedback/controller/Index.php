<?php
namespace app\feedback\controller;
use app\common\util\BaseUtil as BaseUtil;
use app\common\controller\Base;
class Index extends Base
{
	//热点问题
    public function index()
    {
    	$pid=input('pid','0');
    	$where=[];
    	$where['ishot']=1;
    	$where['type']=0;
    	$data=db('feedback')->where($where)->order('create_time desc')->field('title,id')->select();
    	$this->assign('data',$data);
    	return $this->view->fetch('mobile/feedback/index');
    }
    //问题详情
    public function content(){
    	$id=input('id');
    	$info = db('feedback')->where('id',$id)->find();
    	$this->assign('info',$info);
    	return $this->view->fetch('mobile/feedback/content');
    }

    //问题类型
    public function problem(){
    	$map['type']=0;
    	$map['pid']=0;
    	$data=db('feedback')->where($map)->field('title,id,icon')->select();
    	$this->assign('data',$data);
    	return $this->view->fetch('mobile/feedback/problem');
    }
    //常见问题
    public function problem_list(){
    	$pid=input('pid','0');
    	$where['pid']=$pid;
    	$where['type']=0;
    	$data=db('feedback')->where($where)->order('create_time desc')->field('title,id')->select();
    	$this->assign('data',$data);
    	return $this->view->fetch('mobile/feedback/problem_list');
    }
    //意见反馈
    public function feedback(){
    	$pid=input('pid',0);
    	$map['pid']=$pid;
    	$map['type']=1;
    	$data=db('feedback')->where($map)->field('title,id')->select();
    	$where['type']=1;
    	foreach ($data as &$value) {
    		$where['pid']=$value['id'];
    		$count=db('feedback')->where($where)->count();
    		$value['ischild']=$count;
    	}
    	$this->assign('data',$data);
    	return $this->view->fetch('mobile/feedback/feedback');
    }


    //意见反馈提交页面
    public function feedback_form(){
    	$id=input('id');
		$data=db('feedback')->where('id',$id)->field('problem,id')->find();
	    //$info['problem']=json_decode($data['problem']);
	    $info['id']=$data['id'];
	    if($info){
	    	BaseUtil::createSuccessData($info);
	    }else{

	    	BaseUtil::createFail('获取数据失败');
	    }
    	
    }
    public function feedback_submit(){
    	//$post_data=input('post.');
	    $data=array(
	    	'userid'=>input('userId'),
	    	'content'=>input('content'),
	    	'feedbackid'=>input('feedbackid'),
	    	'mobile'=>input('mobile'),
	    	'pic'=>input('pic'),
	    );
	    $res=db('feedbackInformation')->insert($data);
	    if ($res){
	       	BaseUtil::createSimpleSuccess('反馈成功');
	   }else{
	        BaseUtil::createFail('反馈失败');
	    }
    }

}
