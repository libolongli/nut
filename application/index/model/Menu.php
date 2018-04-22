<?php
namespace app\index\model;


class Menu extends\think\Model{
	protected $pk = 'id';

	/**
	 * 获取全部操作菜单
	 * @param pid 
     * @return array
	*/
	public function getMenus($pid=0){
		$menus=$this->where('pid','eq',$pid)->select();
		foreach ($menus as &$value) {
			$value['child']=self::child_menu($value['id']);
		}
		return $menus;
	}

	/**
	 * 获取指定父级菜单下的子菜单
	 * @param pid 
     * @return array
	*/
	public function child_menu($pid){
	    if (session("subMenu")){
	        $subMenu=session("subMenu");
	        if (isset($subMenu[$pid])){
	            $childList=$subMenu[$pid];
	        }else{
	            $childList=$this->where('pid','eq',$pid)->select();
	            if ($childList){
	                $subMenu[$pid]=$childList;
	                session("subMenu",$subMenu);
	            }
	        }
	    }else{
	        $childList=$this->where('pid','eq',$pid)->select();
	        if ($childList){
	            $subMenu=[];
	            $subMenu[$pid]=$childList;
	            session("subMenu",$subMenu);
	        }
	    }
// 	    if ($childList){
// 	        foreach ($childList as &$value) {
// 	            $value['childs']=self::child_menu($value['id']);
// 	        }
// 	    }
		return $childList;
	}

}