<?php
namespace app\common\logic;
use app\common\model\News as newsDB;
class NewsLogic{
    
    public function getNewsInfo($nid=""){
        $news=newsDB::get(['id'=>$nid]);
        return $news;
    }
}