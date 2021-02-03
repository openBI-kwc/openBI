<?php

namespace app\index\Controller;

use think\Cache;
use think\Config;
use think\Db;
use think\Loader;

/**
 * Class My
 * @package app\index\Controller
 * 用于回调 socket 主动推送数据给fd
 * 静态数据接口  添加API数据源 和websocket数据源
 */
class My
{
    //dbindscenes表model
    protected  $deviceInfoModel;

    //Unityjson 表model
    protected $unityJsonModel;

    protected $arrContextOptions = array(
        "ssl"=>array(
            "verify_peer"=>false,
            "verify_peer_name"=>false,
        ),
    );

    // 构造方法
    public function __construct()
    {
        $this->deviceInfoModel = Loader::model('Deviceinfo');        //初始化$deviceInfoModel
        $this->unityJsonModel = Loader::model('Unityjson');        //初始化$unityJsonModel
    }

    public function testDb()
    {
        $config = [
            // 数据库类型
            'type'            => 'mysql',
            // 服务器地址
            'hostname'        => '192.168.30.207',
            // 数据库名
            'database'        => 'tpflow',
            //用户名
            'username'        => 'root',
            // 密码
            'password'        => 'root',
            // 端口
            'hostport'        => '3306',
        ];
        $config = [
            // 数据库类型
            'type'            => 'sqlsrv',
            // 服务器地址
            'hostname'        => '192.168.30.128',
            // 数据库名
            'database'        => 'kwcnet',
            //用户名
            'username'        => 'sa',
            // 密码
            'password'        => 'Admin123',
            // 端口
            'hostport'        => '1433',
        ];
        $config = [
            // 数据库类型
            'type'            => 'es',
            // 服务器地址
            'hostname'        => '192.168.30.119',
            // 数据库名
            'database'        => 'syslog-other',
            //用户名
            'username'        => '',
            // 密码
            'password'        => '',
            // 端口
            'hostport'        => '9200',
        ];
        $config = [
            // 数据库类型
            'type'            => 'mongo',
            // 服务器地址
            'hostname'        => '192.168.30.119',
            // 数据库名
            'database'        => 'syslog-other',
            //用户名
            'username'        => '',
            // 密码
            'password'        => '',
            // 端口
            'hostport'        => '9200',
        ];
        // dump(\DataSource::connect($config));die;
        // $obj = \DataSource::connect($config)->query('select * from wf_flow');
        // $obj = \DataSource::connect($config)->query('select * from wf_flow');

        $table = 'runoob.test';
        $filter = [];
        $options = [
            'projection' => ['_id' => 0]
        ];
        $obj = \DataSource::connect($config)->mongoQuery($table ,$filter , $options  );

        // $obj = \DataSource::connect($config)->getTables();
        // $obj = \DataSource::connect($config)->testConnection();
        // $query = [
        //     'index'=>'syslog-other',
        //     'size' => 10,
        // ];
        // $query =   [
        //     'index' =>  'syslog-other',
        //     'type' => 'doc',
        //     'body' => [
        //         'size' => 1,
        //         'aggs'=> [
        //             'group_by_grabTime'=> [
        //                 'date_histogram'=> [
        //                     'field'=> '@timestamp',
        //                     'interval'=> 'day',
        //                     'format'=> 'yyyy-MM-dd',
        //                     'time_zone'=> '+08:00',
        //                     'min_doc_count'=> 0
        //                 ]
        
        //                 ],
        //                 ],

        
        //     ]
        // ];
        // $obj = \DataSource::connect($config)->query($query);
        // dump($obj);
    }

    //静态数据
    public function index()
    {
//        //YML文件
//        //接收到的数据
//        $input = [
//            "sysname" => "kwcnet2sss.com",
//            "publish" => 1,
//            "logopath" => "./static/assets/img/logo.png"
//        ];
//        //配置文件地址
//        $paht = ROOT_PATH .'public' . DS .'openv' . DS . 'static' .DS . '_config.yml';
//        //打开文件
//        $file = fopen($paht,'r');
//        //定义文件内容存储字符串
//        $contents = "";
//        //循环文件行数
//        while (true) {
//            $content = fgets($file); //单行读取文件
//            foreach ($input as $key => $value) {
//                $start = strpos($content , $key); //计算截取前面字符串的长度
//                $keyLen = strlen($key) + 2; //+2 后面有两空格
//                if($start) {
//                    $replace = substr($content ,0,$start + $keyLen); //截取前面的字符串
//                    $end = strpos($content , "#"); //计算截取后面字符串的长度
//                    $startEnd = substr($content ,$end - 1);//截取后面的字符串 前面有一个空格
//                    $content = $replace . $value . $startEnd; //组合字符串
//                    unset($input[$key]);//匹配完成,删除键
//                }
//            }
//            $contents .= $content; //拼接内容
//            //判断是否读取到文件末尾
//            if(!$content) {
//                break;
//            }
//        }
//        //关闭文件
//        fclose($file);
//        //将文件内容写入文件
//        $result = file_put_contents($paht , $contents);
//        dump($result);
//        dump($contents);
//        die();
        $arr = [
            [
                'name' => "张三",
                'value' =>  5,
                'age' => 50
            ],
            [
                'name' =>"李四",
                'value' => 2,
                'age' => 40
            ],
            [
                'name' =>"赵六",
                'value' => 1,
                'age' => 19
            ],
            [
                'name' =>"孙七",
                'value' => 1,
                'age' => 27
            ],
            [
                'name' => "王五",
                'value' =>1,
                'age' => 37
            ]
            ];
            // dump(json_encode($arr,JSON_UNESCAPED_UNICODE));
            return $arr;
    }

