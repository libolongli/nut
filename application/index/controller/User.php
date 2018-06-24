<?php
namespace app\index\controller;
use Think\Controller;
use Think\Db;
class User extends Common
{
    /*用户列表*/
    public function index(){
        $pageParam    = ['query' =>[]];
        $ser=$where=[];
        $ser['name']=input('name','');
        $ser['registTime']=input("registTime","");
        $ser['lastLogin']=input("lastLogin","");
        $ser['lastIp']=input("lastIp","");
        $ser['accountStatus']=input("accountStatus","");
        $ser['id']=input("id","");
        $ser['mail']=input("mail","");
        $ser['phone']=input("phone","");
        $pageParam['query']=$ser;
        if(!empty($ser['name'])){
            $where['u.name']=array('like','%'.$ser['name'].'%');
            $pageParam['page']=1;
        }
        if (!empty($ser['registTime'])){
            $where['SUBSTR(u.createTime,1,10)']=['between',[strtotime($ser['registTime']),strtotime($ser['registTime'])+86400]];
            $pageParam['page']=1;
        }
        if (!empty($ser['id'])){
            $where['u.id']=$ser['id'];
            $pageParam['page']=1;
        }
        if(!empty($ser['accountStatus'])){
            if ($ser['accountStatus']=='020')$where['w.status']=0;
            if ($ser['accountStatus']=='010')$where['u.status']=0;
            $pageParam['page']=1;
        }
        if (!empty($ser['mail'])){
            $where['u.mail']=array('like','%'.$ser['mail'].'%');
            $pageParam['page']=1;
        }
        if (!empty($ser['phone'])){
            $where['u.mobile']=array('like','%'.$ser['phone'].'%');
            $pageParam['page']=1;
        }
        if (!empty($ser['lastLogin'])){
            $where['SUBSTR(u.last_login_time,1,10)']=['between',[strtotime($ser['lastLogin']),strtotime($ser['lastLogin'])+86400]];
            $pageParam['page']=1;
        }
        if (!empty($ser['lastIp'])){
            $where['u.last_login_ip']=$ser['lastIp'];
            $pageParam['page']=1;
        }

        //只展示没删除的用户
        $where['u.isdelete'] = 0;

        $this->assign('ser',$ser);
        $data=db('user')->alias('u')->where($where)->join('im_wallet w','u.id=w.userId')->field('u.*,w.money,w.status as wstatus,w.maxdraw as draw')->order('u.id desc')->paginate(15, false, $pageParam);
        $this->assign('data',$data);
        return view();
    }
    /*财务管理*/
    public function walletHistory(){
        $pageParam    = ['query' =>[]];
        $where=$search=[];
        $search['nickname']=input('nickname','');
        $search['id']=input("id","");
        $search['moneyType']=input("moneyType","");
        $search['moneyDirect']=input("moneyDirect","");
        $search['ctime']=input("ctime","");
        $where['w.formType']=0;
        $pageParam['query']=$search;
        if(!empty($search['nickname'])){
            $where['u.name']=array('like','%'.$search['nickname'].'%');
            // $pageParam['page'] = 1;
        }
        if(!empty($search['id'])){
            $where['u.id']=$search['id'];
            // $pageParam['page'] = 1;
        }
        if(!empty($search['moneyType'])){
            $where['w.moneyType']=$search['moneyType'];
            // $pageParam['page'] = 1;
        }
        if(!empty($search['moneyDirect'])&&$search['moneyDirect']!=0){
            $where['w.moneyDirect']=$search['moneyDirect'];
            // $pageParam['page'] = 1;
        }
        if (!empty($search['ctime'])){
            $time=$search['ctime'];
            $where['SUBSTR(w.occurTime,1,10)']=['between',[strtotime(date("$time 00:00:00")),strtotime(date("$time 23:59:59"))]];
            $search['ctime']="";
            $pageParam['query']['time']="";
            // $pageParam['page'] = 1;
        }
        $pageParam['page'] = input('page',1);
        $this->assign('search',$search);
        $total=db('walletHistory')->alias('w')->where($where)->join('im_user u','w.userId=u.id','LEFT')->sum('moneyDirect*amount');
        $today_deposit = db('walletHistory')->where(array('moneyType'=>1,'occurTime'=>array('>=',strtotime('today')*1000)))->sum('amount');
        $today_withdrawal = db('walletHistory')->where(array('moneyType'=>2,'occurTime'=>array('>=',strtotime('today')*1000)))->sum('amount');
        $depost_withdrawal = $today_deposit - $today_withdrawal;
        $this->assign("total",array('total'=>$total,'today_deposit'=>$today_deposit,'today_withdrawal'=>$today_withdrawal,'depost_withdrawal'=>$depost_withdrawal));
        $data=db('walletHistory')->alias('w')->where($where)->join('im_user u','w.userId=u.id','LEFT')->join('im_user ug','w.destId=ug.id','LEFT')->field('w.*,u.realname,u.name as nickname,ug.name as getname')->order('w.occurTime desc')->paginate(15, false, $pageParam);
        
        $this->assign('data',$data);
        return view();
    }
    /*用户充值*/
    public function userpay(){
        $id=input('id');
        $where['userId']=array('eq',$id);
        $member=db('user')->alias('u')->where($where)->join('im_wallet w','u.id=w.userId')->field('u.*,w.money')->find();
        if (request()->isPost()){
            $post_data=input('post.');
            $data=array(
                'content'=>$post_data['desc'],
                'moneyType'=>$post_data['type']==0?1:15,
                'amount'=>$post_data['money'],
                'userId'=>$id,
                'formType'=>1,
                'moneyDirect'=> $post_data['type']==0?1:-1,
                'destId'=>0,
                'occurTime'=>time().'000'
            );
            $res=db('walletHistory')->insert($data);
            if($res!==false){
                if($post_data['type']==0) db('wallet')->where($where)->setInc('money',$post_data['money']);
                else db('wallet')->where($where)->setDec('money',$post_data['money']);
                $this->success('操作成功,账户金额'.(($post_data['type']==0?1:-1)*$post_data['money']));
            }else{
                $this->error('操作失败');
            }
        }else{
            $this->assign('member',$member);
            return view(); 
        }
        
    }
    /*实名认证*/
    public function userAuth(){
        $id=input('id');
        $where['id']=array('eq',$id);
        $member=db('user')->where($where)->find();
        if (request()->isPost()){
            $isAuth=input('isAuth');
            $res=db('user')->where($where)->setField('isAuth',$isAuth);
            if($res){
                $this->success('认证成功');
            }else{
                $this->error('认证失败');
            }
        }else{
            $this->assign('member',$member);
            return view(); 
        }
    }
    /*好友关系*/
    public function userFriend(){
        $pageParam    = ['query' =>[]];
        $search['id']=input('id');
        $where['userId']=array('eq',$search['id']);
        $pageParam['query']['userId'] = $search['id'];
        $search['name']=input('name');
        if($search['name']){
            $where['name']=array('like','%'.$search['name'].'%');
            $pageParam['query']['name'] = $search['name'];
        }
        $search['mobile']=input('mobile');
        if($search['mobile']){
            $where['mobile']=array('like','%'.$search['mobile'].'%');
            $pageParam['query']['mobile'] = $search['mobile'];
        }
        
        $list=db('Friend')->alias('f')->where($where)->join('im_user u','f.friendId=u.id','LEFT')->field('f.*,u.mobile,u.name')->paginate(15, false, $pageParam);
        $this->assign('list',$list);
        $this->assign('search',$search);
        return view(); 
    }
    /*编辑用户信息*/
    public function userEdit(){
        $id=input('id');
        $where['id']=array('eq',$id);
        $member=db('user')->where($where)->find();
        if (request()->isPost()){
            $post_data=input('post.');

            $data=array(
                'mobile'=>$post_data['mobile'],
            );

            if(trim($post_data['pwd'])){
                $data['pwd'] = md5($post_data['pwd']);
            }

            $res=db('user')->where($where)->update($data);
            if($res){
                $this->success('编辑成功');
            }else{
                $this->error('编辑失败');
            }
        }else{
            $this->assign('member',$member);
            return view(); 
        }
    }
    /*删除用户*/
    public function deluser(){
        $id=input('id');
        if($id){
            $res=db('user')->where('id',$id)->setField('isdelete',1);
            if($res!==false){
                $this->success('删除成功');
            }else{
                $this->error('删除失败');
            }
        }
    }

    
    /*朋友圈管理*/
    public function feed(){
        $pageParam    = ['query' =>[]];
        $where['f.id']=array('neq',0);
        $pageParam['query']['id'] = 0;
        $search['name']=input('name');
        if($search['name']){
            $where['u.name']=array('like','%'.$search['name'].'%');
            $pageParam['query']['name'] = $search['name'];
        }
        $search['mobile']=input('mobile');
        if($search['mobile']){
            $where['u.mobile']=array('like','%'.$search['mobile'].'%');
            $pageParam['query']['mobile'] = $search['mobile'];
        }
        
        $list=db('feed')->alias('f')->where($where)->join('im_user u','f.user_id=u.id','LEFT')->field('f.*,u.mobile,u.name')->order('create_time desc')->paginate(10, false, $pageParam);
        $data=$list->items();
        foreach ($data as $key=>$value) {
            if($value['feed_imgs']){
                $ii='';
                foreach (explode(',', $value['feed_imgs']) as $k => $val) {
                    $ii.='<img src="'.$val.'" style="right:'.(($k+1)*10).'px;" >';
                }
                $data[$key]['data']=$ii;
            }
        }
        $this->assign('list',$data);
        $this->assign('page',$list->render());
        $this->assign('search',$search);
        return view(); 
    }
    /*用户信息*/
    public function userInfo(){
        $id=input('id');
        $where['id']=array('eq',$id);
        $member=db('user')->where($where)->find();
        $this->assign('member',$member);
        return view();
    }
    /*删除朋友圈动态I*/
    public function feeddelete(){
        $id=input('id');
        $where['id']=array('eq',$id);
        $imgs=db('feed')->where($where)->value('feed_imgs');
        // if($imgs){
        //     foreach (explode(',', $imgs) as $key => $value) {
        //         unlink($value);
        //     }
        // }
        $res=db('feed')->where($where)->delete();
        if($res){
            db('FeedComment')->where('feed_id',$id)->delete();
            db('FeedPraise')->where('feed_id',$id)->delete();
            $this->success('删除成功');
        }else{
            $this->error('删除失败');
        }
    }

