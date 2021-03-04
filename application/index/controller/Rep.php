<?php
namespace app\index\Controller;

use think\Loader;

class Rep
{

    protected $screenchartModel;

    protected $oldNew = [
        "pie1" => "biaozhuduibibingtu",
        "pie2" => "daitubingtu",
        "pie3" => "danzhibaifenbibingtu",
        "pie4" => "duoweidubingtu",
        "pie5" => "jibenbingtu",
        "pie6" => "huanshanbingtu",
        "pie7" => "lunbobingtu",
        "pie8" => "mubiaozhanbibingtu",
        "pie9" => "zhibiaoduibibingtu",
        "pie10" => "zhibiaozhanbibingtu",
        "pie11" => "huanzhuangfaguangzhanbitu",
        "line1" => "daitubingtu",
        "line2" => "quyutu",
        "line3" => "quyufanpaiqi",
        "line4" => "shuangzhouzhexiantu",
        "line5" => "3dzhexiantu",
        "line6" => "xuxianzhexiantu",
        "bar1" => "banmazhutu",
        "bar2" => "chuizhijibenzhutu",
        "bar3" => "chuizhijiaonanzhutu",
        "bar4" => "fenzuzhutu",
        "bar5" => "huxingzhutu",
        "bar6" => "jibenzhutu",
        "bar7" => "shuipingjibenzhutu",
        "bar8" => "shuipingjiaonanzhutu",
        "bar9" => "tixingzhutu",
        "bar10" => "zhexianzhutu",
        "bar11" => "danzhouzhexianzhutu",
        "bar13" => "shuangxianghengxiangzhuzhuangtu",
        "bar14" => "fenzhujianbianzhuzhuangtu",
        "bar12" => "3dzhuzhuangtu",
        "scatter1" => "qipaotu",
        "scatter2" => "sandiantu",
        "scatter3" => "sandianzhexiantu",
        "scatter4" => "qipaozhexiantu",
        "graph" => "guanxiwangluo",
        "radar" => "leidatu",
        "gauge" => "yibiaopan",
        "wordCloud" => "ciyun",
        "funnel" => "loudoutu",
        "liquidFill" => "shuiqiutu",
        "gridheatmap" => "relitu",
        "boxplot" => "xiangxingtu",
        "hexian" => "hexiantu",
        "sunburst" => "xuritu",
        "rectangletree" => "juxingshutu",
        "sankey" => "sangjitu",
        "chinamap" => "zhongguoditu",
        "chinamap3d" => "3dzhongguoditu",
        "worldMap" => "shijieditu",
        "worldMap3d" => "3dshijieditu",
        "globechart" => "diqiuyi",
        "gismap" => "gismap",
        "lunbotable" => "lunbobiaoge",
        "txt" => "biaoti",
        "time" => "shijian",
        "web" => "wangye",
        "carousel" => "lunbotu",
        "richtxt" => "fuwenben",
        "video" => "shipinbofangqi",
        "counting" => "jishuban",
        "counting2" => "jishuban2",
        "txts" => "duoxingwenben",
        "diversion" => "paomadeng",
        "link" => "chaolianjie",
        "progress" => "jindutiao",
        "table" => "biaoge",
        "circleProgress" => "huanxingjindutiao",
        "image" => "tupian",
        "icon" => "icon",
        "border" => "biankuang",
        "linellae" => "xiantiao",
        "lines" => "zhixian",
    ];

    public function __construct()
    {
        $this->screenchartModel = Loader::model("Screenchart");
    }

    public function start()
    {
        //获取总数
        $num = $this->screenchartModel->getCount();
        //分页查询
        $p = 10; //每页数
        $maxPage  = ceil($num / $p);//总页数
        //循环取值
        for ($i = 1 ; $i <= $maxPage ; $i++) {
            //每次10条处理
            $this->process($this->screenchartModel->pageSelect($i , $p));
        }
    }

    public function process($arr)
    {
        //遍历数组
        foreach ($arr as $value){
            //查找charttype字符串
            $charttypeH = substr($value['tconfig'] , strpos( $value['tconfig'], "charttype" )); //截取出现位置以后的
            $charttypestring = substr($charttypeH ,0 ,strpos( $charttypeH, '","' )+1); //截取出现未知以前的
            //获取charttype图表类型
            $charttypeArr = explode(":" , $charttypestring); //将字符串分割成数组
            $charttype = str_replace('"' , "" , $charttypeArr[1]); // 去数组后一个 图表类型
            if(isset($this->oldNew[$charttype])) { //判断是否在数组中
                $charttypeValue = $this->oldNew[$charttype]; //查询新类型
                $string = str_replace( $charttype ,$charttypeValue, $charttypestring); //替换
                $json = str_replace($charttypestring ,$string ,  $value); //替换到json
                $data[] = ["tconfig" => $json]; //加入到数组
                //修改图表
                $this->screenchartModel->updateScreenChart($value['tid'] , $json);
            }
        }
    }
}