<?php
/** 
 * 通用数据处理类
 * 将各种类型的数据转换为前端需要的数据
 */

namespace app\addons\cache\hook;

use app\addons\cache\hook\Chartdata as Chart;
use app\addons\cache\Common;
use think\Db;

class ChartProcess
{
    // 图表数据
    public $chartData = [];
    // 图标数据处理类
    public $chartModel;

    public static function process($input)
    {
        if (isset($input['chartid'])) {
            return self::processChart($input['chartid']);
        } else {
            return self::processScreen($input['id']);
        }
       
    }
    // 处理大屏
    protected static function processScreen($screenid)
    {
        $chartInfos = Db::name('screenchart')->field('tid')->where('screenid', $screenid)->select();
        // 无数据返回
        if (!$chartInfos) return [];
        $data = [];
        foreach ($chartInfos as $chartInfo) {
            $data += self::processChart($chartInfo['tid']);
        }
        return $data;
    }
    // 单图表处理
    public static function processChart($tid)
    {
        //根据id从缓存里拿到图表数据
        $chartCache = Common::getChartCache($tid);
        $data[$chartCache['tname']] = self::processSingleChart($chartCache);
        return $data;
    }
    protected static function processSingleChart($chartCache)
    {   
        // 实时数据
        $realTimeData = self::getRealTimeData($chartCache);
        if (!isset($realTimeData[0]) && $chartCache['chartSourceType']) return $chartCache['name'].'无法获取数据';
        $mapData = self::mapping($realTimeData, $chartCache['maps']);
        if (!$mapData) return $chartCache['name'].'数据映射有误！';
        return Chart::index($chartCache['charttype'], $mapData, $chartCache['name']);
    }
    // 获取实时数据
    protected static function getRealTimeData($data)
    {
        // 图片等类型无数据类型的返回空
        if (!method_exists(__CLASS__, $data['chartSourceType'])) return $data['dataInfo'];
        // 其他有类型的
        return self::{$data['chartSourceType']}($data);
    }
    // 映射
    public static function mapping($realTimeData, $maps)
    {
        $realTimeData = is_array($realTimeData) ? $realTimeData : json_decode($realTimeData, true);
        if (!$maps) return $realTimeData; 
        foreach ($realTimeData as &$value) {
            foreach ($maps as $newKey => $oldKey) {
                if (!isset($value[$oldKey])) return false;
                $value[$newKey] = $value[$oldKey];
                unset($value[$oldKey]);
            }
        }
        return $realTimeData;
    }

    protected static function static($data)
    {
        return $data['tdata'];
    }
    protected static function api($data)
    {
        try{
            //直接从API里面取值
            $results = file_get_contents($data['filepath'] , false , stream_context_create(array(
                "ssl"=>array(
                    "verify_peer"=>false,
                    "verify_peer_name"=>false,
                    ),
                )));
            //如果返回值为XML则转成json
            if(xml_parser($results)){
                $xml =simplexml_load_string($results);
                $xmljson= json_encode($xml);
                $result=json_decode($xmljson,true);
            }else{
                $result=json_decode($results,true);
            }
            
            return $result;
        } catch (\Exception $e) {
            return false;
        }
    }
    protected static function sql($data)
    {
        //通过数据库配置ID拿到数据库配置
        $config = Db::name('databasesource')->where('baseid' , $data['sid'])->find();
        //判断是否查询成功
        if (!$config) return false;
        $config = json_decode($config['baseconfig'],1);
        $config['password'] = decrypt($config['password'],$config['len']);
        //连接数据库执行sql
        return \DataSource::connect($config)->query($data['returnsql']);
    }
    protected static function webSocket($data)
    {
        try{
            //直接从API里面取值
            $results = file_get_contents($data['filepath'] , false , stream_context_create(array(
                "ssl"=>array(
                    "verify_peer"=>false,
                    "verify_peer_name"=>false,
                    ),
                )));
            //如果返回值为XML则转成json
            if(xml_parser($results)){
                $xml =simplexml_load_string($results);
                $xmljson= json_encode($xml);
                $result=json_decode($xmljson,true);
            }else{
                $result=json_decode($results,true);
            }
            return $result;
        } catch (\Exception $e) {
            return false;
        }
    }
}