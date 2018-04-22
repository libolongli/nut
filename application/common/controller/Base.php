<?php
/**
 * 基础控制器
 * 处理json输出
 * 页面展示
 */
namespace app\common\controller;
use app\common\util\BaseUtil;
use think\Request;
use app\common\model\User;
use think\Controller;
class Base extends \think\Controller{

    protected $mid;
    
    protected function _initialize(){
        $userId=input('userId','');
        if (!empty($userId)){
            $this->_initUser($userId);
        }
    }

    protected function _initUser($userId){
        if (!session('apiuser')||session('apiuser')['id']!=$userId){
            $user=User::where('id',$userId)->field('id,pwd')->find();
            if ($user){
                $this->mid=$userId;
                session('apiuser',$user);
            }
        }
    }

    /**
     * 验证用户存在
     * @param unknown $userId
     */
    protected function _userAuth($userId){
        $user=User::where('id',$userId)->field('id,pwd')->find();
        if (empty($user)){
            BaseUtil::createFail('用户不存在');
        }else {
            $this->mid=$userId;
        }
    }

    /**
     * 验证好友关系
     * @param unknown $userId
     * @param unknown $friendId
     */
    protected function _userAuthFriend($userId,$friendId){
        $user=User::where('id',$userId)->field('id,pwd')->find();
        if ($user){
            BaseUtil::createFail('用户不存在');
        }
    }
}