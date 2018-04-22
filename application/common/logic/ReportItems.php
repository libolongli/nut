<?php
namespace app\common\logic;
use app\common\params\Constants;
class ReportItems extends \think\Model{

    public function getTree($pid=0,$type='user',$lang='cn'){
        session(Constants::$REPORT_SESSION_KEY.$type."-".$lang,null);
        if (session(Constants::$REPORT_SESSION_KEY.$type."-".$lang))return session(Constants::$REPORT_SESSION_KEY.$type."-".$lang);
        $list=$this->where('pid',$pid)->where('belong',$type)->select();
        if ($list){
            foreach ($list as &$left){
                $left['child']=$this->getTree($left['id']);
            }
        }
        session(Constants::$REPORT_SESSION_KEY.$type."-".$lang,$list);
        return session(Constants::$REPORT_SESSION_KEY.$type."-".$lang);

    }

    public function getChildByPid($pid,$type='user',$lang='cn'){
        $list=session(Constants::$REPORT_SESSION_KEY.$type."-".$lang);
        if ($list){
            return $this->getChildFromList($pid, $list);
        }
        return false;
    }

    private function getChildFromList($pid,$array){
        if ($array){
            foreach ($array as $a){
                if ($a['id']==$pid){
                    return $a['child'];
                }else {
                    if ($a['child']){
                        return $this->getChildFromList($pid, $a['child']);
                    }else {
                        continue;
                    }
                }
            }
        }else {
            return false;
        }
    }
}