    /*查看相册*/
    public function feedImgs(){
       $id=input('id');
        $where['id']=array('eq',$id);
        $imgs=db('feed')->where($where)->value('feed_imgs');
        $this->assign('imgs',$imgs);
        return view(); 
    }
	
	/*充值明细*/
    public function userorder(){
        $pageParam    = ['query' =>[]];
        $search=[];
        $where=[];
        $where['o.id']=array('neq',0);
        $pageParam['query']['id'] = 0;
        $search['userid']=input('userid');
        $search['addTime']=input('addTime');
        $search['payway']=input('payway');
        $search['amount']=input('amount');
        $search['outTradeNo']=input('outTradeNo');
        $pageParam['query']=$search;
        if(!empty($search['outTradeNo'])){
            $where['o.outTradeNo']=array('like','%'.$search['outTradeNo'].'%');
            // $pageParam['page']=1;
        }
        if (!empty($search['userid'])){
            $where['o.userId']=$search['userid'];
            // $pageParam['page']=1;
        }
        if (!empty($search['addTime'])){
            $where['o.createTime']=['between',[$search['addTime'].' 00:00:00',$search['addTime'].' 23:59:59']];
            // $pageParam['page']=1;
        }
        if (!empty($search['payway'])){
            $where['o.plat']=$search['payway'];
            // $pageParam['page']=1;
        }
        if (!empty($search['amount'])&&is_numeric($search['amount'])&&$search['amount']>0){
            $where['o.amount']=sprintf("%.2f",$search['amount']);
            // $pageParam['page']=1;
        }
        $pageParam['page'] = input('page',1);
        $this->assign('search',$search);
        $list=db('UserOrders')->alias('o')->where($where)->join('im_user u','o.userId=u.id','LEFT')->field('o.*,u.mobile,u.name')->order('createTime desc')->paginate(10, false, $pageParam);
        $data=$list->items();
        $this->assign('list',$data);
        $this->assign('page',$list->render());
        return view(); 
    }

