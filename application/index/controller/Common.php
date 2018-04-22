<?php
namespace app\Index\controller;

use think\Controller;
//use app\index\model\AdminLog;
use app\index\model\Menu;
use app\index\model\AdminPrivs;
class Common extends Controller {
    protected $user_id;
    protected $user_name;
    
    protected $noLoginArr=['index/index/index','index/login/index'];
    
    protected $url='';
    
    public function __construct(\think\Request $request = null) {
        parent::__construct($request);
        $this->url=strtolower($this->request->module().'/'.$this->request->controller().'/'.$this->request->action());
        if (in_array($this->url, $this->noLoginArr)){
           
        }else {
            if (!$this->checkLogin()){
                $this->error('请登陆', '/login', 0);
            }else{
                $this->user_id = session('member')['id'];
                $this->user_name = session('member')['username'];
                @self::optRecord();
                //权限检查
                $AdminPrivs=new AdminPrivs;
                $c = strtolower(request()->controller());
                $a = strtolower(request()->action());
                if (!$AdminPrivs->getAction(session('member')['roleid'],$c,$a)) {
                    $this->error('你无权限操作','/');
                }
            }
        }
    }    
    
    private function checkLogin(){
        $cookie=json_decode(cookie('member'),true);
        if (isset($cookie['id'])&&!$cookie['id']){
            session('member',null);
            cookie('member',null);
            return false;
        }else{
            cookie('member',cookie('member'));
            return true;
        }
    }
    
    public function optRecord(){
        if (!db('menu')->where("m",request()->module())->where("c",request()->controller())->where("a",request()->action())->find()){
            $log=[
                'userId'=>$this->user_id,
                'username'=>$this->user_name,
                'm'=>request()->module(),
                'c'=>request()->controller(),
                'a'=>request()->action(),
                'params'=>json_encode(request()->param())
            ];
            db('AdminOptLog')->insert($log);
        }
        
    }

}
