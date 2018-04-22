<?php
namespace app\home\controller;
class News extends Apps{
    
    public function info(){
        $id=input("nid","");
        $news=controller("common/NewsLogic","logic")->getNewsInfo($id);
        if (!$news){
            $this->displayError();
        }else{
            $this->assign("info",$news);
            return $news['type']==1?view('news/notice'):view();
        }
    }
}