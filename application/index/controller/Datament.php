<?php

namespace app\index\Controller;

use think\Db;
use think\Request;
use think\Session;
use \app\index\controller\User;
use PhpOffice\PhpSpreadsheet\IOFactory;


class Datament
{
  protected $token;
    
  public function __construct(Request $request = null)
  {
      if (strtolower($_SERVER['REQUEST_METHOD']) == 'options') {
         exit();
      }
      $user = new User;
      $user->keepLogging();
  }

  //添加Excel、Csv
  public function addCsv()
  {
    // //获取表单上的文件
    // $file = request()->file('file');
    // //获取表单信息
    $post = input('post.');
    //判断是否有对应的键
    $vali = valiKeys(['cid','dataname','datatype','filepath'],$post);
    if($vali['err']) {
      return get_status(1,$vali['data'].'不能为空',333);
    }
    // // dump($post);
    //定义文件上传路径
    $path = ROOT_PATH . 'public';
    
    $path = str_replace( '\\' , '/' , $path);
    /*单个文件 
    $filepath = $path  . $post['filepath'];
    // $filepath = $post['filepath'];
    $new_name = str_replace('\\','/',$filepath);

    
    $objPHPExcel = IOFactory::load($new_name);
    //生成返回数据
    $excel_array = $objPHPExcel->getSheet(0)->toArray();

      //将数组0下标提取组成新数组
      $newarr = $excel_array[0];
      //销毁0下标数据
      unset($excel_array[0]);
      $arr = [];
      $i = 0;
      if(empty($excel_array)) {
        return  get_status(1, "文件为空",4010);
      }
     
      foreach ($excel_array as $key => $value) {
        $j = 0;
        foreach ($newarr as $k => $val) {
          $arr[$i][$val] = $value[$j];
          $j++;
        }
        $i++;
      }
      */
       
      /*多个文件*/
      //定义存储数组用于存储多个表格数据
      $excelArrList = [];
      //定义存储下标的数组
      $subscript = [];
      //获取数组长度最大数组下标
      $long = 0;
      $maxKey = 0;
      $maxi = 0;
      if(empty($post['filepath'])){
          return  get_status(1, "文件为空",4010);
      }
      //遍历文件路径
      foreach($post['filepath'] as $value) {
        $filepath = $path  . $value;
        // $filepath = $post['filepath'];
        $new_name = str_replace('\\','/',$filepath);
        $objPHPExcel = IOFactory::load($new_name);
        //生成返回数据
        $excel_array = $objPHPExcel->getSheet(0)->toArray();

          //将数组0下标提取组成新数组
          $newarr = $excel_array[0];

          //将所有出现的下标放入
          foreach($newarr as $key => $value) {
            //判断下边是否已经存在
            if(!in_array($value,$subscript)) {
              $subscript[] = strval($value);
            }else  {
              $onlyKey = $this->onlyKey($value,$subscript);
              $subscript[] = $onlyKey;
              $newarr[$key] = $onlyKey;
            }
          }
          
          //销毁0下标数据
          unset($excel_array[0]);
         
          $i = 0;
          if(empty($excel_array)) {
            return  get_status(1, "文件为空",4010);
          }
         
          foreach ($excel_array as $key => $value) {
            $j = 0;
            foreach ($newarr as $k => $val) {
              $excelArrList[$i][$val] = $value[$j];
              $j++;
            }
            $i++;
          }
      }

      //将数据转成json格式
      $jarr = json_encode($excelArrList,JSON_UNESCAPED_UNICODE );

      //获取对应的分类id
      $screenname = Db::name('screengroup')->where('screenname',$post['cid'])->value('sid');

      //将表单信息组成数组
      $data = [
        'data' => $jarr,
        'createtime' => date('Y-m-d H:i:s', time()),
        'filepath' => $new_name,
        'datatype' => $post['datatype'],
        'dataname' => $post['dataname'],
        'cid' => $post['cid'],
          'ccid'=>$screenname,
      ];
      //判断表单数据是否有空
      if (empty($data['dataname']) || empty($data['cid']) || empty($data['datatype'])) {
        return get_status(2, '不能为空',4003);
      }
      //查询是否有重复名
      $val = Db::name('datament')->where('dataname', $post['dataname'])->find();
      if ($val) {
        return get_status(3, '文件名重复' , 4006);
      }
      //将数组插入数据库
      $result = Db::name('datament')->insert($data);
      if ($result) {
        return  get_status(0, '添代成功');
      } else {
        return  get_status(1, null);
      }
    }

