<?php
namespace app\index\controller;
use Think\Controller;
use Think\Db;
use app\index\model\Menu as adminMenu;
class System extends Common
{
    /*用户列表*/
    public function index(){
        $pageParam    = ['query' =>[]];
        $ser['pid']=input('pid',0);

        $where['pid']=array('eq',$ser['pid']);
        $pageParam['query']['pid'] = $ser['pid'];
        $ser['name']=input('name','');
        if($ser['name']){
            $where['name']=array('like','%'.$ser['name'].'%');
            $pageParam['query']['name'] = $ser['name'];
        }
        $this->assign('ser',$ser);
        $data=adminMenu::where($where)->paginate(15, false, $pageParam);
        $this->assign('data',$data);
        return view();
    }
    /*添加项目*/
    public function MenuAdd(){
        if (request()->isPost()){
            $post_data=input('post.');
            $data=array(
                'name'=>$post_data['name'],
                'pid'=>$post_data['pid'],
                'sort'=>$post_data['sort'],
                'm'=>'Index',
                'c'=>$post_data['c'],
                'a'=>$post_data['a'],
            );
            if (adminMenu::where('name',$data['name'])->where('pid',$data['pid'])->find())$this->error('该菜单下已经包含相同名称的菜单');
            $res=adminMenu::insert($data);
            if($res!==false){
                session("mainMenu",null);
                session("subMenu",null);
                session("menus",null);
                $this->success('添加菜单成功');
            }else{
                $this->error('添加菜单失败');
            }
        }else{
            $data=adminMenu::where('pid','0')->select();
            $this->assign('data',$data);
            return view();
        }
        
    }
    /*编辑项目*/
    public function MenuEdit(){
        $id=input('id');
        if (request()->isPost()){
            $post_data=input('post.');
            $data=array(
                'name'=>$post_data['name'],
                'pid'=>$post_data['pid'],
                'sort'=>$post_data['sort'],
                'm'=>'Index',
                'c'=>$post_data['c'],
                'a'=>$post_data['a'],
            );
            $res=adminMenu::where('id',$id)->update($data);
            if($res!==false){
                session("mainMenu",null);
                session("subMenu",null);
                session("menus",null);
                $this->success('编辑菜单成功');
            }else{
                $this->error('编辑菜单失败');
            }
        }else{
            if($id){
                $info=adminMenu::where('id',$id)->find();
                $this->assign('info',$info);
            }
            $data=adminMenu::where('pid','0')->select();
            $this->assign('data',$data);
            return view();
        }
        
    }
    /*删除项目*/
    public function delMenu(){
        $id=input('id');
        if($id){
            $res=adminMenu::where('id',$id)->delete();
            if($res){
                session("mainMenu",null);
                session("subMenu",null);
                session("menus",null);
                $this->success('删除成功');
            }else{
                $this->error('删除失败');
            }
        }
    }
	
	/*文档列表*/
    public function document(){
        $pageParam    = ['query' =>[]];

        $where['d.id']=array('neq',0);
        $pageParam['query']['id'] = 0;
        $ser['title']=input('title','');
        if($ser['title']){
            $where['title']=array('like','%'.$ser['title'].'%');
            $pageParam['query']['title'] = $ser['title'];
        }
        $this->assign('ser',$ser);
        $data=db('document')->alias('d')->where($where)->paginate(15, false, $pageParam);
        echo db('document')->getlastsql();
        $this->assign('data',$data);
        return view();
    }

