<?php
namespace app\index\controller;
use Think\Controller;
use Think\Db;
use app\index\controller\Common;
class Withdraw extends Common
{
    /*提现申请列表*/
    public function index(){
        $pageParam    = ['query' =>[]];
        $search=$where=[];
        $search['id']=input("id","");
        $search['ctime']=input("ctime","");
        $search['ordersn']=input("ordersn","");
        $search['amount']=input("amount","");
        $search['status']=input("status","-1");
        $pageParam['query']=$search;
        if(!empty($search['ctime'])){
            $time=$search['ctime'];
            $where['uw.createTime']=array('between',[date("Y-m-d H:i:s",strtotime($time." 00:00:00")),date("Y-m-d H:i:s",strtotime($time." 23:59:59"))]);
            $search['ctime']=$pageParam['query']['ctime']="";
            $pageParam['page']=1;
        }
        if (!empty($search['id'])){
            $where['uw.id']=$search['id'];
            $pageParam['page']=1;
        }
        if (!empty($search['ordersn'])){
            $where['uw.ordersn']=['like',"%".$search['ordersn']."%"];
            $pageParam['page']=1;
        }
        if (!empty($search['amount'])){
            $where['uw.amount']=sprintf("%.2f",$search['amount']);
            $pageParam['page']=1;
        }
        if (!empty($search['status'])&&$search['status']>=0){
            $where['uw.tradestatus']=$search['status'];
            $pageParam['page']=1;
        }
        $this->assign('search',$search);
        $data=db('UserWithdraw')->alias('uw')->where($where)->join('__USER__ u','uw.userId=u.id')->field('uw.*,u.name as oname')->order("uw.createTime desc")->paginate(15, false, $pageParam);
        $this->assign('data',$data);
        return view();
    }
    
    public function updateStatus($id,$sta){
        $r=db('UserWithdraw')->where("id",$id)->setField("tradestatus",$sta);
        if ($r!==false){
            $this->success("操作成功");
        }else{
            $this->error("操作失败");
        }
    }
    
    public function info($id){
        $info=db('UserWithdraw')->alias('uw')->join('__USER__ u','uw.userId=u.id')->join("__USER_BANKCARD__ ub","ub.id=uw.cardid","left")->field('uw.*,u.name as oname,ub.cardNo,ub.bankName,ub.userName,ub.openBankName')->where("uw.id",$id)->find();
        $this->assign("info",$info);
        return view();
    }
    
    
    /**
     * 财务风险异常客户列表
     */
    public function risklist(){
        $pageParam    = ['query' =>[]];
        $search=$where=[];
        $search['id']=input("id","");
        $search['ctime']=input("ctime","");
        $search['amount']=input("amount","");
        $pageParam['query']=$search;
        if(!empty($search['ctime'])){
            $time=$search['ctime'];
            $where['ur.createTime']=array('between',[date("Y-m-d H:i:s",strtotime($time." 00:00:00")),date("Y-m-d H:i:s",strtotime($time." 23:59:59"))]);
            $search['ctime']=$pageParam['query']['ctime']="";
            $pageParam['page']=1;
        }
        if (!empty($search['id'])){
            $where['ur.id']=$search['id'];
            $pageParam['page']=1;
        }
        if (!empty($search['amount'])){
            $where['ur.amount']=sprintf("%.2f",$search['amount']);
            $pageParam['page']=1;
        }
        $this->assign('search',$search);
        $data=db("UserRisk")->alias("ur")->join("__USER__ u","u.id=ur.userId")->where($where)->field("ur.*,u.name as oname")->order("ur.createTime desc")->paginate(15, false, $pageParam);
        $this->assign('data',$data);
        return view();
    }
    
    /**
     * dayMaxWithdraw
        monthMaxWithdraw
        yearMaxWithdraw
        withdrawFee
        tradeFee
        payway
        payway
     * @return \think\response\View
     */
    public function setoption(){
        $keys=[
            'dayMaxWithdraw'
            ,'monthMaxWithdraw'
            ,'yearMaxWithdraw'
            ,'withdrawFee'
            ,'tradeFee'
            ,'payway'
        ];
        $list=db("SysConfig")->where("_key",'in',$keys)->select();
        $info=[
            'dayMaxWithdraw'=>"0.00",
            "monthMaxWithdraw"=>"0.00",
            "yearMaxWithdraw"=>"0.00",
            "withdrawFee"=>"0.00",
            "tradeFee"=>"0.00",
            "alipay"=>"0",
            "wxpay"=>"0"
        ];
        if ($list){
            foreach ($list as $value){
                if ($value['_key']=="payway"){
                    $json=json_decode($value['_value'],true);
                    if (isset($json['paykey'])){
                        $info[$json['paykey']]=$value['status'];
                    }
                }
                else{
                    $info[$value['_key']]=$value['_value'];
                }
            }
        }
        $this->assign("info",$info);
        return view();
    }
    
