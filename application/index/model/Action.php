<?php
namespace app\index\model;


class Action extends\think\Model{
	protected $pk = 'id';




    /**
     * 获取全部可设置权限
     * @return array
    */
    public function _getallAction(){
        return $this->select();
    }

}