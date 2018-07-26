<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件

// 密码生成器
function password($password,$type=0,$salt=''){
    $return='';
    $str='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    if ($salt==''){
        for ($i=0;$i<6;$i++){
            $salt.=substr($str, rand(0, strlen($str)-1),1);
        }
    }
    switch ($type){
        case 0:$return=md5($password);break;
        case 1:$return=md5($password.$salt);break;
        default:$return=md5(md5($password).$salt);break;
    }
    return array('password'=>$return,'salt'=>$salt);
}

/*时间戳装换日期*/
function transformTime($str){
    $len=strlen($str);
    if($len>10){
        $time=date('Y-m-d H:i:s',substr($str, 0,10));
    }else{
        $time=date('Y-m-d H:i:s',$str);
    }
    return $time;
}

/*资金别动类型*/
function moneyType(){
    return ;
}

/*充值状态*/
function orderStatus($status){
    $array=array(
        '0'=>'<span style="color:#C1C1C1">未支付</span>',
        '1'=>'<span style="color:green">充值成功</span>',
        '2'=>'<span style="color:#ccc">已取消</span>',
    );
    return $array[$status];
}

/*  数字规则匹配*/
function numberRule($num){
    $res="";
    $match=array();
    // 6到3位 顺
    for ($i=5;$i>1;$i--){
        $patternr="/(?:0(?=1)|1(?=2)|2(?=3)|3(?=4)|4(?=5)|5(?=6)|6(?=7)|7(?=8)|8(?=9)){{$i}}\d|(?:9(?=8)|8(?=7)|7(?=6)|6(?=5)|5(?=4)|4(?=3)|3(?=2)|2(?=1)|1(?=0)){{$i}}\d/";
        if (preg_match_all($patternr, $num,$match)){
            $res.="pairs-".($i+1)."r;";
            if ($match&&isset($match[0])){
                foreach ($match[0] as $m){
                    $res.="order-$m;";
                }
            }
            break;
        }
    }
    // 大于3位重复
    $pattern='/([\d])\1{2,}/';
    if (preg_match_all($pattern, $num,$match)){
        if ($match&&isset($match[0])){
            foreach ($match[0] as $m){
                $res.="pairs-".(strlen($m))."A;";
                $res.="pairs-$m;";
            }
        }
    }
	
	//AABB
    $pattern2='/([\d])\1{1,}([\d])\2{1,}/';
    if (preg_match_all($pattern2, $num,$match)){
        if ($match&&isset($match[0])){
			$res.="pairs-AABB;";
            foreach ($match[0] as $k=>$m){
                $res.="pairs-$m;";
            }
        }
    }
    //ABCABC
    $pattern3='/^(\d)(\d)(\d)\1\2\3$/';
    if (preg_match_all($pattern3, $num,$match)){
        if ($match&&isset($match[0])){
			$res.="pairs-ABCABC;";
            foreach ($match[0] as $k=>$m){
                $res.="pairs-$m;";
            }
        }
    }
	
	if (!empty($res)){
        $ch=97;
        $str="";
        $j=0;
        $a3=substr_count($res, "pairs-3A");
        for ($i=0;$i<$a3;$i++){
            $str.=str_pad(chr($ch+$i), 3,chr($ch+$i),STR_PAD_RIGHT);
            $j=$i;
        }
        if (!empty($str))$res.="pairs-$str;";
        $ch=97;
        $str="";
        $a4=substr_count($res, "pairs-4A");
        for ($i=0;$i<$a4;$i++){
            $str.=str_pad(chr($ch+$i+$j), 3,chr($ch+$i+$j),STR_PAD_RIGHT);
        }
        if (!empty($str))$res.="pairs-$str;";
    }
    $res.="len-".strlen($num).";";
    return $res;
}
/**
 * 1充值2提现3发红包4收红包5发转账6收转账7红包退回8转账退回 9 帐号购买 10 AA收款 11 AA 付款
 * @param unknown $moneyType
 * @return string
 */
function walletType($moneyType){
    $str="";
    switch ($moneyType){
        case 1:$str='<span>充值</span>';break;
        case 2:$str='<span>提现</span>';break;
        case 3:$str='<span>发红包</span>';break;
        case 4:$str='<span>收红包</span>';break;
        case 5:$str='<span>发转账</span>';break;
        case 6:$str='<span>收转账</span>';break;
        case 7:$str='<span>红包退回</span>';break;
        case 8:$str='<span>转账退回</span>';break;
        case 9:$str='<span>帐号购买</span>';break;
        case 10:$str='<span>AA收款</span>';break;
        case 11:$str='<span>AA付款</span>';break;
		case 15:$str='<span>用户列表扣款</span>';break;
		case 16:$str='<span>提现拒绝</span>';break;
        default:$str='<span>未知类型</span>';
    }
    return $str;
}
