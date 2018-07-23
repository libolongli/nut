<?php
namespace app\index\controller;
use Think\Controller;
use Think\Db;
use app\index\model\Menu;
use app\index\model\Admin;
use think\Request;
class Login extends \think\Controller
{
    public function index()
    {
        if (isset(cookie('member')['id'])&&(cookie('member')['id'])){
            $this->redirect('/');
        }
        return view('Login/Login');
    }
    public function login(){
        $name=input('username');
        $password=input('password');
        $captcha=input('captcha');
        if(empty($name)) $this->error('请输入管理员名称');
        if(empty($password)) $this->error('请输入登录密码');
        if(empty($captcha)) $this->error('请输入验证码');
        if(!captcha_check($captcha)){
            $this->error('亲，验证码输入错误');
        };
        $res=Admin::where('username',$name)->field('id,username,roleid')->where('password',MD5($password))->find();
        if($res){
            cookie("member",$res);
            session('member',$res);
            $request = Request::instance();
            Admin::where('id',$res['id'])->update(['last_login_ip'  => $request->ip(),'last_login_time' => time()]);
            $this->success('登录成功', '/');
        }else{
            $this->error('登录失败，用户名或密码错误');
        }
    }
    /**
     * 登出
     */
    public function loginout() {
        session('member', null);
        cookie("member",null);
        $this->success('退出成功', '/login');
    }
}