	/**
     * 靓号管理
     */
    public function numberIndex(){
        $pageParam    = ['query' =>[]];
        $where=$ser=array();
        $item=input('item','');
        if($item=='rule'){
            $where['isdelete']=array('eq',0);
            $pageParam['query']['isdelete'] = 0;
            $data=db('SamNumberType')->where($where)->select();
        }else{
            $where['su.id']=array('neq',0);
            $pageParam['query']['id'] = 0;
            $ser['number']=input('number','');
            if($ser['number']){
                $where['number']=array('like','%'.$ser['number'].'%');
                $pageParam['query']['number'] = $ser['number'];
            }
            $this->assign('ser',$ser);
            $data=db('SamNumber')->alias('su')->where($where)->join([['im_user u','u.id=su.userId','left']])->field('su.*,u.nickName,u.mobile,u.headUrl')->paginate(15, false, $pageParam);
        }
        $this->assign('item',$item);
        $this->assign('data',$data);
        return view();
    }

    public function numberAdd(){
        if (request()->isPost()){
            $post_data=input('post.');
            $rule='pairs-'.$post_data['type'].';';
            if($post_data['rule']){
                 foreach ($post_data['rule'] as $key => $value) {
                     $rule.='pairs-'.$value.';';
                 }
             }
            //$rule.='len-'.strlen($post_data['number']).';';
            $data=array(
                'number'=>$post_data['number'],
                'price'=>$post_data['price'],
                'rule'=>numberRule($post_data['number'])
            );
            if(!preg_match("/^\d*$/",$data['number']))$this->error("号码必须纯数字");
            if (strlen($data['number'])>12||strlen($data['number'])<6)$this->error("号码必须在6-12位之间");
            if (db('SamNumber')->where('number',$data['number'])->find())$this->error("靓号已经存在");
            setlocale(LC_ALL,"zh");
            $price=0;
            try {
                $price=number_format($data['price'],2,".","");
            }catch (\Exception $e){
                $this->error("价格格式不正确");
            }
            if ($price<0.01)$this->error("请输入价格");
            $res=db('SamNumber')->insert($data);
            //$Predis=new Redis();
            //$Predis->hashSet("SamNumber", [$data['number']=>0]);
            if($res!==false){
                $this->success('添加靓号成功');
            }else{
                $this->error('添加靓号失败');
            }
        }else{
            $type=db('SamNumberType')->select();
            $this->assign('type',$type);
            return view();
        }
    }

