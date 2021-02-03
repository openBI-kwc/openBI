<?php
namespace app\addons\cache;

class Common
{
    public static function getTidDataCacheName($tid)
    {
        return config('addons.cachePlugin')['config']['tidChartData'].$tid;
    }
    public static function getScreenidDataCacheName($screenid)
    {
        return config('addons.cachePlugin')['config']['screenidChartData'].$screenid;
    } 

    // 获取单一图表数据
    public static function getChartCache($tid)
    {
        $data = \think\Cache::get(self::getTidDataCacheName($tid));
        if (!$data) {
            \app\addons\cache\hook\CacheChartInfo::cacheSingle($tid);
            $data = \think\Cache::get(self::getTidDataCacheName($tid));
        }
        return $data;
    }
}
