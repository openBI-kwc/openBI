<?php
namespace app\index\controller;

/**
 * 该类用于处理爱创数据 设备的和场景的处理
 */
use think\Loader;

class Deviceinfo
{
    //deviceInfoModel 用于处理数据库
    protected  $deviceInfoModel;

    // 构造方法
    public function __construct()
    {
        //初始化$deviceInfoModel
        $this->deviceInfoModel = Loader::model('Deviceinfo');
    }

    /**
     * 产线和设备的信息
     */
    public  function deviceAll()
    {
        //所有场景
        $Scenes = [
            [
                "value" => 1,
                "name" => "场景1",
            ],
            [
                "value" => 2,
                "name" => "场景2",
            ],
        ];
        //设备
        $device = [
            [
                "value" => 1,
                "name" => "吹风机1",
                ],
            [
                "value" => 2,
                "name" => "吹风机3",
            ],
            [
                "value" => 3,
                "name" => "吹风机2",
            ],[
                "value" => 4,
                "name" => "打包机器1",
            ],
            [
                "value" => 5,
                "name" => "打包机器2",
            ],
            [
                "value" => 5,
                "name" => "打包机器3",
            ]];

        //生产线
        $ProductionLine = [
            [
                "value" => 1,
                "name" => "产线1",
            ],
            [
                "value" => 2,
                "name" => "产线2",
            ],
            [
                "value" => 3,
                "name" => "产线3",
            ],
        ];

        return ['err' => 0 ,'data' =>['device' => $device , 'ProductionLine' => $ProductionLine ,  'scenes'=> $Scenes]];
    }

    /**
     * 场景与设备的绑定
     *  scenes chainId     modelID    guid = 3DModelID    对应 场景ID 生产线ID ， 设备ID  ， 模型GUID
     */
    public function bindDevice()
    {
        //获取前端传参
        $input = input('post.');
        //验证数据是否是需要的键
        $vail = valiKeys(["scenesId","chainId" ,"modelID" ,"guid" ] , $input);
        if($vail['err']) {
            return get_status(1,$vail['data'].'不能为空',333);
        }
        //场景的配置
        $config = [
            'guid' => $input['guid'], //模型GUID 3DModelID
            'scenes' => $input['scenesId'], //场景ID
            'modelid' => $input['modelID'], //设备ID
            'chainid' => $input['chainId'], //生产线ID

        ];
        //数据库参数
        $data = [
            "scenes" => $input['scenesId'],
            'scenes_config' => json_encode($config),
        ];
        //查询数据库是否存在一样的数据
        $result = $this->deviceInfoModel->vailBindDevice($data);
        if($result) {
            return get_status(0,'模型已绑定');
        }
        //将创建时间加入到data中
        $data['scenes_createtime'] = time();
        //存入数据库
        $res = $this->deviceInfoModel->bindDevice($data);
        if($res) {
            return get_status(0,'模型绑定成功');
        }else {
            return get_status(1,'模型绑定失败',1111);
        }
    }

}