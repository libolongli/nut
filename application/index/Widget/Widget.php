<?php
namespace app\index\widget;
use think\Controller;
use think\Db;
use app\index\model\Menu;
use app\index\model\AdminPrivs;
class Widget extends Controller
{
     public function menu()
    { 
        if(session("mainMenu")){
            $list=session("mainMenu");
        }else {
            $list=Menu::where('pid','eq',0)->select();
            session("mainMenu",$list);
        }
        $html = '<div class="left-sidebar">';
        $html .='<div class="tpl-sidebar-user-panel">';
        $html .='<div class="tpl-user-panel-slide-toggleable">';
        $html .='<span class="user-panel-logged-in-text">';
        $html .='<i class="am-icon-circle-o am-text-success tpl-user-panel-status-icon"></i>'.session('member')['username'];
        $html .='</span>';
        $html .='<a href="javascript:;" class="tpl-user-panel-action-link"> <span class="am-icon-pencil"></span> 账号设置</a>';
        $html .='</div>';
        $html .='</div>';
        $html .='<ul class="sidebar-nav">';
        $html .=self::menu_tree($list);
        $html .='</ul>';
        $html .='</div>';
        return $html;
    }
    public function menu_tree($tree){
        $html = '';
        if (is_array($tree)) {
            $c = strtolower(request()->controller());
            $a = strtolower(request()->action());
            $userAction=new AdminPrivs;
            $menu=new Menu;
            $pid=$menu::where("m",request()->module())->where("c",request()->controller())->where("a",request()->action())->where("pid",'neq',0)->value('pid');
            foreach ($tree as $k=>$val) {   
                $num=0;
                $childList=$menu->child_menu($val['id']);
                if($childList){
                    if (session("privsMenus")){
                        $uac=session("privsMenus");
                    }else {
                        $uac=$userAction->where('roleid',session('member')['roleid'])->value('menus');
                        session("privsMenus",$uac);
                    }
                    if($uac=='all' || $uac==''){
                        $num++;
                    }else{
                        //返回指定父级菜单下的子菜单与管理员菜单的交集
                        $check = $userAction->getintersection($val['id']);
                        if(!empty($check)){
                            $num++;
                        }
                    }                    
                }
                if($num>0){
                    if(isset($val['name'])) $title=$val['name']; 
                    $html.='<li class="sidebar-nav-link">';
                    $selected='';
//                     if($c==strtolower($val['c'])){
//                         $selected="action";
//                     }
                    if ($pid&&$pid==$val['id'])$selected="action";
                    $html.='<a href="javascript:;" class="sidebar-nav-sub-title '.$selected.'">';
                    $icon=empty($val['icon'])?"navicon":$val['icon'];
                    $html.='<i class="am-icon-'.$icon.' sidebar-nav-link-logo"></i> '.$title;
                    $html.='<span class="am-icon-chevron-down am-fr am-margin-right-sm sidebar-nav-sub-ico"></span>';
                    $html.='</a>';
                    if ($pid&&$pid==$val['id']) $html.='<ul class="sidebar-nav sidebar-nav-sub" style="display: block;">';
                    else $html.='<ul class="sidebar-nav sidebar-nav-sub">';
                    $childList=$menu->child_menu($val['id']);
                    if($childList){
                        foreach ($childList as $v) {
                            $html.='<li class="sidebar-nav-link">';
                            $url='';
                            if (!empty($v["a"])) $url.= url( $v['c'] . '/' . $v['a']);
                            $current='';
                            if($c==strtolower($v['c']) && $a==strtolower($v['a'])) $current='sub-active';
                            /*else{
                                if($c==strtolower($v['c']) && $a==strtolower($v['a'])) $current='sub-active';
                            }*/
                            $html.='<a href="'.$url.'" class="'.$current.'">';//onclick=loading(this,"'.$url.'")
                            if(isset($v['name'])) $childtitle=$v['name'];
                            $html.='<span class="am-icon-angle-right sidebar-nav-link-logo"></span> '.$v['name'];
                            $html.='</a>';
                            $html.='</li>';
                        }                    
                    }
                    $html.='</ul>'; 
                    $html.='</li>';  
                }       
            }

            /*foreach ($tree as $k=>$val) {
                if($val['isnum']>0){
                    if(isset($val['name'])) $title=$val['name']; 
                    $html.='<li class="sidebar-nav-link">';
                    $selected='';
                    if($c==strtolower($val['c'])) $selected="action";
                    $html.='<a href="javascript:;" class="sidebar-nav-sub-title '.$selected.'">';
                    $html.='<i class="'.$val['icon'].' sidebar-nav-link-logo"></i> '.$title;
                    $html.='<span class="am-icon-chevron-down am-fr am-margin-right-sm sidebar-nav-sub-ico"></span>';
                    $html.='</a>';
                    if($c==strtolower($val['c'])) $html.='<ul class="sidebar-nav sidebar-nav-sub" style="display: block;">';
                    else $html.='<ul class="sidebar-nav sidebar-nav-sub">';
                    $childList=$menu->child_menu($val['id']);
                    if($childList){
                        foreach ($childList as $v) {
                            $html.='<li class="sidebar-nav-link">';
                            $url='';
                            if (!empty($v["a"])) $url.= url($v['m']. '/' . $v['c'] . '/' . $v['a']);
                            $current='';
                            if($c==strtolower($v['c']) && $a==strtolower($v['a'])) $current='sub-active';
                            else{
                                if($c==strtolower($v['c']) && $a==strtolower($v['a'])) $current='sub-active';
                            }
                            $html.='<a href="'.$url.'" class="'.$current.'">';
                            if(isset($v['name'])) $childtitle=$v['name'];
                            $html.='<span class="am-icon-angle-right sidebar-nav-link-logo"></span> '.$v['name'];
                            $html.='</a>';
                            $html.='</li>';
                        }                    
                    }
                    $html.='</ul>'; 
                    $html.='</li>';  
                }
                     
            }*/
        }
        return $html;
    }
    public function header(){
        //$rolename=db('adminRole')->where('id','eq',session('role_id'))->where('isdelete','eq',0)->value('rolename');
        $this->assign('rolename',0);
        return $this->fetch('Widget/_header');
    }
}