    //将数组中的值设置为唯一
    protected function onlyKey($value,$subscript)
    {
     
        foreach ($subscript as $val) {
          if($val == $value){
              return $this->onlyKey($val.'(1)',$subscript);
          }
        }
        return $value;
    }
    
  //修改exce、Csv
  public function  updateDataSrc()
  {
      //定义文件上传路径
      $path = ROOT_PATH . 'public';
      $path = str_replace( '\\' , '/' , $path);
      //定义存储数组用于存储多个表格数据
      $excelArrList = [];
      //定义存储下标的数组
      $subscript = [];
    //获取表单信息
    $put = input('put.');
    $vali = valiKeys(['daid'] , $put);
    if($vali['err']) {
        return get_status( 1 , '参数不完整' , 1050);
    }
    //查询数据源信息
    $dataInfo = Db::name('datament')->where(['daid'=>$put['daid']])->find();
    if(!$dataInfo) { return get_status( 1 , '数据修改失败' , 4005); }

    //判断有没有传名字
    if(isset($put['dataname'])) {
        //检测是否重命名
        $dataname = Db::name('datament')->where(['dataname'=>$put['dataname']])->where('daid','<>',$put['daid'])->find();
        if($dataname){
            return get_status(1, '文件名重复' , 4006);
        }
    }else {
        $put['dataname'] = $dataInfo['dataname'];
    }
    //新建的时候传的是数组，修改的时候传的是字符串
    if(is_array($put['filepath'])){
       foreach($put['filepath'] as $value){
           $filepath = $path  . $value;
           // $filepath = $post['filepath'];
           $new_name = str_replace('\\','/',$filepath);
           $objPHPExcel = IOFactory::load($new_name);
           //生成返回数据
           $excel_array = $objPHPExcel->getSheet(0)->toArray();

           //将数组0下标提取组成新数组
           $newarr = $excel_array[0];

           //将所有出现的下标放入
           foreach($newarr as $key => $value) {
               //判断下边是否已经存在
               if(!in_array($value,$subscript)) {
                   $subscript[] = strval($value);
               }else  {
                   $onlyKey = $this->onlyKey($value,$subscript);
                   $subscript[] = $onlyKey;
                   $newarr[$key] = $onlyKey;
               }
           }
           //销毁0下标数据
           unset($excel_array[0]);
           $i = 0;
           if(empty($excel_array)) {
               return  get_status(1, "文件为空",4010);
           }
           foreach ($excel_array as $key => $value) {
               $j = 0;
               foreach ($newarr as $k => $val) {
                   $excelArrList[$i][$val] = $value[$j];
                   $j++;
               }
               $i++;
           }
       }
       $put['filepath'] = $new_name;
    }
    //修改时如果没有选择文件filepath则不是一个数组，需要判断和api区分
    //也可以通过是否有http判断是excel还是api
      if($put['datatype'] == 'excel/csv'){
          $filepath = $put['filepath'];
          // $filepath = $post['filepath'];
          $new_name = str_replace('\\','/',$filepath);
          $objPHPExcel = IOFactory::load($new_name);
          //生成返回数据
          $excel_array = $objPHPExcel->getSheet(0)->toArray();

          //将数组0下标提取组成新数组
          $newarr = $excel_array[0];

          //将所有出现的下标放入
          foreach($newarr as $key => $value) {
              //判断下边是否已经存在
              if(!in_array($value,$subscript)) {
                  $subscript[] = strval($value);
              }else  {
                  $onlyKey = $this->onlyKey($value,$subscript);
                  $subscript[] = $onlyKey;
                  $newarr[$key] = $onlyKey;
              }
          }
          //销毁0下标数据
          unset($excel_array[0]);
          $i = 0;
          foreach ($excel_array as $key => $value) {
              $j = 0;
              foreach ($newarr as $k => $val) {
                  $excelArrList[$i][$val] = $value[$j];
                  $j++;
              }
              $i++;
          }
          $put['filepath'] = $new_name;
      }

      //将数据转成json格式
      $jarr = json_encode($excelArrList,JSON_UNESCAPED_UNICODE );
      if(!isset($put['cid'])) {
          $put['cid'] = $dataInfo['cid'];
      }
      //根据分类名字查找分类id
      $ccid = Db::name('screengroup')->where('screenname',$put['cid'])->value('sid');

    //将表单信息组成数组
    $data = [
      'datatype' => $put['datatype'],
      'filepath' => $put['filepath'],
        'data'   =>$jarr,
        'ccid'   =>$ccid
    ];
    if(isset($put['dataname'])) {
      $data['dataname'] = $put['dataname'];
    } 
    if(isset($put['dataname'])) {
      $data['cid'] = $put['cid'];
    } 

    //查询和表单信息是否一致
    $vali = Db::name('datament')->where($data)->find();

    //如果一致则无需修改，直接返回
    if ($vali) {
      return get_status(0, '修改成功');
    }
    $update = Db::name('datament')->where('daid', $put['daid'])->update($data);
    if ($update) {
      return get_status(0, '修改成功');
    } else {
      return get_status(1, '修改失败' , 4005);
    }
  }

