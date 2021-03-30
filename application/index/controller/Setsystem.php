<?php
namespace app\index\Controller;
use think\Db;
use \tp5er\Backup;
use app\base\controller\Base;

/**
  系统设置
* 
*/
class Setsystem extends Base
{
  //系统常规设置修改和插入操作
  public function generalSet()
  {
     //获取数据 
     $put = file_get_contents('php://input');

     if (empty($put)) {
        return get_status(1,NULL);
     }
     $post = json_decode($put,1);
     //配置文件的路径
      // $path = config('static_config_path');
      $path = config('static_config_path');
     //获取配置文件的内容
     //die;
     $file = file_get_contents($path);
     //转成数组
     $arr = json_decode($file,1);
    //dump($arr);
     //修改配置文件
     $arr['system'] = $post;
     //dump($arr);
     //转成json字符串
//      echo 444;
     $jsondata = json_encode($arr,JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
//     dump($jsondata);
      //die;
     //写入config.json文件
     $data = file_put_contents($path, $jsondata,FILE_USE_INCLUDE_PATH);
//      echo 666;
//     dump($data);die;

     if(empty($data)){
        return get_status(1,NULL);
     }else{
        return get_status(0,NULL);
     }

  }
  //系统常规设置查询数据列表
  public function generalList()
  {
    //查询数据
    //$path = '/home/wwwroot/datav-h5/static/config.json';
//    $path = ROOT_PATH . 'public/openv/static/config.json';
    // $path = config('static_config_path');
    $path = config('static_config_path');
    //获取配置文件的内容
    $file = file_get_contents($path);
    $arr = json_decode($file,1);
    //$result = Db::name('systemset')->select();
    if (empty($file)) {
           return get_status(1,NULL);
      }else{
           return get_status(0,$arr['system']);
      }

  }

  //上传logo
  public function uploadimg()
  {
    //执行上传操作
    $file = request()->file('image');
    if(empty($file)){
      return get_status(1,NULL);
    }
      //把图片移动到/public/uploads/img/文件下
    $info = $file->validate(['size'=>5242880])->move(ROOT_PATH . 'public' . DS . 'uploads' . DS . 'logo');
    if($info){
        //获取图片的路径
        $newpath =  '/uploads/logo/' .$info->getSaveName();
        $newpath = str_replace('\\','/',$newpath);
    }else{
        // 上传失败获取错误信息
        $error = $file->getError();
    }
    //返回的数据
    if (empty($info)) {
         return get_status(1,$error);
    }else{
         return get_status(0,$newpath);
    }
  }

    /**
     * 附件设置查询
     */
  public function getAttachment()
  {
      //查询附件设置
      $result = Db::name('attachment')->find();
      if(empty($result)) {
          //返回空数组
          return get_status(0,$result);
      }
      $result['is_water'] = $result['is_water'] == 1 ? true : false;
      //修改数据
      // $data = $this->processAttachmentData($result[0]);
      //返回数据
      return get_status(0,$result);
  }

  //附件设置
  public function attachment()
  {
      $put = file_get_contents('php://input'); 
      //$put = '{"type":"jpg,png,gif","width":120,"height":80,"is_water":1,"transparency":50,"waterpath":"/uplode/water/","position":3}';
      // dump($put);die;

      if(empty($put)){
        // echo 123;
         $result = Db::name('attachment')->select();
      }else{
        $post = json_decode($put,1);
        $update = Db::name('attachment')->where('attid',1)->update($post);
        if(empty($update)){
          return get_status(1,'未做修改' ,5001);
        }else{
          $result = Db::name('attachment')->select();
        }
      }
      //返回值
      if (empty($result)) {
        return get_status(1,'未做修改',5001);
      }else{
          //修改数据
          $data = $this->processAttachmentData($result[0]);
          //返回数据
          return get_status(0,$result[0]);
      }
  }

    //处理附件查询数据
    protected function processAttachmentData($data)
    {
        //将1,2换成true , false
        $data['is_water'] = $data['is_water'] == 1 ? true : false;
        //将位置换成字符串
        switch ($data['position']) {
            case "1" :
                $position = "LeftTop";
                break;
            case "2" :
                $position = "Top";
                break;
            case "3" :
                $position = "RightTop";
                break;
            case "4" :
                $position = "Left";
                break;
            case "5" :
                $position = "Center";
                break;
            case "6" :
                $position = "Right";
                break;
            case "7" :
                $position = "'LeftBottom";
                break;
            case "8" :
                $position = "Bottom";
                break;
            case "9" :
                $position = "RightBottom";
                break;
            default:
                $position = "LeftTop";
        }
        $data['position'] = $position;

        return $data;
    }

    //将英文转换为数字
    protected function processEToN($string)
    {
      //将位置换成字符串
      switch ($string) {
        case "LeftTop" :
            $position = "1";
            break;
        case "Top" :
            $position = "2";
            break;
        case "RightTop" :
            $position = "3";
            break;
        case "Left" :
            $position = "4";
            break;
        case "Center" :
            $position = "5";
            break;
        case "Right" :
            $position = "6";
            break;
        case "LeftBottom" :
            $position = "'7";
            break;
        case "Bottom" :
            $position = "8";
            break;
        case "RightBottom" :
            $position = "9";
            break;
        default:
            $position = "2";
      }

      return $position;
    }



  //上传附件
  public function upattachment()
  {
    $select = Db::name('attachment')->select(); 
    //将位置英文字符串转为数字
    $select[0]['position'] = $this->processEToN($select[0]['position']);
    $path = ROOT_PATH . 'public' . $select[0]['waterpath'];
    //执行上传操作
    $file = request()->file('image');
        
    if(empty($file)){
      return get_status(1,"不能为空",5002);
    }
    //把图片移动到/public/uploads/img/文件下
    $filepath = ROOT_PATH . 'public' . DS . 'uploads';
    $info = $file->validate(['size'=>156711800 /*,'ext'=>$select[0]['type']*/])->move($filepath);
    $len = strlen($info->getSaveName())-3;
    //获取后缀
    $svgg = substr($info->getSaveName(),$len);
   
    if (!$info) {
      return get_status(1,"上传附件宽高过大",5003);
    }
    //获取图片的宽和高
    list($width, $height) = getimagesize($filepath.'/'.$info->getSaveName());
    
    //判断图片的宽和高大于设置的数 直接返回报错信息
    if ($width > $select[0]['width'] && $height > $select[0]['height']) {
      return get_status(1,"上传附件宽高过大",5003);
    }
    if($svgg == 'svg'){
      //获取图片的路径 
      $newpath =  '/uploads/' .$info->getSaveName();
      //保存到data数组
      $data['url'] = str_replace('\\', '/', $newpath);
      //路径存入data数组
      $data['thumb'] = '/uploads/thumb/'.date('Ymd').'/'.$info->getFilename();
        //存入数据库
        $result = Db::name('upattachment')->insert($data);
          if(empty($result)){
             return get_status(1,'上传附件失败',5004);
        }
         //查询出添加的id
         $id = Db::name('upattachment')->where($data)->value('upid');
         //查询出增加的数据
         $result = Db::name('upattachment')->where(['upid'=>$id])->find();
         if($result['waterurl'] == ''){
          $result['waterurl'] = $result['url'];
        }
        if(empty($result)){
          return get_status(1,"查询失败",5005);
       }else{
         return ['err'=>0,'data'=>$result];
       } 
    }
    
    if($info){
        //拼接全路径，打开图片资源的时候使用
        $imagepath = $filepath.'/'.$info->getSaveName();
        //路径中正斜线和反斜线的替换
        $imagepath = str_replace('\\','/',$imagepath);
        //拼接缩略图路径
        $data_path = $filepath.'/thumb/'.date('Ymd');
        //递归创建缩略图路径
        if(!file_exists($data_path)){
          mkdir($data_path,0777,true);
        }
        //缩略图路径加文件
        $thumb_path = $data_path.'/'.$info->getFilename();
        //打开图片资源
        $image = \think\Image::open($imagepath); 
        //生成缩略图
        $image->thumb(300,300,\think\Image::THUMB_CENTER)->save($thumb_path); 
        
        //检测水印照片是否存在
        if(!file_exists($path)){
          Db::name('attachment')->where(['attid'=>$select[0]['attid']])->update(['is_water'=>2]);
          $select = Db::name('attachment')->select();
        }

        if($select[0]['is_water'] == 1){
          //拼接原图加水印路径
          $water_path = $filepath.'/water/'.date('Ymd');
          //递归创建原图加水印路径 
          if (!file_exists($water_path)) {
            mkdir($water_path,0777,true);
          }
          //原图加水印路径加文件
          $waterurl = $water_path.'/'.$info->getFilename();
          //打开图片资源
          $image2 = \think\Image::open($imagepath); 
          //添加水印 并保存到waterurl位置
          $image2->water($path,$select[0]['position'],$select[0]['transparency'])->save($waterurl);
          $thwater_path = $filepath.'/thwater/'.date('Ymd');
          if (!file_exists($thwater_path)) {
            mkdir($thwater_path,0777,true);
          }
          $thwaterurl = $thwater_path.'/'.$info->getFilename();
          // 实例化缩略图对象
          $image1 = \think\Image::open($thumb_path);      
          //路径存入data数组
          $image1->water($path,$select[0]['position'],$select[0]['transparency'])->save($thwaterurl);
          $data['warterthumb'] = '/uploads/thwater/'.date('Ymd').'/'.$info->getFilename();
          $data['waterurl'] = '/uploads/water/'.date('Ymd').'/'.$info->getFilename();
           //数据库严格模式
          $data['thumb'] = '/uploads/thumb/'.date('Ymd').'/'.$info->getFilename();
          $data['url'] = str_replace('\\', '/', '/uploads/' . $info->getSaveName());
        }else{
          //获取图片的路径 
          $newpath = str_replace('\\', '/', '/uploads/' . $info->getSaveName());
          //保存到data数组
          $data['url'] = $newpath;
          //数据库严格模式
          $data['warterthumb'] ='';
          $data['waterurl'] = '';
          //路径存入data数组
          $data['thumb'] = '/uploads/thumb/'.date('Ymd').'/'.$info->getFilename();
        }        
      
        //存入数据库
        $result = Db::name('upattachment')->insert($data);
         //查询出添加的id
         $id = Db::name('upattachment')->where($data)->value('upid');
          if(empty($result)){
             return get_status(1,'上传附件失败',5004);
        }
        }else{
            // 上传失败获取错误信息
            $error = $file->getError();
        }



        $result = Db::name('upattachment')->where(['upid'=>$id])->find();
        if($result['waterurl'] == ''){
          $result['waterurl'] = $result['url'];
        }
      // foreach($result as $k=>&$v){
      //   if($v['waterurl'] == ''){
      //     $v['waterurl'] = $v['thumb'];
      //   }
      // }
            // //返回的数据
    if (empty($info)) {
       return get_status(1,$error);
    }else{
       if(empty($result)){
          return get_status(1,"查询失败",5005);
       }else{
         return ['err'=>0,'data'=>$result];
       }            
    }
  }
  //附件列表
  public function attList()
  {
    $post = input('get.');
    //$get = '{"pages":1,"num":2}';
    //$post = json_decode($get,1);
    if(!isset($post['currentPage'])){
      $post['currentPage'] = '1';
    }
    
    if(!isset($post['pageSize'])){
      $post['pageSize'] = '20';
    }

    $select = Db::name('attachment')->find();
    //判断是否传了_t
    if(isset($post['_t'])) {
      //删除_t _t用于前端请求方便
      unset($post['_t']);
    }

      if(empty($post)){
          if ($select['is_water'] == 1) {
          $sel = Db::name('upattachment')->select();
        $result = Db::name('upattachment')->field('upid,warterthumb,url,waterurl')->page($post['currentPage'],$post['pageSize'])->order('upid','desc')->
        select();
        $total = count($sel);
      }elseif($select['is_water'] == 2){
        $sel = Db::name('upattachment')->select();
        $result = Db::name('upattachment')->field('upid,thumb,url,waterurl')->page($post['currentPage'],$post['pageSize'])->order('upid','desc')->
        select();
        $total = count($sel);
      }
    }else{
      if ($select['is_water'] == 1) {
        $sel = Db::name('upattachment')->select();
        $result = Db::name('upattachment')->field('upid,warterthumb,url,waterurl')->page($post['currentPage'],$post['pageSize'])->order('upid','desc')->
        select();
        $total = count($sel);
      }elseif($select['is_water'] == 2){
        $sel = Db::name('upattachment')->select();
        $result = Db::name('upattachment')->field('upid,thumb,url,waterurl')->page($post['currentPage'],$post['pageSize'])->order('upid','desc')->
        select();
        $total = count($sel);
      }   
    }
    //判断有无水印，无水印的话展示缩略图
    foreach($result as $k=>&$v){
      if($select['is_water'] == 1){
        if($v['waterurl'] == ''){
          $v['waterurl'] = $v['url'];
        }
      }else{
        if($v['waterurl'] == ''){
          $v['waterurl'] = $v['url'];
        }
      }
    }

      $data = [
      'list'=>$result,
      'total'=>$total
    ];
      return ['err'=>0,'data'=>$data];
//    if (empty($result)) {
//      return ['err'=>0,'data'=>$data];
//    }else{
//      return ['err'=>0,'data'=>$data];
//    }
  }
  //查看大图
  public function showimg()
  {
    //获取前台穿过来的值
    $put = file_get_contents('php://input');
    //转换成数组
    $post = json_decode($put,1);
    //查询附件设置数据
    $select = Db::name('attachment')->select();
    //判断是否开启水印
    if ($select[0]['is_water'] == 1) {
      //返回水印大图
          $result = Db::name('upattachment')->where('upid',$post['upid'])->field('upid,warterurl')->select();
        }elseif($select[0]['is_water'] == 2){
          //返回原图大图
          $result = Db::name('upattachment')->where('upid',$post['upid'])->field('upid,url')->select();
        }
        //返回值
        if (empty($result)) {
           return get_status(1,'查询失败',5005);
        }else{
           return get_status(0,$result);
        }
  }
  //删除附件
  public function attdelete()
  {
    $put = file_get_contents('php://input');
    if (empty($put)) {
      return get_status(1,NULL);
    }
    //$put = '{"upid":[13]}';
    $post = json_decode($put,1);
    if (count($post['upid']) >1 ) {
      $result = Db::name('upattachment')->where('upid','in',$post['upid'])->delete();
    }else{
      $result = Db::name('upattachment')->where('upid',$post['upid'][0])->delete();
    }   
    if (empty($result)) {
           return get_status(1,'删除附件失败',5006);
        }else{
           return get_status(0,$result);
        }
  }
  //安全设置
  public function safe()
  {
     $put = file_get_contents('php://input');
     if (empty($put)) {
       return get_status(1,'添加数据为空',5001);
     }
     //$put = '{"maxerr":3,"intervaltime":5,"terminal":3,"adminlog":1,"login":1}';
     $post = json_decode($put,1);
     $select = Db::name('safety')->select();
     //查看数据库中是否有数据，如果有数据，执行修改操作，如果没有数据，执行插入操作
     if (empty($select)){
        $result = Db::name('safety')->insert($post);
     }else{
        $result = Db::name('safety')->where('said',$select[0]['said'])->update($post);
     }
     //返回值
     if (empty($result)) {
         return get_status(1,'查询失败',5005);
     }else{
         return get_status(0,$result);
     } 

  }
  //安全设置查询数据
  public function safeList()
  {
    //查询数据
    $result = Db::name('safety')->select();
    if (empty($result)) {
         return get_status(1,'查询失败',5005);
    }else{
         return get_status(0,$result);
    }
  }
  //数据库备份
  public function backup()
  {
    $variable = Db::name('variable')->find();
    if ($variable['variable'] == 0) {
      //echo 111;
      $variable = Db::name('variable')->where('id',1)->update(['variable'=>1]);
      $config=array(
        'path' =>ROOT_PATH.'data/',//数据库备份路径
      );
      $db= new Backup($config);//实例化数据库备份类进行条用里面的方法。
      $db->setTimeout(0);
      //$db->setFile(['name'=>'xiao','part'=> 1]);
      $tables = $db->dataList();
      for ($i=0; $i < count($tables); $i++) { 
        $rel = $db->backup($tables[$i]['name'],0);
      }
      $filename = $db->getFile();
      $data['dataname'] = $filename['filename'];
      $data['backtime'] = time();
      $data['link'] = '';
      $variable = Db::name('variable')->where('id',1)->update(['variable'=>0]);
      if ($rel === false) {
        return get_status(1,'数据库备份失败',5007);
      }else{
        $result = Db::name('backup')->insert($data);
        if (empty($result)) {
          return get_status(1,'数据库插入失败',5008);
        }else{
          return get_status(0,NULL);
        }

      }

   }else{
      return get_status(1,'正在备份请稍后再试',5009);
   }
    //$a = $db->downloadFile($data['dataname']);
    
        
  }
  //数据库备份列表
  public function backupList()
  {
    $post = input('get.');
    //dump($post);
    //'{"pages":2,"num":10}'
    //$post = json_decode($get,1);
    //判断是否传了_t
    if(isset($post['_t'])) {
      //删除_t _t用于前端请求方便
      unset($post['_t']);
    }

    //分页 $currentPage第几页
    if(isset($post['currentPage'])){
      $currentPage = $post['currentPage'];
    }else{
      $currentPage = 1;
    }

    //分页 $pageSize每条页数
    if(isset($post['pageSize'])){
      $pageSize = $post['pageSize'];
    }else{
      $pageSize = 10;
    }

    $result = Db::name('backup')->field('bid,dataname,backtime')->page($currentPage.','.$pageSize)->order('backtime' , 'desc')->select();
    $total = Db::name('backup')->count();
    
    $data = ['list' => $result , 'total' => $total];
    
    if (empty($result)) {
         return get_status(0,[]);
    }else{
         return get_status(0,$data);
    }

  }
  //数据库备份下载
  public function backupdown()
  {
    $post = input('get.');
    //$put = '{"dataname":"20180802-113738-1.sql"}';
    //$post = json_decode($put,1);
    if (empty($post)) {
      return get_status(1,"参数不完整" , 1050);
    }
    $config=array(
      'path' =>ROOT_PATH.'data/',//数据库备份路径
    );
    $db= new Backup($config);//实例化数据库备份类进行条用里面的方法。

    $str = trim($post['dataname'],'-1.sql');
    $str = explode('-',$str);
    $str = join($str,'');  
    $result = $db->downloadFile(strtotime($str));
    if (!empty($result)) {
      return get_status(1,$result);
    }
  }
  public function backupdel()
  {
    $put = file_get_contents('php://input');
    if (empty($put)) {
      return get_status(1,NULL);
    }
    //$put = '{"upid":[13]}';
    $post = json_decode($put,1);
    $result = Db::name('backup')->where('bid',$post['bid'])->delete();
    if (empty($result)) {
          return get_status(1,'删除失败',5006);
        }else{
          return get_status(0,NULL);
        }
  }


  public function localdeploy()
  {
      //查询所有发布信息
      $releaseList = Db::name('publish')->where(["extype" => 0 , "localdeploy" => 1])->field("scid,link")->select();
      //存储文件路径数组
      $fileList = array();
      //存储发布信息
      $publish = array();
      //获取大屏名称
      $jsonPaht = ROOT_PATH . 'public' . DS . 'zip' . DS ;
      //判断文件夹是否存在
      if (!file_exists($jsonPaht)) {
          mkdir($jsonPaht , 0777);
      }
      //存储网络图片文件夹
      $netPicture = ROOT_PATH . 'public' . DS . 'uploads' . DS . 'netpicture' . DS ;
      //判断文件夹是否存在
      if (!file_exists($netPicture)) {
        mkdir($netPicture , 0777);
    }
      //删除上次的压缩包，防止存在脏数据
      $this->delDir(ROOT_PATH . 'public' . DS . 'alg');
      $this->delDir(ROOT_PATH . 'public' . DS . 'uploads' . DS . 'netpicture');
      
      //循环获取大屏数据
      foreach ($releaseList as $value) {
          //获取大屏信息c
          $req = $this->getScreenInfo($value['scid'] , $jsonPaht , $netPicture);
         
          //判断该大屏是否获取成功
          if($req['err']) {
              continue;
          }
          //将json存入数组
          foreach ($req['data'] as $val) {
              $fileList[] = $val;
          }

          //查询大屏信息
          $screenInfo = Db::name('screen')->where("id" ,$value['scid'])->field('name, imgdata')->find();
          //将link的地址去除
          $link  = substr($value['link'] , strpos($value['link'] , "/#"));

          $publish[] = ["link" => $link ,"name" => $screenInfo['name'], "img" => $screenInfo['imgdata']];
        }
        //对图片路径进行处理
        foreach($publish as $pk=>&$pv){
          if(!empty($pv['img'])){
            //文件名+后缀
            $suffix = substr($pv['img'],strrpos($pv['img'],'/'));
            $pv['img'] = '.' . DS . 'static' . DS . 'data' . DS . 'image'.$suffix;
          }
        }
        //将数据转为json
        $publish =  json_encode($publish,JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
         //将数据生成文件
        file_put_contents($jsonPaht.'/deployScreen.json',$publish);  

      //打包成zip , 获取zip
      $zipPath = $this->fileToZip($fileList , "data.zip" , $jsonPaht , $netPicture);

        //删除文件夹，清空旧文件
        $jsonPaht = ROOT_PATH . 'public' . DS . 'zip';

        //判断文件夹是否存在
        if(file_exists($jsonPaht)){
          $this->delDir($jsonPaht);
        }
     
      if ($zipPath['err']) {
          return get_status( 1 , $zipPath['data'] , 1080);
      }else {
          return get_status( 0 , $zipPath['data']);
      }
  }

    /**
     * @param $sid 大屏ID
     * @param $jsonPaht 文件存储位置
     * @return 变量结构|mixed 返回错误信息或者zip连接地址
     */
  protected function getScreenInfo($sid , $jsonPaht , $netPicture)
  {
      
       //读取json文件，或得服务器配置       
       $inturl = self::jspath();
      //获取 /index/index/getscreeninfo?sid=480
      $indexObj = file_get_contents($inturl . "/index/index/getscreeninfo?id=$sid");
      
      $indexArr = json_decode($indexObj , 1);
      
      //压缩包图片的路径
      $zipUrl =  '.' . DS . 'static' . DS . 'data' . DS . 'image';

       //获取字符串长度
       $weblen = strlen($inturl);
       //处理getscreeninfo的图表里面图片路径
       $indexArr['data']['position'] = $this->handleGetscreeninfo($indexArr,$zipUrl,$inturl,$netPicture);   
     
      //处理大屏背景图路径
      $indexArr['data']['screenOption']= $this->BigScreenBack($indexArr,$zipUrl,$inturl,$netPicture);
      
      //将数组中的图片取出 TODO ::目前拿不到文件精确位置
      //判断获取结果
      if($indexArr['err']) { return get_status(1, "获取数据失败" , 1050	);}
      $indexObj = json_encode($indexArr,JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);

      //获取 /index/screen/getallchart?sid=480
      $screenObj = file_get_contents($inturl . "/index/screen/getallchart?id=$sid");
      //将json转换为数组
      $screenArr = json_decode($screenObj , 1);
      if($screenArr['err']) { return get_status(1, "获取数据失败" , 1050	);}
         //更改图片路径
         foreach($screenArr['data'] as $scK=>&$scV){
           //检测图片字段是否被设置
          if(isset($scV['imglist'])){
            //对轮播图数组进行循环
            foreach($scV['imglist'] as $imgK=>&$imgV){
              $imglist = $this->dlfile($imgV , $netPicture);
              $imgV = $zipUrl . '/' . $imglist;
            }
            $scV['imglist'][] = $imgV;
            //会多一条数据，去重
            array_pop($scV['imglist']);
            $screenObj = json_encode($screenArr,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
          }
        }
      //保存为_index_index_getscreeninfo_480.json
      $getscreeninfoJsonName = $jsonPaht.'_index_index_getscreeninfo_'.$sid.'.json';
      //保存为 _index_screen_getallchart_480.json
      $getallchartJsonName  = $jsonPaht.'_index_screen_getallchart_'.$sid.'.json';
      //执行删除
        @unlink($getscreeninfoJsonName);
        @unlink($getallchartJsonName);
      //将文件写入json
      $jsonfile1 = file_put_contents( $getscreeninfoJsonName, $indexObj);
      $jsonfile2 = file_put_contents($getallchartJsonName , $screenObj);
      if(!$jsonfile1 || !$jsonfile2) { //判断写入是否成功
          return get_status(1, "json写入文件失败" , 1050);
      }
      //将文件路径存入数组
      $fileList = [ $getscreeninfoJsonName , $getallchartJsonName ];

      $arr =  $fileList ;
      return get_status(0 , $arr);
  }

    /**
     * @param $fileList 文件数组
     * @param $zipName 大屏名称
     * @return string zip文件路径
     */
  public function fileToZip($fileList , $zipName , $jsonPaht)
  {
          //读取json文件获取服务器配置
          $inturl = self::jspath();

          $zipPath = ROOT_PATH ."public" . DS ."alg" . DS; //定义压缩包路径
          $zip = new \ZipArchive(); 
          if(!file_exists($zipPath)) { //判断文件是否存在
              mkdir($zipPath , 0777);
          }
          $filename = $zipPath . $zipName; // 压缩包所在的位置路径
      
          $zip->open($filename,\ZipArchive::CREATE);   //打开压缩包
          foreach($fileList as $file){
              $zip->addFile($file,basename($file));   //向压缩包中添加文件
          }
          $zip->addFile($jsonPaht.'deployScreen.json',basename($jsonPaht.'deployScreen.json'));

          //查询出全部图片
           $picture = Db::name('upattachment')->field('url')->select();
           //有图片的话放入压缩包
           if(!empty($picture)){
              //向压缩包中添加文件
            foreach($picture as $v){
              $thumb = $v['url'];
              if(!file_exists(ROOT_PATH ."public" . DS .$thumb)){
                return get_status(1,'图片资源有丢失',7001);
              }
              $suffix = substr($thumb,strrpos($thumb,'/'));
              $zip->addFromString('image/'.$suffix,file_get_contents(ROOT_PATH ."public" . DS .$thumb));
             }
           }

           //查询出icon图片
           $icon = Db::name('icon')->field('iconpath')->select();
           //判断数据不为空
           if(!empty($icon)){
             foreach($icon as $iconv){
              if(!file_exists(ROOT_PATH ."public" . $iconv['iconpath'])){
                return get_status(1,'图片资源有丢失',7001);
              }
               $iconfix = substr($iconv['iconpath'],strrpos($iconv['iconpath'],'/'));
               $zip->addFromString('image'.$iconfix,file_get_contents(ROOT_PATH . "public" . $iconv['iconpath']));
             }
           }
           
           //查询cover图片
           $cover= Db::name('screen')->select();
           //判断不为空
           if(!empty($cover)){
             foreach($cover as $cv){
               if($cv['screentype'] == '0'){
                 if(!empty($cv['imgdata'])){
                  if(!file_exists(ROOT_PATH ."public" . $cv['imgdata'])){
                    return get_status(1,'图片资源有丢失',7001);
                  }
                  $coverfix = substr($cv['imgdata'],strrpos($cv['imgdata'],'/'));
                  $zip->addFromString('image'.$coverfix,file_get_contents(ROOT_PATH . "public" . $cv['imgdata']));
                 }
               }
             }
           }
           //下载到本地的网络图片
           $picurl = ROOT_PATH . 'public' . DS . 'uploads' . DS . 'netpicture';
           $netPic = scandir($picurl);
           unset($netPic[0]);
           unset($netPic[1]);
           foreach($netPic as $vv){
            $zip->addFromString('image/'.$vv,file_get_contents($picurl . '/' . $vv));
           }
          $zip->close();
          $webZipPath = $inturl  . DS ."alg" . DS . $zipName;
          return get_status(0 , $webZipPath);
  }

  //取出indexjson中的图片路径
  protected function getIndexImageArr($json)
  {
      $out = $this->getimages($json);
      dump($out);
      exit();
  }
    //取得页面所有的图片地址
  function getimages($str)
  {
    $str = '"http://www.codelovers.cn/Public/upload/20180621/1529561322214.png,http://www.codelovers.cn/Public/Home/images/404.jpg ,./Public/Home/images/404.jpg';
    $preg = '/\/(?P<name>\w+\.(?:png|jpg|gif|bmp))$/i';//匹配img标签的正则表达式
    $str = ".test('http://www.baidu.com/img/a.jpg')";
    preg_match_all($preg, $str, $allImg);//这里匹配所有的img
    dump($allImg);
    dump($str);
    exit();
  }

    //删除文件及文件夹
  function delDir($path, $del = false)
  {
    //打开所选文件
    $handle = opendir($path);
    if ($handle) {
      //函数返回目录中下一个文件的文件名
      while (false !== ($item = readdir($handle))) {
        //排除. ..文件
        if (($item != ".") && ($item != "..")) {
            //删除文件
          is_dir("$path/$item") ? delDir("$path/$item", $del) : unlink("$path/$item");
        }
      }
      closedir($handle);
      if ($del) {
        //删除空目录
        return rmdir($path);
      }
      //检测文件或目录是否存在
      }elseif (file_exists($path)) {
        return unlink($path);
      }else {
          return false;
        }
  }
  //下载远程图片到本地
  public function dlfile($fileUrl,$saveUrl)
  {
    //获取文件后缀
    $suffix = substr($fileUrl,strrpos($fileUrl,'.'));
    //读取图片信息
    $content = file_get_contents($fileUrl);
    //生成文件名
    $filename = time() . rand(1000,10000) . $suffix;
    //将图片写入文件夹
    file_put_contents($saveUrl . $filename ,$content);
    //获取文件路径
    $filename;
    return $filename;
  }

  //读取json文件配置
  public static function jspath()
  {
      //读取json文件，或得服务器配置
      $jsPath = file_get_contents(config('static_config_path'));
      //将json文件转为数组
      $jsPath = json_decode($jsPath,1);
      //获取服务器配置
      $inturl = $jsPath['setting']['server'];
      return $inturl;
  }
   /**
   * 处理getscreeninfo文件
   * $indexArr   json数据
   * $zipUrl     压缩包路径
   * $inturl     接口地址
   * $netPicture 网络图片地址
   */
  public function handleGetscreeninfo($indexArr,$zipUrl,$inturl,$netPicture)
  {
      foreach($indexArr['data']['position'] as $k=>&$v){
        //判断有图片字段并且不为空，然后进行截取
        if(isset($v['chartData']['url'])){
          //判断不为空，并且不为网络图片
          if(!empty($v['chartData']['url'])){
            //修改图片路径
            $v['chartData']['url'] = $this->pictureUrl($v['chartData']['url'],$zipUrl,$inturl,$netPicture);
          }
        }
      
        //判断小图标是否存在
        if(isset($v['chartData']['iconObj']['url'])){
          //判断是否为空
          if(!empty($v['chartData']['iconObj']['url'])){
            //修改图片路径
            $v['chartData']['iconObj']['url'] = $this->pictureUrl($v['chartData']['iconObj']['url'],$zipUrl,$inturl,$netPicture);
          }
        }

        // //修改视频路径
        // if(isset($tconfig['comkey'])){
        //   //p判断是否为视频类型
        //   if($tconfig['comkey'] == 'shipinbofangqi'){
        //     if(!empty($tconfig['chartData']['playerOptions']['sources'][0]['src'])){
        //       $nameurl = $this->dlfile($tconfig['chartData']['playerOptions']['sources'][0]['src'],$netPicture);
        //       $tconfig['chartData']['playerOptions']['sources'][0]['src'] = $zipUrl . '/' . $nameurl;
        //     }
        //   }
        // }
    }
    return $indexArr['data']['position'];
  }
  /**
   * 处理背景图
   * $indexArr   json数据
   * $zipUrl     压缩包路径
   * $inturl     接口地址
   * $netPicture 网络图片地址
   */
  public function BigScreenBack($indexArr,$zipUrl,$inturl,$netPicture)
  {
    //处理大屏背景图
    $img = $indexArr['data']['screenOption'];
    //截取后替换
    if(isset($img['background']['image'])){
      if(!empty($img['background']['image'])){
        if(strpos($img['background']['image'],$inturl)){
          $img['background']['image'] = $zipUrl . substr($img['background']['image'],strrpos($img['background']['image'],'/'));
        }else{
          $nameurl = $this->dlfile($img['background']['image'],$netPicture);
          $img['background']['image'] = $zipUrl . '/' . $nameurl; 
        }
      }
    }
    return $img; 
  }
  /**
   * 导出本地部署，修改图片路径
   */
  public function pictureUrl($data,$zipUrl,$inturl,$netPicture)
  {
    if((strpos($data,$inturl) !== false) || (strpos($data,'/image') !== false)){
        //将截取后的字符串放入字段
        $data = $zipUrl . substr($data,strrpos($data,'/'));
    }else{
      $nameurl = $this->dlfile($data,$netPicture);
      $data = $zipUrl . '/' . $nameurl;
    }
    return $data;
  }
}