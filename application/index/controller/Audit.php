<?php
namespace app\index\controller;
use think\Db;
use think\Request;


class Audit 
{
	//新建邮件接口
	public function creatMail()
	{
		//接收post来的数
		$put=file_get_contents('php://input');  
		//json->数组 数据类型转换
		$post =json_decode($put,1);
		//$post['userid'] = 1;
		//组装添加的数据
		$data['userid'] = $post['userid'];
		$data['inserttime'] = time();
		$data['endtime'] = time();
		//判断是否有传入userid
		if(!empty($post['userid'])){
			//执行添加语句
			$DB = Db::name('audit')->insertGetId($data);
			$return['err'] = 0;
			$return['mailid'] = $DB;
			return $return;
		}else{
			$return['err'] = 1;
			return $return;
		}

	}

	//图片上传
	public function uploadImg()
	{

		//接收post来的数据
		$post = input('post.');
		//执行上传
		$file = request()->file('imgurl');

		//定义上传路径
		$info = $file->move(ROOT_PATH . 'public' . DS . 'mailimg');
		//获取路径
		$imgSrcWin = '/mailimg/'.$info->getSaveName();
		
		//设置存储路径
		$imgdata['imgurl'] = $imgSrcWin;
		$imgdata['userid'] = $post['userid'];
		$imgdata['mailid'] = $post['mailid'];

		//执行添加语句
		$data = Db::name('mailimage')->insertGetId($imgdata);

		//判断是否添加成功并返回id
		if(empty($data)){
			$return['err'] = 1;
		}else{
			$imgurl= Db::name('mailimage')->where('imgid',$data)->field('imgurl')->find();
			$return['imgurl'] = $imgurl['imgurl'];

			$return['err'] = 0;
		}

		return $return;
	}


	//保存邮件
	public function saveMail()
	{
		//接收post来的数
		$put=file_get_contents('php://input');  
		//json->数组 数据类型转换
		$post =json_decode($put,1);
		
		// $post['userid'] = 1;
		// $post['mailid'] = 1;
		// $post['theme'] = 1;
		// $post['sendList'] = 1;
		// $post['data'] = 1;
		

		//提取post键名
		$postKey = array_keys($post);

		//定义参数名称为数组
		$key = ['userid','mailid','theme','sendList','data'];
		//提取未传入参数
		$diff = array_diff($postKey ,$key);
		//提取userid
		$id = $post['userid'];
		//干掉post里面的参数
		unset($post['userid']);
		//定义生成时间
		$post['inserttime']=time();
		//定义状态吗
		$post['state'] = 2;

		//判断是否有传入userid
		if(empty($diff)){
			//执行添加语句
			$DB = Db::name('audit')->where('mailid',$id)->update($post);
			$select = Db::name('audit')->where('mailid',$id)->where('state','2')->select();
			$return['err'] = 0;			
			$return['Unsentlist'] = $select;			
			return $return;
		}else{
			$return['err'] = 1;
			return $return;
		}

	}


	//发送邮件
	public function sendOutMail()
	{
		//接收post来的数
		$put=file_get_contents('php://input');  
		//json->数组 数据类型转换
		$post =json_decode($put,1);

		if(empty($post)){
			return $return['err'] = 1;
		}
		//提取post键名
		$postKey = array_keys($post);
		//定义参数名称为数组
		$key = ['userid','mailid','theme','sendList','data'];
		//提取未传入参数
		$diff = array_diff($postKey ,$key);

		//提取userid
		$id = $post['mailid'];
		//干掉post里面的参数
		unset($post['mailid']);
		
		

		//判断是否有未传入参数
		if(empty($diff)){

			//提取收件人
			$addr = $post['sendList'];
			//定义生成时间
			$post['endtime']=time();
			//失败人员
			$addrNo = '';
			//循环执行发送语句
			for($i = 0;$i<count($addr);$i++){
				//执行发送语句
				$sendOut = sendEmail([['user_email'=>$addr[$i],'theme'=>$post['theme'],'content'=>$post['data']]]);
				if($sendOut != 0){
					$addrNo .= $addr[$i] . ',';
				
				}
			}

		
			if(!empty($addrNo)){
				$return['addrNo'] = $addrNo;
				$post['sendList'] = $addrNo;
				//定义状态吗
				$post['state'] = 2;
				//执行修改语句
				$DB = Db::name('audit')->where('mailid',$id)->update($post);
				//定义返回数据
				$return['err'] = 2;
				$return['addrNo'] = $post['sendList'];

				return $return;
			
			}else{
				$addr = '';

				for($a = 0;$a < count($post['sendList']);$a++){

					$addr .= $post['sendList'][$a] . ',';

				}
				$post['sendList'] = $addr;
				//定义状态吗
				$post['state'] = 0;
				//执行修改语句
				//

				$DB = Db::name('audit')->where('mailid',$id)->update($post);
				$return['err'] = 0;
				$return['addrNo'] = NULL;
				return $return;

			}

		}
	

	}