    /*添加文档*/
    public function DocumentAdd(){
        if (request()->isPost()){
            $post_data=input('post.');
            $data=array(
                'title'=>$post_data['title'],
                'content'=>$post_data['content'],
                '_key'=>$post_data['_key'],
                'userId'=>session('member')['id'],
                'auth'=>session('member')['username'],
            );
            $res=db('document')->insert($data);
            if($res!==false){
                $this->success('添加文档成功');
            }else{
                $this->error('添加文档失败');
            }
        }else{
            return view();
        }
    }
    /*编辑文档*/
    public function DocumentEdit(){
        $id=input('id');
        if (request()->isPost()){
            $post_data=input('post.');
            $data=array(
                'title'=>$post_data['title'],
                'content'=>$post_data['content'],
                '_key'=>$post_data['_key'],
                'userId'=>session('member')['id'],
                'auth'=>session('member')['username'],
            );
            $res=db('document')->where('id',$id)->update($data);
            if($res!==false){
                $this->success('编辑文档成功');
            }else{
                $this->error('编辑文档失败');
            }
        }else{
            $info=db('document')->where('id',$id)->find();
            $this->assign('info',$info);
            return view();
        }
    }

    /*删除文档*/
    public function delDocument(){
        $id=input('id');
        if($id){
            $res=db('document')->where('id',$id)->delete();
            if($res){
                $this->success('删除成功');
            }else{
                $this->error('删除失败');
            }
        }
    }
	/*小程序列表*/
    public function program(){
        $pageParam    = ['query' =>[]];

        $where['p.id']=array('neq',0);
        $pageParam['query']['id'] = 0;
        $ser['name']=input('name','');
        if($ser['name']){
            $where['p.name']=array('like','%'.$ser['name'].'%');
            $pageParam['query']['name'] = $ser['name'];
        }
        $this->assign('ser',$ser);
        $data=db('program')->alias('p')->where($where)->paginate(15, false, $pageParam);
        $this->assign('data',$data);

        return view();
    }
    /*编辑小程序*/
    public function programEdit(){
        $id=input('id');
        if (request()->isPost()){
            $post_data=input('post.');
            $data=array(
                'url'=>$post_data['url'],
            );
            $res=db('program')->where('id',$id)->update($data);
            if($res!==false){
                $this->success('编辑小程序成功');
            }else{
                $this->error('编辑小程序失败');
            }
        }else{
            $info=db('program')->where('id',$id)->find();
            $this->assign('info',$info);
            return view();
        }
    }

    /*删除小程序*/
    public function delProgram(){
        $id=input('id');
        if($id){
            $res=db('program')->where('id',$id)->delete();
            if($res){
                $this->success('删除成功');
            }else{
                $this->error('删除失败');
            }
        }
    }

    /*系统设置*/
    public function config(){
        $pageParam    = ['query' =>[]];

        $data=db('sysConfig')->paginate(15, false, $pageParam);
        $this->assign('data',$data);

        return view();
    }

    /*设置开启状态*/
    public function checkVal(){
        $id=input('id');
        $val=input('val');
        $data=array(
            'id'=>$id,
            '_value'=>$val,
        );
        $res=db('sysConfig')->update($data);
        if($res!==false){
            $json['code']=1;
            $json['msg']='设置成功';
            if($val==1) $json['html']='<a alt="点击关闭" style="color: #4db14d" onclick="checkVal('.$id.',0)"><span class="am-icon-check-circle-o"></span></a>';
            else $json['html']='<a alt="点击开启" style="color: red" onclick="checkVal('.$id.',1)"><span class="am-icon-times-circle-o"></span></a>';
            echo json_encode($json);
        }else{
            $this->error('设置失败');
        }
    }

    /*设置启用状态*/
    public function checkSta(){
        $id=input('id');
        $sta=input('sta');
        $data=array(
            'id'=>$id,
            'status'=>$sta,
        );
        $res=db('sysConfig')->update($data);
        if($res!==false){
            $json['code']=1;
            $json['msg']='设置成功';
            if($sta==1) $json['html']='<a alt="点击关闭" style="color: #4db14d" onclick="checkSta('.$id.',0)"><span class="am-icon-check"></span></a>';
            else $json['html']='<a alt="点击启用" style="color: red" onclick="checkSta('.$id.',1)"><span class="am-icon-times"></span></a>';
            echo json_encode($json);
        }else{
            $this->error('设置失败');
        }
    }
}