<?php
namespace app\index\model;


class AdminPrivs extends\think\Model{
	protected $pk = 'id';


	/**
	 * 判断当前操作方法是否有权限
	 * @param user_id 用户ID
     * @return array
	*/
	public function getAction($roleid,$c,$a){
		if (!$roleid) {
            return false;
        }
        $mid=db('menu')->where('c',$c)->where('a',$a)->value('id');
		$dca=self::getPrivs(session('roleid'));
		$menus=explode(',',$dca);
		
        if($dca=='all' || $dca==''){
            return true;
        }
        $c = strtolower(request()->controller());
        $a = strtolower(request()->action());
        if ($c == 'index' && $a == 'index') {
            return true;
        }
		
		if(!empty($menus)){
			if(in_array($mid,$menus)){
				return true;
			}
			else{
				return false;
			}
		}else{
			return false;
		}

	}
	/**
	 * 获取指定角色的权限
	 * @param role_id 角色ID
     * @return array
	*/
	public function getPrivs($role_id){
	    $adminprivs=$this->where('roleid',$role_id)->value('menus');
        return $adminprivs;
	}

	/**
	 * 获取指定父级菜单下的子菜单与管理员菜单的交集
	 * @param pid 父级菜单ID
     * @return array
	*/
	public function getintersection($pid=0){
		$alchild=db('menu')->where('pid',$pid)->where('site',0)->column('id');
		$adminprivs=self::getPrivs(session('roleid'));
		return array_intersect($alchild,$adminprivs);
	}

}