	//查询邮件
	public function selectMail()
	{
		//接收post来的数
		$put=file_get_contents('php://input');  
		//json->数组 数据类型转换
		$post =json_decode($put,1);
		
		// $post['userid'] = 1;
		// $post['mailid'] = 1;
		if(empty($post)){
			return $return['err'] = 1;
		}
		
		var_dump($post);

		exit;
		//提取post键名
		$postKey = array_keys($post);

		//定义参数名称为数组
		$key = ['userid','mailid'];
		//提取未传入参数
		$diff = array_diff($postKey ,$key);


		if(empty($diff)){
			$data = Db::name('audit')->where('mailid',$post['mailid'])->find();
			$return['err'] = 0;
			$return['data'] = $data;
		}else{
			$return['err'] = 1;
		}

		return $return;
	}	

	//查询邮件列表
	public function selectMailList()
	{
		//接收post来的数
		$put=file_get_contents('php://input');  
		//json->数组 数据类型转换
		$post =json_decode($put,1);
		
		if(empty($post)){
			return $return['err'] = 1;
		}
		
		
		
		//提取post键名
		$postKey = array_keys($post);

		//定义参数名称为数组
		$key = ['userid','state'];
		//提取未传入参数
		$diff = array_diff($postKey ,$key);


		if(empty($diff)){
			$data = Db::name('audit')->where('userid',$post['userid'])->field('mailid,endtime,sendList,theme')->where('state',$post['state'])->select();
			$return['err'] = 0;
			$return['sendList'] = $data;
		}else{
			$return['err'] = 1;
		}

		return $return;
	}	


	

	//保存收件人信息
	public function insertAddr()
	{
		//接收post来的数
		$put=file_get_contents('php://input');  
		//json->数组 数据类型转换
		$post =json_decode($put,1);
		

		if(empty($post)){
			return $return['err'] = 1;
		}
		
		//提取post键名
		$postKey = array_keys($post);

		//定义参数名称为数组
		$key = ['userid','addresss','groupname'];
		//提取未传入参数
		$diff = array_diff($postKey ,$key);


		if(empty($diff)){
			$DB = Db::name('address')->insert($post);
			$return['err'] = 0;

		}else{
			$return['err'] = 1;
		}


		return $return;
	}

	//查询收件人分组名称
	public function selectAddrGroup()
	{
		//接收post来的数
		$put=file_get_contents('php://input');  
		//json->数组 数据类型转换
		$post =json_decode($put,1);
		
		if(empty($post)){
			return $return['err'] = 1;
		}
		
		
		//提取post键名
		$postKey = array_keys($post);

		//定义参数名称为数组
		$key = ['userid'];
		//提取未传入参数
		$diff = array_diff($postKey ,$key);


		if(empty($diff)){

			$DB = Db::name('address')->where('userid',$post['userid'])->field('groupname')->group('groupname')->select();
			$return['err'] = 0;
			$return['grouplist'] = $DB;
		}else{
			$return['err'] = 1;
		}

		return $return;

	}


	//查询收件人列表
	public function selecraAddr()
	{
		//接收post来的数
		$put=file_get_contents('php://input');  
		//json->数组 数据类型转换
		$post =json_decode($put,1);
		
		if(empty($post)){
			return $return['err'] = 1;
		}
		
		
		
		//提取post键名
		$postKey = array_keys($post);

		//定义参数名称为数组
		$key = ['userid','groupname'];
		//提取未传入参数
		$diff = array_diff($postKey ,$key);


		if(empty($diff)){

			$DB = Db::name('address')->where('groupname',$post['groupname'])->field('addresss')->select();
			$return['err'] = 0;
			$return['addrlist'] = $DB;
		}else{
			$return['err'] = 1;
		}

		return $return;

	}







	public function mail()
	{

		$a = sendEmail([['user_email'=>'wjp@kwcnet.com','theme'=>'nihao','content'=>'bnjkcdsbckjsdbkjsdb']]);

	}

	
}