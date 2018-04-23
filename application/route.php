<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

return [
    '__pattern__' => [
        'name' => '\w+',
    ],
    '[hello]'     => [
        ':id'   => ['index/hello', ['method' => 'get'], ['id' => '\d+']],
        ':name' => ['index/hello', ['method' => 'post']],
    ],
	'[speech]'=>[
		'recive'=>'index/speech/recive',
	],
    
    // 后台
    'login'=>'index/login/index',
    '/'=>'index/index/index',
    
    '[news]'=>[
        '/:nid'=>'home/news/info',
        '/'=>'home/news/page_404',
    ],
    '[wap]'=>[
        'news'=>'wap/index/index',
        'reportG'=>'wap/report/reportG',
        'reportFriend'=>'wap/report/reportFriend',
        'getReport'=>'wap/report/getReport',
    ],
    '[chat]'=>[
        'feedback_submit'=>'feedback/index/feedback_submit',
    ]
    

];
