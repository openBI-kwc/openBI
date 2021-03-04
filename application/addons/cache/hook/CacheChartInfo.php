<?php
/** 
 * 缓存图表信息
 */
namespace app\addons\cache\hook;
use app\addons\cache\model\Screenchart;
use app\addons\cache\Common;

class CacheChartInfo
{
    public function run($response)
    {
        $tid = input('param.tid');
        // 临时使用，后期修改
        if (!$tid) {
            self::cacheAll();
        } else {
            $where = ['tid' => ['>=', $tid]];
            self::cacheMore($where);
        }
        
    }

    public static function cacheMore($where)
    {
        $allChart = Screenchart::getChartInfo($where);
        foreach ($allChart as $chart) {
            self::cacheChart($chart);
        }
    }
    public static function cacheAll()
    {
        $allChart = Screenchart::getAllChart();
        foreach ($allChart as $chart) {
            self::cacheChart($chart);
        }
    }
    // 缓存单图表
    public static function cacheSingle($tid)
    {
        $chart = Screenchart::getSingleChart($tid);
        self::cacheChart($chart);
    }
    // 缓存大屏图表
    public static function cacheScreen($screenid)
    {
        $allChart = Screenchart::getScreenchart($screenid);
        foreach ($allChart as $chart) {
            self::cacheChart($chart);
        }
        self::cacheScreen($allChart);
    }
    // public function getChartCache($tid)
    // {
    //     return \think\Cache::get(Common::getTidDataCacheName($chart['tid']));
    // }
    // public function getScreenCache($screenid)
    // {
    //     return \think\Cache::get(Common::getScreenidDataCacheName($chart['screenid']));
    // }

    // 缓存单一图表
    protected static function cacheChart($chart)
    {
        \think\Cache::set(Common::getTidDataCacheName($chart['tid']), $chart->toArray());
    }
    // 缓存大屏图表
    protected static function cacheScreenchart()
    {
        \think\Cache::set(Common::getScreenidDataCacheName($chart['screenid']), collection($chart)->toArray());
    }
}