    protected function yaml_parse_file($file) {
        return Spyc::YAMLLoad($file);
    }

    //添加API
    public function addapi()
    {
        $put = file_get_contents('php://input');
        //$put = '{"dataname":"xiaoni","cid":3}';
        //转换成数组
        if (empty($put)) {
            return get_status(1, '请添加相应的数据' , 4003);
         }
         $post = json_decode($put, 1);
            $name = Db::name('screengroup')->where('screenname',$post['cid'])->field('sid')->find();

            $post['ccid'] = $name['sid'];
         //判断是否已存在
         $res = Db::name('datament')->where(['dataname'=>$post['dataname']])->select();
         //不存在执行插入操作
         if (!$res) {
             $post['createtime'] = date('Y-m-d H:i:s', time());
             $result = Db::name('datament')->insert($post);
         } else {
             return get_status(2, '文件名重复' , 4006);
         }
         //返回值
         if (empty($result)) {
             return get_status(1, '添加失败',4009);
         } else {
             return get_status(0, '添加成功');
         }
    }

    //添加websocket
    public function addSocket()
    {
        $put = file_get_contents('php://input');
        //$put = '{"dataname":"socket","cid":4,"datatype":7}';
        if (empty($put)) {
            return get_status(1, '请填写相应的数据',4003);
        }
        //转换数组
        $arr = json_decode($put, 1);
        //判断是否已存在
        $res = Db::name('datament')->where($arr)->select();
        //不存在执行插入操作
        if (!$res) {
            $arr['createtime'] = date('Y-m-d H:i:s', time());
            $result = Db::name('datament')->insert($arr);
        } else {
            return get_status(2, '数据已存在',4001);
        }
        //返回值
        if (empty($result)) {
            return get_status(1, '添加失败',4009);
        } else {
            return get_status(0, '添加成功');
        }
    }

    /**
     * 生成token
     */
    public function generateToken()
    {
        //获取用户token
        $arr = get_all_header();
        if(isset($arr['token'])){
            $userToken = $arr['token'];    
        }else {
            $data = get_status(1,'非法用户访问',10001);
            return $data;
        }

        //设定唯一登录标识
        $rand = mt_rand(10000,99999);
        //设置token
        $token = md5($userToken.time().$rand);
        return $token;
    }

    /**
     * 发送数据到自动刷新图表socket   请注意PATH_WEB是否在公共文件定义了
     * websocket执行回调
     */
    public function screenSocketRequestStart()
    {
        //获取缓存中的screen
        $screen = Cache::get('screen');
        //判断是否有缓存
        if(empty($screen)) {
            return "success";
        }
        //将数组转换为json
        $screenArr = json_decode($screen,1);

        //curlPOST目标地址
        $url = CHART_SCREEN_AUTO_URL."/socket.php";
        //遍历缓存
        foreach ( $screenArr as $value) {
            //获取大屏ID
            $id = $value['sid'];
            //获取刷新信息
            $curlData = file_get_contents(PATH_WEB."/index/Websocket/index?id=".$id);
            //绑定数据
            $data[$id] = $curlData;
        }
        //参数
        $requestString = json_encode($data);
        //curl执行post
        $result = $this->doCurlPostRequest($url , $requestString , 60);
        sleep(1);
        return $result;
    }

    /**
     * 发送数据到socket
     * websocket执行回调
     */
    public function unitySocketRequestStart()
    {
        //curlPOST目标地址
        $url = CHART_UNITY_AUTO_URL."/socket.php";
        //获取所有的场景信息
        $dev = $this->getAllDevice();

        //定义数据存储数组
        $arr = [];
        //遍历场景
        foreach ($dev['data'] as $key => $value) {
            //遍历每个场景中的设备
            foreach ($value as $v) {
                //获取每个场景的数据
                $data = $this->getData($v);
                $arr[$v['ScenceId']][] =  json_encode($data);
            }
        }
        //参数
        $requestString = json_encode($arr);
        //curl执行post
        $result = $this->doCurlPostRequest($url , $requestString , 60);
        return $result;
    }

