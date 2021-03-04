<?php
namespace app\index\Controller;

use think\Db;
use think\Loader;
/**
 * 改类用于处理 Unity 3D模型和动画的上传 查询
 */

class Unity
{
    //unityjson model
    protected $unityjson;

    //初始化model
    public  function __construct()
    {
        $this->unityjson = Loader::model('Unityjson');
    }

    //上传3D模型
    public function uploadUnity()
    {
        if(!isset($_FILES['file'])) {
            return get_status(1,"文件不存在");
        }
        //获取文件信息
        $fileinfo = $_FILES['file'];
        //判断文件是否存在
        if(empty($fileinfo)){
            return get_status(1,NULL);
        }
        //定义文件存储路径
        $path = ROOT_PATH . 'public' . DS . 'unity'.DS;
        $dir = 'unity';
        $result = $this->uoloadFile($fileinfo,$path,$dir);
        if(!$result['err']) {
            $data = $result['data'];
            return $this->uploadSuccess($data , 'unity', 'unityid');
        }else {
            return get_status(1,$result['data'],000);
        }
    }

    //获取文件版本号
    protected function getVersion($name , $tableName)
    {
        //查询name
        $version = Db::name($tableName)->where('name',$name)->order('version DESC')->find();
        if($version) {
            $versionNum = $version['version'];
        }else {
            $versionNum = 0;
        }
        return $versionNum;
    }

    //获取全部3D模型名字
    public function getFileName()
    {
        $result = Db::name('unity')->select();
        if($result) {
            $total = count($result);
            $data = [
                "Total" => $total,
                "Data" => $result,
            ];
            echo str_replace('\\','',json_encode(get_status(0,$data)));
        }else {
            echo str_replace('\\','',json_encode(get_status(0,[])));
        }

    }

    //上传动画
    public function uoloadAnimation()
    {
        //判断文件是否存在
        if(!isset($_FILES['file'])) {
            return get_status(1,"文件不存在");
        }
        //获取文件信息
        $fileinfo = $_FILES['file'];
        //判断文件是否存在
        if(empty($fileinfo)){
            return get_status(1,NULL);
        }
        //接收input的值
        $label = input('post.label');
        $animationName = input('post.animationName');

        //定义文件存储路径
        $path = ROOT_PATH . 'public' . DS . 'Animation'.DS;
        $dir = 'Animation';
        $result = $this->uoloadFile($fileinfo,$path,$dir);
        if(!$result['err']) {
            $data = $result['data'];
            $data['label'] = str_replace(' ' ,'',$label);
            $data['animationname'] = str_replace(' ' ,'',$animationName);

            return $this->uploadSuccess($data , 'animation', 'aid');
        }else {
            return get_status(1,$result['data'],000);
        }
    }

    //上传动画成功,处理数据库
    protected function uploadSuccess($data , $table , $key)
    {
        //查询数据库内是否存在name
        $res =  Db::name($table)->where('name',$data['name'])->find();
        if($res) {
            Db::name($table)->where('name', $data['name'])->update($data);
        }else {
            //获取自增ID
            $maxid = $this->primaryKeyGet($table,$key);
            if(!$maxid) {
                $maxid = 0;
            }
            //将自增ID加入data
            $data[$key] = $maxid;
                //将信息存入数据库
            Db::name($table)->insert($data);
        }
        return get_status(0,'文件上传成功');
    }

    //获取模糊查询的动画信息
    public function getAnimation()
    {
        //获取like的值
        $input = input('get.');
       
        //判断get是否存在
        if(isset($input['like'])) {
            $like = $input['like'];
        }else {
            $like = '';
        }
       
        $result = Db::name('animation')->where('label','like',"%".$like."%")->select();
        
        $total = count($result);
        $data = [
            "Total" => $total,
            "Data" => $result,
        ];
        echo str_replace('\\','',json_encode(get_status(0,$data)));
        
    }

    //获取模糊查询的动画信息
    public function getSingeAnimation()
    {
        //获取like的值
        $input = input('get.');

        $vali = valiKeys(['label','name'],$input);

        if($vali['err']) {
            return get_status(1,$vali['data'].'不能为空',333);
        }

        $result = Db::name('animation')->where('label',$input['label'])->where('animationname' ,'like',"%".$input['name']."%")->find();

        echo str_replace('\\','',json_encode(get_status(0,$result)));

    }

    //提供一个接口   申请一个唯一 的ID
    public function getOnliId()
    {
        //查询数据库最大ID
        $maxID = $this->primaryKeyGet('unityjson' , 'jsonid');
        if(!$maxID) {
            $maxID = 0;
        }
        //向数据库请求一个ID
        $insert = Db::name('unityjson')->insert(['jsonid' => $maxID, 'createtime' => time()]);
        if(!$insert) {
            return ['err' => 1 , 'data' => "未知错误,请重试" ];
        }
        return ['err' => 0 ,"data" => $maxID];
    }

    //上传一个json 字符串    ID : Json
    public function upJsonString()
    {
        //接收数据
        $input = input('post.');
        //验证数据
        $vali = valiKeys(['id','json'],$input);
        if($vali['err']) {
            return get_status(1,$vali['data'].'不能为空',333);
        }
        $isarr = is_array($input['json']);
        if($isarr) {
            $jsonname = json_encode($input['json']);
        }else {
            $jsonname = $input['json'];
        }
        
        //向数据库插入信息
        $data = [
            'jsonname' => $jsonname,
            'createtime' => time()
        ];
        //插入json
        $update = DB::name('unityjson')->where('jsonid',$input['id'])->update($data);
        if($update) {
            return ['err' => 0 , 'data' => '成功'];
        }else {
            return ['err' => 1 , 'data' => '失败'];
        }
    }

