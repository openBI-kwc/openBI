<?php
namespace app\index\Controller;

use think\Db;
use \app\index\controller\Screen;

/**
 * 自动刷新
 */
class Websocket
{
    //处理图表数据类
    protected $screen;

    //构造函数用来实例化基础对象
    public function __construct()
    {
        if(empty($this->screen)) {
            $this->screen = new Screen();
        }
    }
    //主程序
    public function index()
    {
        $input = input('get.');
        //判断数据是否接收成功
        if(!$input) {
            return get_status(1,'数据接收失败',000);
        }
        $id = intval($input['id']);
        //通过ID找到大屏对应的图表
        // $charts = Db::name('screenchart')->where('screenid', $id)->field('tid,tconfig')->select();
        $charts = Db::name('screenchart')->field('screencharttconfig.dataOpt,screenchart.tid')->alias('screenchart')->join('screencharttconfig','screenchart.tid=screencharttconfig.tid')->where('screenchart.screenid', $id)->select();
        //判断charts是否为空
        if(empty($charts)) {
            return  get_status(0,[]);
        }
        //将自动刷新的图表ID提取出来
        $autoChart = $this->getAutoChart($charts);
        //判断autoChartID是否为空
        if(empty($autoChart)) {
            return  get_status(0,[]);
        }
        //处理自动刷新获取返回值
        $autuResult = $this->autoChart($autoChart);
        //直接返回数据无需判断
        return get_status(0,$autuResult);
    }

    /**
     * 提取自动刷新图表
     */
    protected function getAutoChart( array $charts)
    {
        //声明自动刷新图表ID集合数组
        $autu = [];
        $i = 0;
        //遍历图表数组
        foreach($charts as $key => $value) {
            //反序列化config
            // $config = json_decode($value['tconfig'],1);
            $value['dataOpt'] = json_decode($value['dataOpt'],1);
            if( isset($value['dataOpt']['source']['type']) && isset($value['dataOpt']['autoUpdate'])) {
                //拿到是否自动刷新字段
                $isAotu = $value['dataOpt']['autoUpdate'];
                //数据源类型部位websocket
                $type = $value['dataOpt']['source']['type'];
                //判断是否自动刷新
                if($isAotu && strtolower($type != 'websocket')) {
                    //将自动刷新图表ID传入
                    $autu[$i]['tid'] = $value['tid'];
                    //自动刷新开始时间
                    $autu[$i]['autoupdatetime'] = $value['dataOpt']['clockStart'];
                    //自动刷新秒数
                    $autu[$i]['timeClock'] = $value['dataOpt']['timeClock'];
                    $i++;
                }
            }
        }
        //返回自动刷新图表ID集合
        return $autu;
    }

    /**
     * 处理自动刷新
     *  */
    protected function autoChart( array $autoChart) 
    {   
        //声明自动刷新图表数据
        $autuChartData = [];
        //遍历自动刷新数组
        foreach($autoChart as $key => $value) {
            //计算出当前时间与自动刷新开始时间差
            $chartDiff = intval(time() - $value['autoupdatetime']);
            //判断是否符合自动刷新时间
            if ($chartDiff % $value['timeClock'] == 0) {
                //执行图表数据获取方法
                $result = $this->screen->getAllChart(['chartid' => $value['tid']]);
                //提取返回数组中的图表名称及相对应的value
                foreach ($result['data'] as $k => $v) {
                    //判断是否有数据
                    if(!empty($v)) {
                        //获取图表数据
                        $autuChartData[$k] = $v;
                    }
                    
                }
            }
        }
        //返回图表数据集合
        return $autuChartData;
    }


}