    //curl
    protected function doCurlPostRequest($url,$requestString,$timeout = 5){
        if($url == '' || $requestString == '' || $timeout <=0){
            return false;
        }
        $con = curl_init((string)$url);
        curl_setopt($con, CURLOPT_HEADER, false);
        curl_setopt($con, CURLOPT_POSTFIELDS, $requestString);
        curl_setopt($con, CURLOPT_POST,true);
        curl_setopt($con, CURLOPT_RETURNTRANSFER,true);
        curl_setopt($con, CURLOPT_TIMEOUT,(int)$timeout);
        return curl_exec($con);
    }

    /**
     * @return 变量结构|mixed
     * 查询所有的场景并处理格式
     */
    public function getAllDevice()
    {
        //查询所有的设备
        $result = $this->unityJsonModel->getAllDevice();

        if(!$result) {
            return get_status(0,[]);
        }
        //将数据装换为[ "场景ID" => ['场景设备配置']]
        $data = $this->initData($result);

        //判断$data是否为空
        if (!$data) {
            return get_status(0,[]);
        }
        return get_status(0,$data);
    }

    //将数据装换为[ "场景ID" => ['场景设备配置']]
    protected  function initData($result)
    {
        //定义返回数组
        $arr = [];
        //循环遍历$result
        foreach ($result as $key => $value) {
           if($value['jsonname']) { //判断$value是否为空
                $scenceInfo  = json_decode($value['jsonname'] ,1); //场景配置json装数组
                $scenceName = $scenceInfo['ScenceName']; //设置场景名称
                //遍历$scenceInfo中的GameObjects
               foreach ($scenceInfo['GameObjects'] as $v) { //遍历$scenceInfo['GameObjects'] 改组数据中是对应的每个场景中的设备
                   $devInfo = json_decode($v['Obj'] , 1) ; // 设备配置json装数组
                   $arr[$scenceInfo['ScenceId']][] = [
                       "guid" => $devInfo['guid'] , // 设备唯一ID
                       "modelID" => $devInfo['modelID'],
                       "chainId" => $devInfo['chainId'],
                       "ScenceId" => $devInfo['ScenceId'], //场景ID
                       "name" => $devInfo['name'], //模型ID
                       'scenceName' => $scenceName, //场景名字
                   ];
               }
           }
        }
        return $arr;
    }

    /**
     * 用于查询改设备的监控信息
     * @param $key 返回的类型
     * @param $scenesInfo 场景中设备的配置信息
     * @return array 设备的监控信息
     * ,目前为假数据
     */
    protected function getData($scenesInfo)
    {

        //通过设备配置查询数据库

        //判断查询是否成功

        //组成data


        $data = [
            "sid"=> $scenesInfo['ScenceId'], //场景ID
            "3DModelID"=>strval($scenesInfo['guid']), //3D模型ID
            "3DModelName"=>$scenesInfo['name'],//3D模型名称
            "CorpCode"=>"C15",//工厂代码
            "LineNo"=>"19",//生产线编码
            "DeviceCode"=>" C15_19_1", //设备编码
            "ActTime"=>date("Y-m-d H:i:s" , time()),
        ];
        $key = mt_rand(1,8);
        switch ($key) {
            case 1 :
                $data['MessageDesc'] = "正常";
                $data['MessageTypeCode'] = "D01";
                $data['MessageCode'] = "01";
                break;
            case 2 :
                $data['MessageDesc'] = "正常";
                $data['MessageTypeCode'] = "D01";
                $data['MessageCode'] = "01";
                break;
            case 3 :
                $data['MessageDesc'] = "故障信息";
                $data['MessageTypeCode'] = "D03";
                $data['MessageCode'] = "02";
                break;
            case 4 :
                $data['MessageDesc'] = "报警信息";
                $data['MessageTypeCod '] = "D04";
                $data['MessageCode'] = "03";
                break;
            case 5 :
                $data['MessageDesc'] = "10000";//(进设备产品数量)
                $data['MessageTypeCode'] = "D05";
                $data['MessageCode'] = "01";
                break;
            case 6 :
                $data['MessageDesc'] = "99999";//(出设备产品数量（产量）)
                $data['MessageTypeCode'] = "D06";
                $data['MessageCode'] = "01";
                break;
            case 7 :
                $data['MessageDesc'] = "1000";//(理论生产速度)
                $data['MessageTypeCode'] = "D07";
                $data['MessageCode'] = "01";
                break;
            case 8 :
                $data['MessageDesc'] = "1000";//(实际生产速度)
                $data['MessageTypeCode '] = "D08";
                $data['MessageCode'] = "01";
                break;
        }

        //判断$data中的MessageTypeCode是否是D04
        if($data['MessageTypeCode'] == "D04") {
            //如果条件符合 设置MessageCode = 04
            $data['MessageCode'] = "04";
        }
        return $data;
    }
    
}  
