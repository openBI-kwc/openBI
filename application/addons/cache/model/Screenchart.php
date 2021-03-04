<?php

namespace app\addons\cache\model;

use think\Model;

class Screenchart extends Model
{
    public function charttconfig()
    {
        return $this->hasOne('Screencharttconfig','tid', 'tid')->bind('name,chartSourceType,charttype,dataOpt,maps');
    }
    public function datament()
    {
        return $this->hasOne('Datament','daid', 'daid')->bind('filepath,datatype,returnsql,sid,dataInfo');
    }


    // 获取所有图表数据
    public static function getAllChart()
    {
        return self::with('charttconfig,datament')->field('daid,screenid,tid,tname,tdata')->select();
    }
    // 获取单个图表数据
    public static function getSingleChart($tid)
    {
        return self::with('charttconfig,datament')->field('daid,screenid,tid,tname,tdata')->where('tid', $tid)->find();
    }
    // 获取大屏图表数据
    public static function getScreenChart($screenid)
    {
        return self::with('charttconfig,datament')->field('daid,screenid,tid,tname,tdata')->where('screenid', $screenid)->select();
    }
    // 由条件获取图表数据
    public static function getChartInfo($where = [])
    {
        return self::with('charttconfig,datament')->field('daid,screenid,tid,tname,tdata')->where($where)->select();
    }
    
}