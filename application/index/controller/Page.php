<?php
namespace app\index\Controller;
use think\Db;
use think\Request;
use think\Session;

/**
 * @param      任意变量
 *
 * @return     变量结构 数据类型
 */

class Page
{

	public function pageDir()
	{
		//接收post来的数据
		$put=file_get_contents('php://input'); 
		//json->数组 数据类型转换
		$post =json_decode($put,1); 

		if(!isset($post['name'])){
			return get_status(1,NULL,NULL);
		}
		
		$name['name'] = $post['name'];

		$insert = Db::name('pagedir')->insert($name);

		if(empty($insert)){

			$data = Db::name('pagedir')->select();
			for($i = 0;$i < count($data);$i++){
				$data[$i]['count'] = Db::name('page')->where('did',$data[$i]['id'])->count();
			}
			return get_status(1,NULL,$data);
		}else{
			$data = Db::name('pagedir')->select();
			for($i = 0;$i < count($data);$i++){
				$data[$i]['count'] = Db::name('page')->where('did',$data[$i]['id'])->count();
			}
			return get_status(0,NULL,$data);

		}

	}


	/**
	 * 获取页面组列表
	 *
	 * @return     返回err: 0 ;data:屏幕列表
	 */
	public function pageDirSummary()
	{
		
		//执行查询语句
		$data = Db::name('pagedir')->select();
		for($i = 0;$i < count($data);$i++){
			$data[$i]['count'] = Db::name('page')->where('did',$data[$i]['id'])->count();
			$data[$i]['PageSummary'] = Db::name('page')->where('did',$data[$i]['id'])->field('did,id,imgdata,name')->select();

		}
		return get_status(0,NULL,$data);
	}

	/**
	 * 获取指定页面组文件列表
	 *
	 * @return     返回err: 0 ;data:屏幕列表
	 */
	public function PageSummary()
	{
		//接收post来的数
		$put=file_get_contents('php://input'); 
		//json->数组 数据类型转换
		$post =json_decode($put,1); 

		if(!isset($post['id'])){
			return get_status(1,null,NULL);
		}

		$data = Db::name('page')->where('did',$post['id'])->field('id,name,imgdata')->select();
		return get_status(0,NULL,$data);
	}

	/**
	 * 删除页面组
	 *
	 * @return     删除成功返回{err:0;data:大屏列表} 删除失败返回{err:1;data:大屏列表}
	 */
	public function deletePageDir()
	{
		
		
		//接收post来的数据
		$put = file_get_contents('php://input');
		//json数据->php数组转换
		$id = json_decode($put,1);
		//执行删除语句
		$data = Db::name('pagedir')->where('id','=',$id['id'])->delete();
		//判断是否删除成功 
		if($data == 0){
			$data = Db::name('pagedir')->select();
			for($i = 0;$i < count($data);$i++){
				$data[$i]['count'] = Db::name('page')->where('did',$data[$i]['id'])->count();
				$data[$i]['PageSummary'] = Db::name('page')->where('did',$data[$i]['id'])->field('did,id,imgdata,name')->select();

			}
			return get_status(1,NULL,$data);
		}else{
			$data = Db::name('pagedir')->select();
			for($i = 0;$i < count($data);$i++){
				$data[$i]['count'] = Db::name('page')->where('did',$data[$i]['id'])->count();
				$data[$i]['PageSummary'] = Db::name('page')->where('did',$data[$i]['id'])->field('did,id,imgdata,name')->select();

			}
			return get_status(0,NULL,$data);
		}
		
	}