    public function saveOption(){
        $dayMaxWithdraw=input("dayMaxWithdraw",0);
        $monthMaxWithdraw=input("monthMaxWithdraw",0);
        $yearMaxWithdraw=input("yearMaxWithdraw",0);
        $withdrawFee=input("withdrawFee",0);
        $tradeFee=input("tradeFee",0);
        $alipay=input("alipay","");
        $wxpay=input("wxpay","");
        if (!is_numeric($dayMaxWithdraw)||$dayMaxWithdraw<0){
            $this->error("日限额格式不正确");
        }
        if (!is_numeric($monthMaxWithdraw)||$monthMaxWithdraw<0){
            $this->error("月限额格式不正确");
        }
        if (!is_numeric($yearMaxWithdraw)||$yearMaxWithdraw<0){
            $this->error("年限额格式不正确");
        }
        if (!is_numeric($withdrawFee)||$withdrawFee<0||$withdrawFee>100){
            $this->error("提现费率格式不正确");
        }
        if (!is_numeric($tradeFee)||$tradeFee<0||$tradeFee>100){
            $this->error("交易费率格式不正确");
        }
        db("SysConfig")->where("_key","dayMaxWithdraw")->setField("_value",$dayMaxWithdraw);
        db("SysConfig")->where("_key","monthMaxWithdraw")->setField("_value",$monthMaxWithdraw);
        db("SysConfig")->where("_key","yearMaxWithdraw")->setField("_value",$yearMaxWithdraw);
        db("SysConfig")->where("_key","withdrawFee")->setField("_value",$withdrawFee);
        db("SysConfig")->where("_key","tradeFee")->setField("_value",$tradeFee);
        db("SysConfig")->where("_key","payway")->where("_value",'like',"%alipay%")->setField("status",$alipay);
        db("SysConfig")->where("_key","payway")->where("_value",'like',"%wxpay%")->setField("status",$wxpay);
        $this->success("保存成功");
    }
    
    
    public function plat(){
        $pageParam    = ['query' =>[]];
        $where=$search=[];
        $search['nickname']=input('nickname','');
        $search['id']=input("id","");
        $search['moneyType']=input("moneyType","");
        $search['moneyDirect']=input("moneyDirect","");
        $search['ctime']=input("ctime","");
        $pageParam['query']=$search;
        $where['w.formType']=1;
        if(!empty($search['nickname'])){
            $where['u.name']=array('like','%'.$search['nickname'].'%');
            $pageParam['page'] = 1;
        }
        if(!empty($search['id'])){
            $where['w.id']=$search['id'];
            $pageParam['page'] = 1;
        }
        if(!empty($search['moneyType'])){
            $where['w.moneyType']=$search['moneyType'];
            $pageParam['page'] = 1;
        }
        if(!empty($search['moneyDirect'])&&$search['moneyDirect']!=0){
            $where['w.moneyDirect']=$search['moneyDirect'];
            $pageParam['page'] = 1;
        }
        if (!empty($search['ctime'])){
            $time=$search['ctime'];
            $where['SUBSTR(w.occurTime,1,10)']=['between',[strtotime(date("$time 00:00:00")),strtotime(date("$time 23:59:59"))]];
            $search['ctime']="";
            $pageParam['query']['time']="";
            $pageParam['page'] = 1;
        }
        $this->assign('search',$search);
        $total=db('walletHistory')->alias('w')->where($where)->join('im_user u','w.userId=u.id','LEFT')->sum('moneyDirect*amount');
        $this->assign("total",$total);
        $data=db('walletHistory')->alias('w')->where($where)->join('im_user u','w.userId=u.id','LEFT')->field('w.*,u.realname,u.name as nickname')->order('w.occurTime desc')->paginate(15, false, $pageParam);
        
        $this->assign('data',$data);
        return view();
    }
    
    function batchupdateStatus(){
        $ids=input("ids/a",[]);
        $sta=input("sta",0);
        if ($ids){
            $r=db('UserWithdraw')->where("id",'in',$ids)->setField("tradestatus",$sta);
            if ($r!==false){
                $this->success("操作成功");
            }else{
                $this->error("操作失败");
            }
        }
    }
}
