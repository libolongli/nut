<?php
namespace app\index\controller;
use Think\Controller;
use Think\Db;
use app\index\model\Admin as AdminUser;
use app\index\model\AdminPrivs;
use app\index\model\Menu;
class Admin extends Common
{
    /*管理员列表*/
    public function index(){
        $pageParam    = ['query' =>[]];
        $where['a.id']=array('neq',0);
        $pageParam['query']['id'] = 0;
        $name=input('name','');
        if($name){
            $where['a.username']=array('like','%'.$name.'%');
            $pageParam['query']['name'] = $name;
        }
        $this->assign('name',$name);

        $data=AdminUser::alias('a')->where($where)->join('im_admin_role r','r.id=a.roleid')->field("a.*,r.name as role")->paginate(15, false, $pageParam);
        $this->assign('data',$data);
        return view();
    }
    /*添加管理员*/
    public function AdminAdd(){
        if (request()->isPost()){
            $post_data=input('post.');
            $data=array(
                'username' =>$post_data['username'],
                'nickname'=>$post_data['nickname'],
                'mobile' =>$post_data['mobile'],
                'email' =>$post_data['email'],
                'status' =>$post_data['status'],
                'roleid' =>$post_data['roleid'],
            );
            if (AdminUser::where("username",$data['username'])->find())$this->error('该名称已经存在');
            if($post_data['password']){
                if(empty($post_data['repassword'])) $this->error('请输入确认密码');
                if($post_data['password']!=$post_data['repassword']) $this->error('两次输入密码不一致');
                $pwd=password($post_data['password']);
            }else{
                $pwd=password('123456');
            }
            $data['password']=$pwd['password'];
            $data['salt']=$pwd['salt'];
            $res=AdminUser::insert($data);
            if($res!==false){
                $this->success('管理员添加成功');
            }else{
                $this->error('管理员添加失败');
            }
        }else{
            $data=db('adminRole')->where('status',0)->select();
            $this->assign('data',$data);
            return view();
        }
        
    }
    /*编辑管理员信息*/
    public function AdminEdit(){
        $id=input('id');
        if (request()->isPost()){
            $post_data=input('post.');
            $data=array(
                'username' =>$post_data['username'],
                'nickname'=>$post_data['nickname'],
                'mobile' =>$post_data['mobile'],
                'email' =>$post_data['email'],
                'status' =>$post_data['status'],
                'roleid' =>$post_data['roleid'],
            );
            if($post_data['password']){
                if(empty($post_data['repassword'])) $this->error('请输入确认密码');
                if($post_data['password']!=$post_data['repassword']) $this->error('两次输入密码不一致');
                $pwd=password($post_data['password']);
                $data['password']=$pwd['password'];
                $data['salt']=$pwd['salt'];
            }
            
            $res=AdminUser::where('id',$id)->update($data);
            if($res){
                $this->success('编辑管理员信息成功');
            }else{
                $this->error('编辑管理员信息失败');
            }
        }else{
            if($id){
                $info=AdminUser::where('id',$id)->find();
                $this->assign('info',$info);
            }
            $data=db('adminRole')->where('status',0)->select();
            $this->assign('data',$data);
            return view();
        }
        
    }
    /*删除管理员*/
    public function AdminDelete(){
        $id=input('id');
        if($id){
            $res=AdminUser::where('id',$id)->delete();
            if($res){
                $this->success('删除成功');
            }else{
                $this->error('删除失败');
            }
        }
    }


    /*角色列表*/
    public function roleList(){
        $pageParam    = ['query' =>[]];
        $where['id']=array('neq',0);
        $pageParam['query']['id'] = 0;
        $name=input('name','');
        if($name){
            $where['name']=array('like','%'.$name.'%');
            $pageParam['query']['name'] = $name;
        }
        $this->assign('name',$name);

        $data=db('AdminRole')->where($where)->paginate(15, false, $pageParam);
        $this->assign('data',$data);
        return view();
    }

    /*添加角色*/
    public function RoleAdd(){
        if (request()->isPost()){
            $post_data=input('post.');
            $data=array(
                'name' =>$post_data['name'],
                'desc'=>$post_data['desc'],
                'status' =>$post_data['status'],
            );
            if (db('AdminRole')->where("name",$data['name'])->find())$this->error('角色名称已经存在');
            $res=db('AdminRole')->insert($data);
            if($res!==false){
                $this->success('角色添加成功');
            }else{
                $this->error('角色添加失败');
            }
        }else{
            return view();
        }
    }

    /*编辑角色*/
    public function RoleEdit(){
        $id=input('id');
        if (request()->isPost()){
            $post_data=input('post.');
            $data=array(
                'name' =>$post_data['name'],
                'desc'=>$post_data['desc'],
                'status' =>$post_data['status'],
            );
            $res=db('AdminRole')->where('id',$id)->update($data);
            if($res!==false){
                $this->success('角色编辑成功');
            }else{
                $this->error('角色编辑失败');
            }
        }else{
            if($id){
                $info=db('AdminRole')->where('id',$id)->find();
                $this->assign('info',$info);
            }
            return view();
        }
    }

    /*删除角色*/
    public function RoleDelete(){
        $id=input('id');
        if($id){
            $res=db('AdminRole')->where('id',$id)->delete();
            if($res){
                $this->success('删除成功');
            }else{
                $this->error('删除失败');
            }
        }
    }

    /*角色权限*/
    public function privs(){
        $id=input('id');
        if (request()->isPost()){
            $post_data=input('post.menu/a');
            $menus=implode(',', $post_data);
            if (!AdminPrivs::where('roleid',$id)->find()){
                $res= AdminPrivs::create(['roleid'=>$id,'menus'=>$menus]);
            }else{
                $res=AdminPrivs::where('roleid',$id)->setField('menus',$menus);
            }
            if($res){
                session("privsMenus",null);
                $this->success('编辑权限成功');
            }else{
                $this->error('编辑权限失败');
            }
        }else{
            if (!AdminPrivs::where('id',$id)->find()){
                AdminPrivs::create(['roleid'=>$id,'menus'=>'']);
            }
            $data=db("AdminRole")->alias('r')->field('r.name,p.*')->where('r.id',$id)->join('__ADMIN_PRIVS__ p','r.id=p.roleid','left')->find();
//             $data=AdminPrivs::alias('p')->where('p.roleid',$id)->join('im_admin_role r','r.id=p.roleid')->field('p.*,r.name')->find();
            $this->assign('data',$data);
            $menu=new Menu;
            if (session("menus")){
                $menus=session("menus");
            }else{
                $menus=$menu->getMenus();
                session("menus",$menus);
            }
            $this->assign('privs',explode(',',$data['menus']));
            $this->assign('menus',$menus);
            return view();
        }
        
    }
}