	/**
	 * 复制页面组
	 *
	 * @return     删除成功返回{err:0;data:大屏列表} 删除失败返回{err:1;data:大屏列表}
	 */
	public function copyPageDir()
	{
		//接收post来的数
		$put=file_get_contents('php://input'); 
		//json->数组 数据类型转换
		$post =json_decode($put,1); 

		if(!isset($post['id'])){
			return get_status(1,null,NULL);
		}

		$id = $post['id'];

		//执行查询语句
		$data = Db::name('pagedir')->where('id',$id)->find();

		unset($data['id']);
		$data['name'] .= '-' . date('Ymdhis') . '复制';

		$insertDirId =  Db::name('pagedir')->insertGetId($data);

		$pagedata = Db::name('page')->where('did',$id)->select();
		
		
		for($i= 0; $i< count($pagedata);$i++){
			unset($pagedata[$i]['id']);
			$pagedata[$i]['did'] = $insertDirId;

			$insertPage = Db::name('page')->insert($pagedata[$i]);
		}

		//$select =  Db::name('pagedir')->select();
		$data = Db::name('pagedir')->select();
		for($i = 0;$i < count($data);$i++){
			$data[$i]['count'] = Db::name('page')->where('did',$data[$i]['id'])->count();
			$data[$i]['PageSummary'] = Db::name('page')->where('did',$data[$i]['id'])->field('did,id,imgdata,name')->select();

		}

	
		return  get_status(0,null,$data);
		
	}

	/**
	 * 更改页面组名称
	 *
	 * @return     成功返回err:0  失败返回err:1
	 * 
	 */
	public  function updatePageDirName()
	{
		
		//接收post来的数据
		$put=file_get_contents('php://input'); 
		//json->数组 数据类型转换
		$post =json_decode($put,1); 
		//判断是否接收到数据
		if(empty($post['id']) && empty($post['name'])){
			return get_status(3,NULL,NULL);
		}
		//查询语句
		$name['name'] = $post['name'];
		$data = Db::name('pagedir')->where('id','=',$post['id'])->update($name);
		//判断是否查询成功
		if($data == null){
			return get_status(1,NULL,NULL);
		}else{
			return get_status(0,NULL,NULL);
		}

	}

	












	/**
	 * 建立页面接口
	 *
	 * @return     成功返回{err:0;id:$id} 失败返回{err:1;id:$id} 未接收到数据{err:3;id:$id} 名称重复{err:4;id:$id} 
	 */
	public function pageInfo()
	{
		//接收post来的数
		$put=file_get_contents('php://input'); 
		//json->数组 数据类型转换
		$data =json_decode($put,1); 

       	//执行查询语句
       	if(empty($data['name'])){
       		return get_status(3,3,NULL);
       	}
        $selectData = Db::name('page')->where('name',$data['name'])->where('did',$data['did'])->field('id')->find();
        //判断是否查询成功 
        if(!empty($selectData)){
			return get_status(4,4,NULL);
		}else{
			//执行添加语句
			$insert = Db::name('page')->insert($data);
			//执行查询语句
	       	$selectData = Db::name('page')->where('name',$data['name'])->field('id')->find();
	       	$data = Db::name('page')->where('did',$data['did'])->select();

	       	$return['id'] = $selectData['id'];
	       	$return['data'] = $data;
	       	if(!$insert){
				return get_status(1,1,$return);
			} 
			return get_status(0,0,$return);
		}   	 
	} 

	/**
	 * 复制页面数据
	 *
	 * @return     返回err: 0 ;data:屏幕列表
	 */
	public function copyPage()
	{
		
		//接收post来的数
		$put=file_get_contents('php://input'); 
		//json->数组 数据类型转换
		$post =json_decode($put,1); 

		if(!isset($post)){
			return get_status(1,null,NULL);
		}

		$id = $post['id'];

		//执行查询语句
		$data = Db::name('page')->where('id',$id)->find();
		unset($data['id']);

		$insert =  Db::name('page')->insert($data);


		$select =  Db::name('page')->where('did',$data['did'])->select();


		$return = get_status(0,null,$select);
    	return $return;
	}
	/**
	 * 获取页面数据
	 *
	 * @return     成功返回{err:0;data:大屏数据} 失败返回{err:1;data:null}
	 */
	public function getPageInfo()
	{
		//接收post来的数
		$put=file_get_contents('php://input'); 
		//json->数组 数据类型转换
		$id =json_decode($put,1); 
		//质询查询语句
		$data = Db::name('page')->where('id','=',$id['id'])->field('data')->find();
		//判断查询结果是否为空
		if($data == null){
			return get_status(1,NULL,$data['data']);
		}else{
			return get_status(0,NULL,$data['data']);
		}
	}



