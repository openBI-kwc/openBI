<?php
namespace app\index\Controller;
use think\Db;
use think\Request;
use think\Session;
use think\Image;
use app\base\controller\Base;
/**
 * @param      任意变量
 *
 * @return     变量结构 数据类型
 */

class Template
{
	//获取分类方法
	public function categain()
	{
		$uid = Db::name('token')->where('token',$this->token)->field('uid')->find();
		$category = Db::name('user')->where('uid',$uid['uid'])->field('sid')->find();	
		$cate = explode(',',$category['sid']);
		$groupdata = Db::name('screengroup')->where('sid','in',$cate)->field('sid,screenname')->select();
		return $groupdata;
       
	}

	//创建模板
	public  function templateInfo()
	{

        $return['err'] = 1;
		$return['data'] = [];

		//接收post来的数
		$put=file_get_contents('php://input'); 
		if (empty($put)) {
           return $return;
		}
		
		$data =json_decode($put,1); 
       	

				//dump($data);
       	//判断参数是否存在空值
       	if(empty($data['name'])){return $return;}
       	// //执行查询语句
       	if(empty($data['sid'])){return $return;}

       	// //验证name是否已存在
        $selectName = Db::name('template')->where('name',$data['name'])->field('id')->find();
        //dump($selectName);
        if(!empty($selectName)){
			$return['err'] = 1;
			$return['data'] = '模板已存在';
        	return $return;
        }else{
			//前端配置文件路径
			// $path = config('static_config_path');
			$path = config('static_config_path');
			//读取默认大屏配置
			$file = file_get_contents($path);
			//转成数组
			$config = json_decode($file,1);
			// 将大屏信息取出
			// dump($config);
			//求最小公倍数
			$ojld =  $this->ojld($config['screenOption']['width'],$config['screenOption']['height']);
			//求出屏幕比例
			$ratio = $config['screenOption']['width']/$ojld.','.$config['screenOption']['height']/$ojld;
			//将配置加入到data数组中
			$data['pixel'] = $config['screenOption']['width'].','.$config['screenOption']['height'];//屏幕大小
			$data['ratio'] = $ratio;//屏幕比例
			$data['data'] = json_encode($config['screenOption']);//屏幕配置
			//添加数据
			$insert = Db::name('template')->insert($data);
			//返回
        	if($insert == 1){
				//查看数据
				$selectData = Db::name('template')->where('name',$data['name'])->find();
        		$return['err'] = 0;
        		$return['data'] = $selectData;
        		return $return;
        	}else {
				$return['err'] = 1;
				$return['data'] = '添加数据失败';
				return $return;
			}
        }
       	
	}

	public function  microtime_float()
	{
		$ojld =  $this->ojld(1920,1200);
		echo 1920/$ojld;
		echo '<br />';
		echo 1200/$ojld;
	}

	public function ojld($m, $n) 
	{
		if($m ==0 && $n == 0) {
			return false;
		}
		if($n == 0) {
			return $m;
		}
		while($n != 0){
			$r = $m % $n;
			$m = $n;
			$n = $r;
		}
		return $m;
	}

	/**
	 * 更改模板封面
	 *
	 * @return    成功返回err:0  失败返回err:1 \
	 */
	public function updateTemplateCover()
	{
		//接收post来的数据
		$post = input('post.');
		//执行上传
		$file = request()->file('imgdata');

		//定义上传路径
		$info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
		//获取路径
		$imgSrcWin = '/uploads/'.$info->getSaveName();
		
		
		$src = ROOT_PATH . 'public' .$imgSrcWin;

		$image = \think\Image::open($src); 
		//$image = Image::open($src);
		
		// 按照原图的比例生成一个最大为150*150的缩略图并保存为thumb.png 
		$thumbnail = './Cover/' . md5($info->getSaveName()) . '_thumbnail.jpg';
		$imageda = './Cover/' . md5($info->getSaveName()) . '_image.jpg';
		
		$image->thumb(180, 140)->save($thumbnail);
		$image->thumb(250, 200)->save($imageda);
		//设置存储路径
		$imgdata['src'] = $imgSrcWin;
		$imgdata['thumbnail'] = $thumbnail;
		$imgdata['image'] = $imageda;
		
		//设置更改语句
		$data = Db::name('template')->where('id',$post['id'])->update($imgdata);
		//判断是否更改成功
		if($data == null){
			$ifdata = 1;
		}else{
			$ifdata = 0;
		}
	
		//返回数据
		return  get_status($ifdata,null,null);
		
	}


	//获取模板列表
	public function templateSummary()
	{
		//获取分类数据
		//$groupdata = $this->categain();

		//将用户分组加入数组
    	// for ($i=0; $i < count($groupdata); $i++) { 
    	// 	$sid[] = $groupdata[$i]['sid'];
    	// }
    	//判断该用户是否有分组
  //   	if(empty($sid)){
  //   		return ['err'=>0,'status'=>0,'data'=>[],'cate'=>$groupdata];
		// }
		//接收get数据
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
			$get['order'] = 'id';
		}else{
			//去掉首尾空格
			$get['order'] = rtrim(strtolower($get['order']));
		}
		//判断是否有sid
		if(!isset($get['sid'])) {
			$get['sid'] = 0;
		}
		
