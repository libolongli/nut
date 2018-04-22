<?php
/**
 * 基础控制器
 * 处理json输出
 * 页面展示
 */
namespace app\common\util;
class BaseUtil extends \think\Controller{
    /**
     * 输出错误信息
     * @param unknown $str
     */
    static function createFail($str=''){
        $err=(object)[
            'code'=>-1,
            'data'=>['info'=>$str]
        ];
        header("Content-type:application/json");
        die(json_encode($err));
    }

    static function createSimpleSuccess($succTipContent,$code=1){
        $obj=[
            'info'=>$succTipContent,
        ];
        header("Content-type:application/json");
        die(json_encode(self::getResponse($code,$obj)));
    }

    static function createSuccessData($obj){
        header("Content-type:application/json");
        die(json_encode(self::getResponse(1,$obj)));
    }

    static function getResponse($code, $content)
    {
        $data=[
            'code'=>(int)$code,
            'data'=>$content
        ];
        return $data;
    }

    public function createHtmlError($str='',$header){
        $str=empty($str)?lang('发生未知错误'):lang($str);
        $this->error($str);
    }
}