	/**
	 * 更改页面封面
	 *
	 * @return    成功返回err:0  失败返回err:1 \
	 */
	public function updatePageCover()
	{
		//接收post来的数据
		$post = input('post.');
		//执行上传
		$file = request()->file('imgdata');
		//定义上传路径
		$info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
		//获取路径
		$imgSrcWin = '\uploads\\'.$info->getSaveName();
		//设置存储路径
		$imgdata['imgdata'] = $imgSrcWin;
		//设置更改语句
		$data = Db::name('page')->where('id',$post['id'])->update($imgdata);
		//判断是否更改成功
		if($data == null){
			return get_status(1,NULL,NULL);
		}else{
			return get_status(0,NULL,NULL);
		}
	}

	/**
	 * 更改页面数据
	 *
	 * @return    成功返回err:0  失败返回err:1 未做更改返回err:2
	 */
	public function updatePageInfo()
	{
		//接收post来的数
		$put=file_get_contents('php://input'); 

		if(empty($put)){
			return get_status(1,NULL,NULL);
		}
		//json->数组 数据类型转换
		$id =json_decode($put,1); 
		//取出post来的字段
		$field = array_keys($id);
		//执行查询语句
		$selectScreen = Db::name('page')->where('id',$id['id'])->field($field)->find();
		//比较数据库数据与post来的数据是否相同
		$result = array_diff($id,$selectScreen);
		//判断 两方数据相同为空  返回2 不同执行else 添加
		if($result == null){
				return get_status(2,NULL,NULL);
		}else{
			//设置存储路径
			//$imgdata['imgdata'] = $imgSrcWin;
			//提取post来的数据
			$imgdata['data'] = $id['data'];
			//设置更改语句
			$data = Db::name('page')->where('id',$id['id'])->update($imgdata);
			//判断是否更改成功
			if($data == null){
				return get_status(1,NULL,NULL);
			}else{
				return get_status(0,NULL,NULL);
			}
		}
		//设置返回数组格式
		$returndata['err'] = $ifdata;
		//返回数据
		return  $returndata;
	}

	/**
	 * 删除页面数据
	 *
	 * @return     删除成功返回{err:0;data:大屏列表} 删除失败返回{err:1;data:大屏列表}
	 */
	public function deletePage()
	{
		
		
		//接收post来的数据
		$put = file_get_contents('php://input');
		//json数据->php数组转换
		$id = json_decode($put,1);
		$did = Db::name('page')->where('id','=',$id['id'])->field('did')->find();
		//执行删除语句
		$data = Db::name('page')->where('id','=',$id['id'])->delete();
		//执行查询语句
		$screenData = Db::name('page')->where('did',$did['did'])->field('id,name,imgdata')->select();
		//判断是否删除成功 
		if($data == 0){
			return get_status(1,NULL,$screenData);
		}else{
			return get_status(0,NULL,$screenData);
		}
	}


	/**
	 * 更改页面名称
	 *
	 * @return     成功返回err:0  失败返回err:1
	 * 
	 */
	public  function updatePageName()
	{
		
		//接收post来的数据
		$put=file_get_contents('php://input'); 
		//json->数组 数据类型转换
		$post =json_decode($put,1); 
		//判断是否接收到数据
		if(empty($post['id']) && empty($post['name'])){
			return get_status(3,NULL,NULL);
		}
		//查询语句
		$name['name'] = $post['name'];
		$data = Db::name('page')->where('id','=',$post['id'])->update($name);
		//判断是否查询成功
		if($data == null){
			return get_status(1,NULL,NULL);
		}else{
			return get_status(0,NULL,NULL);
		}

	}



	
}