    //申请一个 列表   得到这些 id,  name .
    public function getJsonName()
    {
        //接收数据
        $input = input('get.');
       //判断input['id']是否设置
       if(isset($input['id'])) {
            $result = Db::name('unityjson')->where('jsonid' , $input['id'])->find();
            if($result) {
                //判断是否是json数组
                $jsonname = $this->isJsonArr($result['jsonname']);
                return ['err' => 0, "name" => $jsonname];
            }
       }else {
            $result = Db::name('unityjson')->select();
            if($result) {
                $data = [];
                foreach($result as $value) {
                    $jsonname =  $this->isJsonArr($value['jsonname']);
                    $data[] = ['ID' => $value['jsonid'],'name' => $jsonname];
                }
                return ['err' => 0, "data" => $data];
            }
       }
       return ['err' => 1,'data' => '查询失败'];
    }

    //获取单个场景的名字和ID
    public  function  singleScenesInfo()
    {
        $input = input('get.');
        //查询场景信息
        $result = $this->unityjson->singleScenesInfo();
        if(!$result) {
            return get_status(0 , []);
        }

        //将查询到的jsonname格式化查出场景名称
        foreach ($result as $value) {
            $scenes[] = ['id' => $value['jsonid'] , 'scenesname' =>  $this->getsingleScenesName($value['jsonname'])];

        }
        return get_status(0,$scenes);
    }

    /**用过json获取场景名字
     * @param $data unityjson
     * @return string 场景名字
     */
    protected  function  getsingleScenesName($data)
    {
        //默认场景名
        $scenesName = "未命名场景";
        //判断data是否为空
        if(!$data) {
            return $scenesName;
        }
        //将json转为数组
        $arr = json_decode($data,1);
        //判断json是否可以装换成数组
        if($arr) {
            //判断数组中是否有ScenceName键
            if(isset($arr['ScenceName'])) {
                $scenesName = $arr['ScenceName'];
            }
        }
        return $scenesName;
    }

    /**
     * 删除单个场景
     * @return 变量结构|mixed
     */
    public function deleteScenes()
    {
        $input = input('get.');
        //验证传参是否正确
        $vali =  valiKeys(['id'] , $input);
        if($vali['err']) {
            return get_status(1,$vali['data'].'未设置',3333);
        }
        //删除ID对应的信息
        $result =  $this->unityjson->deleteScenes($input['id']);
        if($result) {
            return get_status(0,'删除成功');
        }else {
            return get_status(1,'删除失败');
        }
    }

    //设置主键ID自增
    public function primaryKeyGet($table , $key)
    {
        //查询数据库最大ID
        $maxIDRes = Db::name($table)->order($key . ' DESC')->find();
        if(!$maxIDRes) {
            return 1;
        }
        $maxID = intval($maxIDRes[$key] + 1);

        return $maxID;
    }

    //判断是否是json数组
    protected function isJsonArr($json)
    {
        if(!$json) {
            return $json;
        }
        $jsonArr = json_decode($json ,1);
        if($jsonArr) {
            return $jsonArr;
        }else {
            return $json;
        }
    }

    //上传文件
    protected function uoloadFile($fileinfo, $path,$dir)
    {
        //判断路径是否存在
        if (!file_exists($path)) {
            mkdir($path , 0755);
        }
        $maxsize = 0;
        //判断错误号
        if($fileinfo['error'] > 0){
            switch($fileinfo['error']){
                case 1:$error="上传的文件超过了 php.ini 中 upload_max_filesize 选项限制的值";break;
                case 2:$error="上传文件的大小超过了 HTML 表单中 MAX_FILE_SIZE 选项指定的值";break;
                case 3:$error="文件只有部分被上传。";break;
                case 4:$error="没有文件被上传。";break;
                case 6:$error="找不到临时文件夹";break;
                case 7:$error="文件写入失败";break;
                default:$error="未知错误，请稍后再试...";
            }
            return get_status(1,$error,000);
        }
        //取出文件后缀后缀
        $file = pathinfo($fileinfo['name']);
        //获取版本号版本号
        $i = $this->getVersion($file['filename'] , $dir);
        //生成文件名.同名加版本号
        do{
            $i++; //版本号自增
            $version = $i; 
            $newname = $file['filename'].'-'.$i.".".$file['extension']; //定义完整文件名
        }while(file_exists($path.$newname));
       
       
        //判断是否上传成功
        if(is_uploaded_file($fileinfo['tmp_name'])){ //判断是否是文件
            if(move_uploaded_file($fileinfo['tmp_name'],$path.$newname)){ //移动文件
                //设置文件以public目录下为根目录地址
                $publicPaht = DS . $dir . DS . $newname;
                //将文件信息保存
                $data = [
                    'name' => $file['filename'],
                    'filename' => $newname,
                    'path' => str_replace('\\','/',$publicPaht),
                    'version' => $version,
                    'createtime' => time(),
                ];
                return get_status(0,$data);
            }else{
                return get_status(1,'文件移动失败',000);
            }
        
        }else{
            return get_status(1,'未知错误！请重试',000);
        }
    }

}