    /*规则编辑*/
    public function numberTypeEdit(){
        $id=input('id');
        if (request()->isPost()){
            $res=db('SamNumberType')->where('id',$id)->setField('type',input('type'));
            if($res!==false){
                $this->success('编辑规则成功');
            }else{
                $this->error('编辑规则失败');
            }
        }else{
            $info=db('SamNumberType')->where('id',$id)->find();
            $this->assign('info',$info);
            return view();
        }
        
    }
    /*删除规则*/
    public function numberTypeDelete(){
        $id=input('id');
        $res=db('SamNumberType')->where('id',$id)->setField('isdelete',1);
        if($res!==false){
            $this->success('删除成功');
        }else{
            $this->error('删除失败');
        }
        
    }

    public function check(){
        $number=input('number');
        $res=numberRule($number);
        $json='';
        if($res){
            $rule='';
            $new=explode(';', $res);
            $rule.='<span id="rule0">';
            $rule.='<input type="text" class="tpl-form-input min-width" value="'.str_replace('pairs-','',$new[1]).'" disabled=disabled >';
            $rule.='<input type="hidden" class="tpl-form-input min-width" value="'.str_replace('pairs-','',$new[1]).'" name="rule[]" >';
            $rule.='<span onclick="addrule()" >添加</span>';
            $rule.='</span>';
            $json['rule']=$rule;
            $json['type']=str_replace('pairs-','',$new[0]);
        }
        echo json_encode($json);
    }
	