  /**
   * 修改SQL
   */
  public function updateSql()
  {
    //接收数据
    $input = input('get.');
    //判断数据是否接收成功
    if(!$input) {
        return get_status(1,'数据接收失败');
    }

  }
  //数据列表
  public function datalist()
  {
       //获取用户token
       $arr = get_all_header();
       if(isset($arr['token'])){
           $this->token = $arr['token'];    
       }else {
           $data = get_status(1,'非法用户访问',10001);
           return $data;
       }
       
        //通过token获取用户的uid
      $uid = Db::name('token')->where('token',$this->token)->field('uid')->find();
        //通过uid获取用户的sid
       
      $category = Db::name('user')->where('uid',$uid['uid'])->field('sid')->find();	
      $cate = explode(',',$category['sid']);
    //分组 分类
    $datatype = Db::name('screengroup')->where('sid','in',$cate)->field('sid,screenname')->select();
    for($i = 0;$i < count($datatype); $i++) {
      $sid[] = $datatype[$i]['screenname'];
    }

    //判断该用户是否有分组
    if(empty($sid)){
      return ['err' => 1,'status' => 0,'data'=>[],'type' =>$datatype];
    }
    //获取数据
    $get = input('get.');
    //判断是否有有搜索关键字
		if(!isset($get['searchword'])) {
			//设置默认搜索关键字
			$get['searchword'] = '';
		}else {
			//去掉首尾空格
			$get['searchword'] = rtrim($get['searchword']);
		}
		//判断是否排序
		if(!isset($get['order'])) {
			//设置默认排序规则
			$get['order'] = 'daid';
		}else{
			//去掉首尾空格
			$get['order'] = rtrim($get['order']);
		}
    //判断是否有sid
    if(!isset($get['sid'])){
      $get['sid'] = 0;
    }

    //判断是否有datatype
    if(!isset($get['datatype'])){
      $get['datatype'] = 'all';
    }
    //判断是否有cid
    if(!isset($get['cid'])){
      $get['cid'] = 'all';
    }
     //查询对应的分类
     $group = Db::name('screengroup')->where('screenname',$get['cid'])->find();
    
    //分页 $currentPage第几页
    if(isset($get['currentPage'])){
      $currentPage = $get['currentPage'];
    }else{
      $currentPage = 1;
    }

    //分页 $pageSize每条页数
    if(isset($get['pageSize'])){
      $pageSize = $get['pageSize'];
    }else{
      $pageSize = 10;
    }

    if($group['screenname'] == ''){
      //判断是否有datatype
      if($get['datatype'] == 'all' ){
        $data = Db::name('datament')->where('dataname' , 'like' , "%".$get['searchword']."%")
                                    ->where('cid','in',$sid)
                                    ->page($currentPage.','.$pageSize)
                                    ->order($get['order'].' DESC')
                                    ->select();
        //查询总条数
        $total = Db::name('datament')->where('cid','in',$sid)->where('dataname','like',"%".$get['searchword']."%")->count();
      }else{
        $data = Db::name('datament')->where('datatype',$get['datatype'])
                                    ->where('cid','in',$sid)
                                    ->where('dataname','like',"%".$get['searchword']."%")
                                    ->page($currentPage.','.$pageSize)
                                    ->order($get['order'].' DESC')
                                    ->select();
        //查询总条数 
      $total = Db::name('datament')->where('cid','in',$sid)->where('datatype',$get['datatype'])->where('dataname','like',"%".$get['searchword']."%")->count();
      }
      
    }else{
     
      //判断是否有datatype
      if($get['datatype'] == 'all'  ){
        $data = Db::name('datament') ->where('dataname','like', "%".$get['searchword']."%")
                                    ->where('cid',$group['screenname'])
                                    ->page($currentPage.','.$pageSize)
                                    ->order($get['order'].' DESC')
                                    ->select();

        //查询总条数
        $total = Db::name('datament')->where('cid',$group['screenname'])->where('dataname' , 'like' , "%".$get['searchword']."%")->count();
      }else{
        $data = Db::name('datament')->where('datatype',$get['datatype'])
                                    ->where('cid',$group['screenname'])
                                    ->where('dataname' , 'like' , "%".$get['searchword']."%")
                                    ->page($currentPage.','.$pageSize)
                                    ->order($get['order'].' DESC')
                                    ->select();
        //查询总条数
        $total = Db::name('datament')->where('cid',$group['screenname'])->where('datatype',$get['datatype'])->where('dataname' , 'like' , "%".$get['searchword']."%")->count();

      }
         
    }
    //查询数据类型
    $databasesList = Db::name('datamentname')->select();
    
    return ['err'=>0,'status'=>0,'cid'=>$datatype,'datalist'=>$data,'datatype' => $databasesList,'total' => $total];
  }
  
