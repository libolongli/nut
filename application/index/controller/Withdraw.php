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
    
    /**
     * [updateStatus 提款状态]
     * 1通过->打款->记录日志
     * 2拒绝->账变记录->更高钱包的钱(im_wallet)
     * @Author   nomius
     * @DateTime 2018-05-15
     * @param    [type]     $id  [description]
     * @param    [type]     $sta [description]
     * @return   [type]          [description]
     */
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
            ,'autowithdraw'
            ,'payway'
        ];
        $list=db("SysConfig")->where("_key",'in',$keys)->select();
        $info=[
            'dayMaxWithdraw'=>"0.00",
            "monthMaxWithdraw"=>"0.00",
            "yearMaxWithdraw"=>"0.00",
            "withdrawFee"=>"0.00",
            "tradeFee"=>"0.00",
            "autowithdraw"=>"0",
            "alipay"=>"0",
            "wxpay"=>"0",
            "mallpay"=>"0"
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
        $mallpay=input("mallpay","");
        $autowithdraw=input("autowithdraw","");
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
        db("SysConfig")->where("_key","autowithdraw")->setField("_value",$autowithdraw);
        db("SysConfig")->where("_key","payway")->where("_value",'like',"%alipay%")->setField("status",$alipay);
        db("SysConfig")->where("_key","payway")->where("_value",'like',"%wxpay%")->setField("status",$wxpay);
        db("SysConfig")->where("_key","payway")->where("_value",'like',"%mallpay%")->setField("status",$mallpay);
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
        if($sta =='1'){
            $this->error("不支持批量通过,只能批量拒绝!");
        }
        if ($ids){
            $r=db('UserWithdraw')->where("id",'in',$ids)->setField("tradestatus",$sta);
            if ($r!==false){
                $this->success("操作成功");
            }else{
                $this->error("操作失败");
            }
        }
    }

    /**
     * [payMoney 根据提款ID 来给用户代付提款]
     * CREATE TABLE `im_important_config` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `key_name` varchar(255) DEFAULT '' COMMENT '键的名字',
  `key_value` varchar(255) DEFAULT '' COMMENT '对应的值',
  PRIMARY KEY (`id`),
  KEY `im_im_config_key` (`key_name`(191)) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='网站重要的配置信息';
     * @Author   nomius
     * @DateTime 2018-05-15
     * @param    [type]     $uw_id [提款ID]
     * @return   [type]            [description]
     */
    public function payMoney($id){

        //需要检查 银行卡号 , 开户行 , 地址 姓名
        $bankinfo=db('UserWithdraw')->alias('uw')->join("__USER_BANKCARD__ ub","ub.id=uw.cardid","left")->field('uw.ordersn,uw.recamount,ub.cardNo,ub.bankName,ub.userName,ub.openBankName')->where("uw.id",$id)->find();
        
        print_r($bankinfo);exit;

        $banks = array(
            '中国建设银行'=>'CCB',
            '建设银行'=>'CCB',
            '中国工商银行'=>'ICBC',
            '工商银行'=>'ICBC',
            '中国农业银行'=>'ABC',
            '农业银行'=>'ABC',
            '中国银行'=>'BOC',
            '中国招商银行'=>'CMB',
            '招商银行'=>'CMB',
            '中国民生银行'=>'CMBC',
            '民生银行'=>'CMBC',
            '中国光大银行'=>'CEB',
            '光大银行'=>'CEB',
            '中国华夏银行'=>'HXB',
            '华夏银行'=>'HXB',
            '中国兴业银行'=>'CIB',
            '兴业银行'=>'CIB',
            '上海浦发银行'=>'SPDB',
            '中国中信银行'=>'CITIC',
            '中信银行'=>'CITIC',
            '广东发展银行'=>'GDB',
            '中国平安银行'=>'SDB    ',
            '平安银行'=>'SDB    ',
            '中国邮政储蓄银行'=>'PSBC',
            '北京银行'=>'BOB',
            '中国交通银行'=>'BOCM',
            '交通银行'=>'BOCM',
        );

        $data = array();
        $data['accountNo'] = 'shopadmin_lingdianzhibo';
        $data['amount'] = $bankinfo['amount'];
        $data['bankAlias'] = 'CCB';
        $data['bankNo'] = '6236683110004231671';
        $data['thirdOrderId'] = 'ly'.date('YmdHis');
        $data['place'] = '建设银行广东省分行';
        $data['realName'] = '陈兴用';



    }

    /**
     * [withdrawInfo 查看提款详情]
     * @Author   nomius
     * @DateTime 2018-05-15
     * @param    [type]     $ordersn [description]
     * @return   [type]              [description]
     */
    private function withdrawInfo($ordersn){

    }

}
