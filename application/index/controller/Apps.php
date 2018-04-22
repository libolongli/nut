<?php
namespace app\index\controller;
use app\index\controller\Common;
use app\index\model\Apps as appsModel;
class Apps extends Common{
    public function appList(){
        $username = input('username');
        $appname = input('appname');
        $map = array();
        if(!empty($username)){
            $map['u.name'] = array('like',"%$username%");
        }
        if(!empty($appname)){
            $map['a.app_name'] = array('like',"%$appname%");
        }
        $list = appsModel::alias('a')->field('a.*,u.name')->join('user u','u.id=a.userId')->where($map)->order('a.status')->paginate();
        $page = $list->render();
        $this->assign('username',$username);
        $this->assign('appname',$appname);
        $this->assign('list',$list);
        $this->assign('page',$page);
        return view();
    }
    /*
     * 应用审核
     */
    
    public function appCheck(){
        $id = input('id');
        $info = appsModel::alias('a')->field('a.*,u.name,u.mobile')->join('user u','u.id=a.userId')->where(['a.id'=>$id])->find();
        $this->assign('info',$info);
        return view();
    }
    public function save(){
        $id = input('id');
        $status = input('status');
        $res = appsModel::where(['id'=>$id])->data(['status'=>$status])->update();
        if($res !== false){
            $this->success('操作成功！');
        }else{
            $this->error('操作失败！');
        }
    }
    /*
     * 删除应用
     */
    public function deleteApp(){
        $id = input('id');
        $res = appsModel::where(['id'=>$id])->delete();
        if($res){
            $this->success('删除成功！');
        }else{
            $this->error('删除失败！');
        }
    }
    /*
     * 应用详情
     */
    public function appInfo(){
        $id = input('id');
        $info = appsModel::alias('a')->field('a.*,u.name,u.mobile')->join('user u','u.id=a.userId')->where(['a.id'=>$id])->find();
        $this->assign('info',$info);
        return view();
    }
}
