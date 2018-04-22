<?php
namespace app\wap\controller;
use app\common\model\News;

class Index {
    
    public function index(){
        return view();
    }
    
    public function newsLists(){
        $news=News::field("title,content,createTime,type,id as nid")->where("is_delete",0)->order("createTime desc")->paginate();
        if ($news->items()){
            foreach ($news->items() as &$value){
                $value['content'] = strip_tags($value['content']);
                $value['content'] = mb_strlen($value['content'])>15?mb_substr($value['content'], 0,15)."...":$value['content'];
                $value['title'] = mb_strlen($value['title'])>12?mb_substr($value['title'], 0,12)."...":$value['title'];
            }
        }
        return json($news);
    }
}