        if ($get['sid'] == 0) {
			$data = Db::name('screen')->where('sid','in',$sid)
									  ->where('name' , 'like' , "%".$get['searchword']."%")
									  ->field('id,name,imgdata,lock,publish')
									  ->order($get['order'].' DESC')
									  ->select();
        }else{
			$data = Db::name('screen')->where('sid',$get['sid'])
									  ->where('name' , 'like' , "%".$get['searchword']."%")
									  ->field('id,name,imgdata,lock,publish')
									  ->order($get['order'].' DESC')
									  ->select();
									
		}	
		
	    //返回数据
		return  ['err'=>0,'status'=>0,'data'=>$data,'cate'=>$groupdata];
		
	

     	//开始查询
		// $selectList = Db::name('template')->field('id,name,time,ratio,pixel,thumbnail,image as imgdata,src')->select();
		// $return['err'] = 0;
		// $return['data'] = [];
		// if(empty($selectList)){
		// 	return $return;
		// }else{
		// 	$return['err'] = 0;
		// 	$return['data'] = $selectList;
		// 	return $return;
		// }

	}

	//获取模板数据
	public function getTemplate()
	{
		//接收post来的数
		$put=file_get_contents('php://input'); 
		//json->数组 数据类型转换
		$data =json_decode($put,1); 

		$return['err'] = 1;
		$return['data'] = [];

		//判断参数是否存在空值
       	if(empty($data['id'])){return $return;}

       	//开始查询
		$selectId = Db::name('template')->where('id',$data['id'])->field('data')->find();

		if(empty($selectId)){
			return $return;
		}else{
			$return['err'] = 0;
			$return['data'] = $selectId['data'];
			return $return;
		}

	}

	//删除模板
	public function deleteTemplate()
	{
		//接收post来的数
		$put=file_get_contents('php://input'); 
		//json->数组 数据类型转换
		$data =json_decode($put,1); 
		$return['err'] = 1;
		$return['data'] = [];

		//判断参数是否存在空值
       	if(empty($data['id'])){return $return;}

       	$delete = Db::name('template')->where('id',$data['id'])->delete();

       	if($delete == 0){
       		return $return;
       	}else{
       		$selectList = Db::name('template')->field('id,name,time,ratio,pixel,thumbnail,image')->select();
       		$return['data'] = $selectList;
       		$return['err'] = 0;
       		return $return;
       	}


	}
	

	//修改模板数据
	public  function updateTemplate()
	{
		//接收post来的数
		$put=file_get_contents('php://input'); 
		//json->数组 数据类型转换
		$data =json_decode($put,1); 

		$udata = $data;
		unset($udata['id']);
		$return['err'] = 1;
		$return['data'] = [];
		//判断参数是否存在空值
       	if(empty($data['id'])){return $return;}
       	if(!empty($data['data'])){
       		$selectData  = Db::name('template')->where('id',$data['id'])->find();
	       	if($selectData['data'] == $data['data']){
	       		return $return['err'] = 3;
	       	}
       	}
       	
       	
       	$update = Db::name('template')->where('id',$data['id'])->update($udata);

       	if($update == 0){
       		return $return;
       	}else{
       		$return['err'] = 0;
       		return $return;
       	}

	}

	public function updateCover()
	{
		//接收post来的数据
		$post = input('post.');
		//执行上传
		$file = request()->file('imgdata');
        if (empty($file)) {
           return  get_status(1,null,null);
        }
		//定义上传路径
		$info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
		//获取路径
		$imgSrcWin = '/uploads/'.$info->getSaveName();
		
		
		$src = ROOT_PATH . 'public' .$imgSrcWin;

		$image = \think\Image::open($src); 
		//$image = Image::open($src);
		
		// 按照原图的比例生成一个最大为150*150的缩略图并保存为thumb.png 
		$thumbnail = './Cover/' . md5($info->getSaveName()) . '_thumbnail.jpg';
		$imageda = './Cover/' . md5($info->getSaveName()) . '_image.jpg';
		
		$image->thumb(180, 140)->save($thumbnail);
		$image->thumb(250, 200)->save($imageda);
		//设置存储路径
		$imgdata['imgdata'] = $imgSrcWin;
		//$imgdata['thumbnail'] = $thumbnail;
		//$imgdata['image'] = $imageda;
		
		//设置更改语句
		$data = Db::name('screendir')->where('id',$post['id'])->update($imgdata);
		//判断是否更改成功
		if($data == null){
			$ifdata = 1;
		}else{
			$ifdata = 0;
		}
	
		//返回数据
		return  get_status($ifdata,null,null);
		
	}


	
	
}
