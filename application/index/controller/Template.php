<?php
namespace app\index\Controller;
use think\Db;
use think\Request;
use think\Session;
use think\Image;
use app\base\controller\Base;
use app\index\model\Screen;

/**
 * @param      任意变量
 *
 * @return     变量结构 数据类型
 */
 
// function get_status($err,$status,$da){
// 	$data['err'] = $err;
// 	$data['status'] = $status;
// 	$data['data'] = $da;
// 	return $data;
// }
class Template extends Base 
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
		//接收post来的数
		$put=file_get_contents('php://input'); 
		if (empty($put)) {
           return  get_status(1,"数据不能为空",7005);
		}
		$data =json_decode($put,1); 
       	//判断参数是否存在空值
		   // if(empty($data['name'])){return $return;}
		   if(empty($data['name'])){return  get_status(1,"数据不能为空",7005);}
		   
       	// //执行查询语句
       	if(empty($data['sid'])){return $return;}

       	// //验证name是否已存在
        // $selectName = Db::name('template')->where('name',$data['name'])->field('id')->find();
        $selectName = Db::name('screen')->where('name',$data['name'])->where('screentype' , 2)->field('id')->find();
        if(!empty($selectName)){		
        	return  get_status(1,"模板已存在",7004);
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
			// $ojld =  $this->ojld($config['screenOption']['width'],$config['screenOption']['height']);
			// //求出屏幕比例
			// $ratio = $config['screenOption']['width']/$ojld.':'.$config['screenOption']['height']/$ojld;
			// //将配置加入到data数组中
			// $data['pixel'] = $config['screenOption']['width'].' X '.$config['screenOption']['height'];//屏幕大小
			// $data['ratio'] = $ratio;//屏幕比例
			// $data['data'] = json_encode($config['screenOption']);//屏幕配置
			$data['createtime'] = time();
			$data['screentype'] = 2;
			$data['updatetime'] = time();
			//添加数据
			$insert = Db::name('screen')->insert($data);
			//获取大屏自增ID
			$id = Db::name('screen')->getLastInsID();
			//返回
        	if(!empty($insert)){
				//查看数据
				//$selectData = Db::name('template')->where('name',$data['name'])->find();
        		return get_status(0,['screenId' => $id]);
        	}else {
				return  get_status(1,"添加模板失败",7001);
			}
        }
       	
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
        if(empty($file)){
           return  get_status(1,NULL,NULL);
        }
		//定义上传路径
		$info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
	
		//获取路径
		$imgSrcWin = '/uploads/'.$info->getSaveName();
		$src = ROOT_PATH . 'public' .$imgSrcWin;
		// header('Content-Type: multipart/form-data;boundary=----WebKitFormBoundaryzLUTQkXZJNgeNYNP');
		// //接收base64图片
		// $input = input('post.imgdata');
		// //处理图片,获取路径
		// $imgSrcWin = base64Image($input);
		// if(!$imgSrcWin) {
		// 	return get_status(1,null,null);
		// }
		// $src = ROOT_PATH . DS .$imgSrcWin;

		// 使用图片类
		$image = \think\Image::open($src); 
		
		// 设置需保存的图片路径
		$thumbnail = './Cover' .DS. md5($info->getSaveName()) . '_thumbnail.jpg'; //缩略图
		$imageda = './Cover' .DS. md5($info->getSaveName()) . '_image.jpg';	//小图

		$image->thumb(100, 68)->save($thumbnail);
		$image->thumb(250, 200)->save($imageda);
		
		//设置存储路径
		$imgdata['thumbnail'] = ltrim($thumbnail,'.');
		$imgdata['image'] = ltrim($imageda,'.');
		//取出src中最后一个点后面的内容
		$newSrc = substr($src,0,strrpos($src, '.')+1);
		$imgSrcWin = substr($imgSrcWin,0,strrpos($imgSrcWin, '.')+1);
		rename($src,$newSrc.'png');
		$imgdata['src'] = ltrim($imgSrcWin.'png','.');
		$imgdata['imgdata'] = ltrim($imgSrcWin.'png','.');
		//设置更改语句
		$data = Db::name('screen')->where('id',$post['id'])->update($imgdata);
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
		//判断模版类型
		if(!isset($get['tempcate'])){
			$get['tempcate'] = 'Universal';
		}
		if($get['tempcate'] == 'Universal'){
			$get['tempcate'] = 0;
		}else{
			$get['tempcate'] = 1;

		}

	
		$where = [
			'tempcate'  =>$get['tempcate'],
			'screentype'=>2
		];
		$get['currentPage'] = issetKey($get , 'currentPage' , 1);
		$get['pageSize'] = issetKey($get , 'pageSize' , 20);
		
		$data = new Screen;
		$data = $data->templateSummary($where,$get['order'],$get['currentPage'],$get['pageSize'],$get['searchword']);

		//查找用户表
		$user = Db::name('user')->field('uid,username')->select();
		foreach($data['list'] as $k=>&$v){
			foreach($user as $key=>$val){
				if($v['publishuser'] == $val['uid']){
					$v['creater'] = $val['username'];
				}
			}
			unset($v['publishuser']);
		}

		//返回数据
		return  ['err'=>0,'status'=>0,'data'=>$data];


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
		$selectId = Db::name('screen')->where('id',$data['id'])->field('data')->find();

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
		//接收delete来的数
		$data = input("post.");
		//判断参数是否存在空值
       	if(empty($data['id'])){
			return get_status(1,'数据接收失败',2000);
		}
		//查询该模板是否属于默认模板
		$tmp = Db::name('screen')->where('id',$data['id'])->field('tmp,share,tempcate,screen_sid')->find();
		//默认模版进行软删除
		if($tmp['tmp']){
			$screen = new Screen;
			$delete = $screen->softdel($data['id']);
		}else{
			if($tmp['tempcate'] == 0 && $tmp['share'] == 1){
			$del = Db::name('screen')->where('id',$tmp['screen_sid'])->update(['share'=>0]);			
			$delete = Db::name('screen')->where('id',$data['id'])->delete();
			//删除模板下的图表信息
			$deleteChart = Db::name('screenchart')->where('screenid', $data['id'])->delete();
			//删除图表配置
			$deleteCharttconfig = Db::name('screencharttconfig')->where('screenid', $data['id'])->delete();
			}else{
			//删除模板
			$delete = Db::name('screen')->where('id',$data['id'])->delete();
			//删除模板下的图表信息
			$deleteChart = Db::name('screenchart')->where('screenid', $data['id'])->delete();
			//删除图表配置
			$deleteCharttconfig = Db::name('screencharttconfig')->where('screenid', $data['id'])->delete();
			}
		}
       	if(!$delete){
       		return  get_status(1,"删除失败",7006);
       	}else{
       		return  get_status(0,'删除成功');
       	}


	}

	

	//修改模板数据
	public  function updateTemplate()
	{
		//接收post来的数
		$put=file_get_contents('php://input'); 
		//json->数组 数据类型转换
		$data =json_decode($put,1); 
		//检测名称是否为空
		if(empty($data['name'])){
			return get_status(1,'大屏名称不能为空',2029);
		}
		$udata = $data;
		unset($udata['id']);
		$return['err'] = 1;
		$return['data'] = [];
		//判断参数是否存在空值
       	if(empty($data['id'])){
			   return $return;
		}
		
       	if(!empty($data['data'])){
       		$selectData  = Db::name('screen')->where('id',$data['id'])->find();
	       	if($selectData['data'] == $data['data']){
	       		return $return['err'] = 3;
	       	}
       	}
        $da = Db::name('screen')->where(['name'=>$udata['name']])->find();
        if($da){
            return get_status(1,'大屏名称已重复',2003);
        }
       	$update = Db::name('screen')->where('id',$data['id'])->update($udata);
			  
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

		//base64
		// header('Content-Type: multipart/form-data;boundary=----WebKitFormBoundaryzLUTQkXZJNgeNYNP');
		// //接收base64图片
		// $input = input('post.imgdata');
		// //处理图片,获取路径
		// $imgSrcWin = base64Image($input);
		// if(!$imgSrcWin) {
		// 	return get_status(1,null,null);
		// }
		// $src = ROOT_PATH . DS .$imgSrcWin;


		$image = \think\Image::open($src); 
		//$image = Image::open($src);
		
		// 按照原图的比例生成一个最大为150*150的缩略图并保存为thumb.png 
		$thumbnail = './Cover/' . md5($info->getSaveName()) . '_thumbnail.jpg';
		$imageda = './Cover/' . md5($info->getSaveName()) . '_image.jpg';
		
		$image->thumb(100, 68)->save($thumbnail);
		$image->thumb(250, 200)->save($imageda);
		if(isset($post['scale'])) {
			//定义图片路径
			$src = ROOT_PATH . 'public' . DS . 'uploads'. DS .$info->getSaveName();
			//获取图片信息
			$img_info = getimagesize($src);
			//获取图片宽高
			$width = $img_info[0];
			$height = $img_info[1];
			//缩放倍数
			$scale = intval($post['scale']);
			//缩略后的宽高
			$scaleWidth = $width / $scale;
			$scaleHeight = $height / $scale;
			//打开图片资源
			$image = \think\Image::open($src); 
			//定义修改后图片位置
			$imgSrcWin = './Cover/' . md5($info->getSaveName().mt_rand(1000,9999)) . '_image.jpg';
			//缩略并保存图片
			$image->thumb($scaleWidth, $scaleHeight)->save($imgSrcWin);
		}
		//取消路径前的点
        $imgSrcWin = substr($imgSrcWin,1);
		//设置存储路径
		$imgdata['imgdata'] = '/'.$imgSrcWin;
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
		return  get_status($ifdata,$imgdata['imgdata'],null);
		
	}

	//分享模版
	public function shareTemplate()
	{
		$post = input('post.');

		// $share = Db::name('screen')->where('id',$post['id'])->value('share');
		
		//分享模板
		$update = Db::name('screen')->where('id',$post['id'])->update(['share'=>1]);

		$id = $post['id'];
		//执行查询语句
		$data = Db::name('screen')->where('id',$id)->find();
		//销毁查询出来的id防止id重复
		unset($data['id']);
        //------预留 模板名称替换位置
        $name = $data['name'];
        $data['name'] = $name.'的分享';

	
		//查询大屏名字是否存在
		$result = Db::name("screen")->where("name" , $data['name'])->select();
		if(!$result) {
			$data['createtime'] = time();
			$data['updatetime'] = time();
			$data['publish'] = 0;			
			$data['tempcate'] = 0;
			$data['share'] = 1;
			$data['tmp'] = 0;
			$data['screen_sid'] = $id;
			//插入新的大屏信息
			Db::name('screen')->insert($data);
			//获取大屏自增ID
			$screenid = Db::name('screen')->getLastInsID();
		}
   
		//查询大屏相关图表
		$screenchart = Db::name('screenchart')->where('screenid',$id)->select();
		//判断大屏是否有图表
		if($screenchart) {
			//遍历所有图表信息
			foreach ($screenchart as $key => $value) {
				//销毁自增ID
				unset($screenchart[$key]['tid']);
				$screenchart[$key]['screenid'] = $screenid;
				$screenchart[$key]['createtime'] = time();
				$screenchart[$key]['updatetime'] = time();
			}
			//插入图表信息
			$screeninsert =  Db::name('screenchart')->insertAll($screenchart);
		}
		//查询相关tconfig
		$tconfig = Db::name('screencharttconfig')->field('id',true)->where('screenid',$id)->select();
			
		if($tconfig){
			//新增图表id
			$tid = Db::name('screenchart')->field('tid')->where('screenid',$screenid)->select();
			foreach($tconfig as $k=>&$v){
				$v['screenid'] = $screenid;
				$v['tid'] = $tid[$k]['tid'];
			}
			$tconfiginsert =  Db::name('screencharttconfig')->insertAll($tconfig);
		}
		//返回大屏列表
		return get_status(0,'分享成功');

		
	}

	//恢复默认模板
	public function resetTemplate()
	{
		$screen = new Screen;
		$rec = $screen->recsof();
		if($rec){
			return get_status(0,'恢复成功');
		}else{
			return get_status(1,'恢复默认模板失败',7009);
		}
		
	}


	
	
}