	/*推送管理*/
    public function push(){
        $pageParam    = ['query' =>[]];

        $where['p.id']=array('neq',0);
        $pageParam['query']['id'] = 0;
        $ser['title']=input('title','');
        if($ser['title']){
            $where['p.title']=array('like','%'.$ser['title'].'%');
            
        }
        $pageParam['query'] = $ser;
        $this->assign('ser',$ser);
        $data=db('app_push_message')->alias('p')->where($where)->paginate(15, false, $pageParam);
        $this->assign('data',$data);

        return view('/user/pushMessage');
    }
    
    public function updateFrozen($uid,$sta){
        $r=db('user')->where('id',$uid)->update(['status'=>$sta]);
        if ($r!==false){
            $this->success("操作成功");
        }else{
            $this->error("操作失败");
        }
    }
    
    public function frozenWallet($uid,$sta){
        $r=db('wallet')->where('userId',$uid)->update(['status'=>$sta]);
        if ($r!==false){
            $this->success("操作成功");
        }else{
            $this->error("操作失败");
        }
    }
    public function frozenAll($uid){
        $r=db('user')->where('id',$uid)->update(['status'=>0]);
        $r=db('wallet')->where('userId',$uid)->update(['status'=>0]);
        if ($r!==false){
            $this->success("操作成功");
        }else{
            $this->error("操作失败");
        }
    }
    
    public function updateLimit($uid,$value){
        if (!is_numeric($value)||$value<0)$this->error("金额格式不正确");
        $r=db('wallet')->where('userId',$uid)->update(['maxdraw'=>$value]);
        if ($r!==false){
            $this->success("操作成功");
        }else{
            $this->error("操作失败");
        }
    }
    
    /*朋友圈礼物*/
    public function feedgift(){
        $ser['from']=input('from','');
        $ser['to']=input('to','');
        $this->assign('ser',$ser);
        
        $pageParam    = ['query' =>[]];
        $where['id']=array('neq',0);
        $pageParam['query']['id'] = 0;
        $data=db('feedGift')->where($where)->paginate(15, false, $pageParam);
        $this->assign('data',$data);
        return view();
    }
	
    /**
     * [bankcard 银行卡管理]
     * @Author   nomius
     * @DateTime 2018-06-09
     * @return   [type]     [description]
     */
    public function bankcard($id){
       $list=db('UserBankcard')->where(array('userId'=>$id))->field('id,cardNo,bankName,userName,openBankName')->paginate(15, false);
        $this->assign('list',$list);
        return view();
    }

    /**
     * [delbankcard 删除银行卡]
     * @Author   nomius
     * @DateTime 2018-06-10
     * @param    [type]     $id [description]
     * @return   [type]         [description]
     */
    public function delbankcard($id){
        db('UserBankcard')->delete($id);
        $this->success("删除成功");
    }

}