  //搜索
  public function search()
    {
         //获取用户token
       $arr = get_all_header();
       if(isset($arr['token'])){
           $this->token = $arr['token'];    
       }else {
           $data = get_status(1,'非法用户访问',10001);
           return $data;
       }
       
        //通过token获取用户的uid
      $uid = Db::name('token')->where('token',$this->token)->field('uid')->find();
        //通过uid获取用户的sid
       
      $category = Db::name('user')->where('uid',$uid['uid'])->field('sid')->find();	
      $cate = explode(',',$category['sid']);
      
    //分组 分类
    $datatype = Db::name('screengroup')->where('sid','in',$cate)->field('sid,screenname')->select();
    for($i = 0;$i < count($datatype); $i++) {
      $sid[] = $datatype[$i]['sid'];
    }

    //判断该用户是否有分组
    if(empty($sid)){
      return ['err' => 1,'status' => 0,'data'=>[],'type' =>$datatype];
    }
    //获取数据
    $get = input('get.');
    //判断是否有有搜索关键字
		if(!isset($get['searchWord'])) {
			//设置默认搜索关键字
			$get['searchWord'] = '';
		}else {
			//去掉首尾空格
			$get['searchWord'] = rtrim($get['searchWord']);
		}
		//判断是否排序
		if(!isset($get['order'])) {
			//设置默认排序规则
			$get['order'] = 'daid';
		}else{
			//去掉首尾空格
			$get['order'] = rtrim($get['order']);
		}
    //判断是否有sid
    if(!isset($get['sid'])){
      $get['sid'] = 0;
    }

    //判断是否有datatype
    if(!isset($get['datatype'])){
      $get['datatype'] = 'all';
    }
    //判断是否有cid
    if(!isset($get['cid'])){
      $get['cid'] = 'all';
    }
     //查询对应的分类
     $group = Db::name('screengroup')->where('screenname',$get['cid'])->find();
    
    
    //分页 $currentPage第几页
    if(isset($input['currentPage'])){
      $currentPage = $input['currentPage'];
    }else{
      $currentPage = 1;
    }

    //分页 $pageSize每条页数
    if(isset($input['pageSize'])){
      $pageSize = $input['pageSize'];
    }else{
      $pageSize = 10;
    }

    if($group['screenname'] == ''){
      //判断是否有datatype
      if($get['datatype'] == 'all' ){
      
        $data = Db::name('datament')->where('dataname' , 'like' , "%".$get['searchWord']."%")
                                    ->page($currentPage.','.$pageSize)
                                    ->order($get['order'].' DESC')
                                    ->group('dataname')
                                    ->column('dataname');	
                                    
      }else{
        
        $data = Db::name('datament')->where('datatype',$get['datatype'])
                                    ->where('dataname','like',"%".$get['searchWord']."%")
                                    ->page($currentPage.','.$pageSize)
                                    ->order($get['order'].' DESC')
                                    ->group('dataname')
                                    ->column('dataname');	
      }
    }else{
      //判断是否有datatype
      if($get['datatype'] == 'all'  ){
    
        $data = Db::name('datament') ->where('dataname','like', "%".$get['searchWord']."%")
                                    ->where('cid',$group['screenname'])
                                    ->page($currentPage.','.$pageSize)
                                    ->order($get['order'].' DESC')
                                    ->group('dataname')
                                    ->column('dataname');	
      }else{
        $data = Db::name('datament')->where('datatype',$get['datatype'])
                                    ->where('cid',$group['screenname'])
                                    ->where('dataname' , 'like' , "%".$get['searchWord']."%")
                                    ->page($currentPage.','.$pageSize)
                                    ->order($get['order'].' DESC')
                                    ->group('dataname')
                                    ->column('dataname');	
      }
    }
        	
	    //返回数据
		return  get_status(0,$data);
    }

  //调整分类
  public function transfer()
  {
    $post = input('put.');
    //判断是否和原分类一样
    $cid = Db::name('datament')->where('daid', $post['daid'])->where('cid', $post['cid'])->find();
    if ($cid) {
      return get_status(0, '调整成功');
    }
    $result = Db::name('datament')->where('daid', $post['daid'])->update(['cid' => $post['cid']]);
    if ($result) {
      return get_status(0, '调整成功');
    } else {
      return get_status(1, '调整失败',4007);
    }
  }

  //删除
  public function deldata()
  {
    //获取数据 id
    $post = input('delete.');
    //查询对应数据
    $result = Db::name('datament')->where('daid', $post['daid'])->delete();
    //判断成功否
    if ($result) {
      return get_status(0, '删除成功');
    } else {
      return get_status(1, '删除失败' , 4008);
    }
  }

  //HTTP代理
  public function agent()
  {
    //获取数据
    $post = input('post.');
    //提取API数据
    $data = file_get_contents($post['api']);
    if($data){
      return get_status(0,$data);
    }else{
      return get_status(0,null);
    }
  }

}
