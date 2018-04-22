<?php
/**
 * 实现服务接口调用
 */
namespace app\index\logic;
class Api{
    
    const api_host="http://103.4.112.25:8080/nut";
    
    const frozeGroup="/api/frozeGroup";
    
    const dissGroup="/api/dissGroup";
    
    const appKey="ced1e7c01d60b1e62712b1f91cf7fb57";
    
    public static function apiRequest($api='',$data=[]){
        $url=self::api_host.$api;
        $data['time']=time();
        $data['sign']=self::makeSign($data);
        $res=self::curl_post_https($url, $data);
        $result=json_decode($res,true);
        return $result;
    }
   
    private static function makeSign($data=[]){
        $signStr="";
        if ($data){
            ksort($data);
            foreach ($data as $key =>$val){
                $signStr.="&$key=$val";
            }
        }
        $signStr.="&key=".self::appKey;
        return md5(ltrim($signStr,"&"));
    }
    
    
    private static function curl_post_https($url,$data){ // 模拟提交数据函数
        \think\Log::write($url) ;//捕抓异常
        \think\Log::write(json_encode($data)) ;//捕抓异常
        $curl = curl_init(); // 启动一个CURL会话
        curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
//         curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
//         curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 1); // 从证书中检查SSL加密算法是否存在
//         curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器
//         curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
//         curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
        curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data)); // Post提交的数据包
        curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
        curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
        $tmpInfo = curl_exec($curl); // 执行操作
                if (curl_errno($curl)) {
                    \think\Log::write('Errno'.curl_error($curl)) ;//捕抓异常
                }
        curl_close($curl); // 关闭CURL会话
        return $tmpInfo; // 返回数据，json格式
    }
}