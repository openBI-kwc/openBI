<?php
namespace app\index\Controller;
use think\Db;
use think\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;


class viewdata
{
	//查询指定用户上传过的文件
	public function excelFile()
	{
		//接收post来的数据
		$dataPost = file_get_contents('php://input'); 
		//POST来的数据转换成json格式
		$post =json_decode($dataPost,1); 
		//提取用户名
		$username = $post['username'];
		//判断用户名是否为NULL
		if(empty($username)){
			return get_status(3,NULL,NULL);
		}
		//根据用户名查询表结构
		$data = Db::name('excelfile')->where('username',$username)->select();
		//返回数据
		return get_status(0,NULL,$data);
	}
	//删除指定用户上传的文件
	public function deleteExcelFile()
	{
		//接收post来的数据
		$dataPost = file_get_contents('php://input'); 
		//POST来的数据转换成json格式
		$post =json_decode($dataPost,1); 
		//提取删除目标id
		$id = $post['id'];
		//提取用户名
		$username = $post['username'];
		//判断username是否为NULL
		if(empty($username)){
			return get_status(3,NULL,NULL);
		}
		//判断ID是否为NULL
		if(empty($id)){
			return get_status(3,NULL,NULL);
		}
		//删除目标文件
		$delete = Db::name('excelfile')->where('id',$id)->delete();
		//根据用户名查询表结构
		$data = Db::name('excelfile')->where('username',$username)->select();
		//返回数据
		return get_status(0,NULL,$data);

	}
	//获取文件数据
	public function getExcelData()
	{
		//接收post来的数据
		$dataPost = file_get_contents('php://input'); 
		//POST来的数据转换成json格式
		$post =json_decode($dataPost,1); 
		//提取删除目标id
		$id = $post['id'];
		//判断ID是否为NULL
		if(empty($id)){
			return get_status(3,NULL,NULL);
		}
		//根据用户名查询表结构
		$data = Db::name('excelfile')->where('id',$id)->find();
		//在本机的路径
		$fileSrcWin = ROOT_PATH . 'public' . '\updirs\\'.$data['name'];
		//斜线转换
		$fileSrc = str_replace('\\','/',$fileSrcWin);
		//使用phpexcel
        $objPHPExcel = IOFactory::load($fileSrc);
        //生成返回数据
        $arrExcel = $objPHPExcel->getSheet(0)->toArray();
        //返回数据
        return get_status(0,NULL,$arrExcel);
	}

	//excel文件保存
	public function  getUploadExcel()
	{
		//接收post来的数据
		$dataPost = file_get_contents('php://input'); 
		//POST来的数据转换成json格式
		$post =json_decode($dataPost,1); 
		if(empty($post)){
			return get_status(3,NULL,NULL);
		}
		//获取文件名称
		$nameAndType = $post['name'];
		//干掉文件后缀名 
		$type = '.' . get_types($nameAndType);
		$name = str_replace($type,'',$nameAndType) . '.csv';
		//获取用户名
		$username = $post['username'];
		//获取数据
		$data = $post['data'];

		$src = ROOT_PATH . 'public' . '/updirs/' . $name;
		$put = '';
		for($i = 0;$i < count($data);$i++){
			for($j = 0;$j < count($data[$i]);$j++){
				//print_r($data[$i][$j] . ';');
				$put .= $data[$i][$j] . ',';
			}
			$put .= '
';

		}

		
		file_put_contents($src,$put);

		//定义存储路径
		$insertSrc =  $_SERVER['REMOTE_ADDR'] . '/updirs/' . $name;
		//删除语句
		$delete = Db::name('excelfile')->where('username',$username)->where('name',$nameAndType)->delete();
		//添加文件
		$insert = Db::name('excelfile')->insert(['src' => $insertSrc,'name' => $name,'username' => $username]);
		//查询语句
		$select = Db::name('excelfile')->where('username',$username)->select();

		return get_status(0,0,$select);

	}

	//获取数据库表名
	public function tables()
     {
     	//执行sql语句
    	$tables = Db::query('show tables');
    	//给他变成一维数组
    	for($i = 0;$i<count($tables);$i++){
    		$arrayTable = array_values($tables[$i]);
    		for($j = 0;$j<count($tables[$i]);$j++){
    			if(strpos($arrayTable[$j],'sdata_d_') !== false ){
	    			$table[$i]['name'] = $arrayTable[$j];
	    			$table[$i]['number'] = Db::table($arrayTable[$j])->count();
    			}
    		}
    	}
    	return get_status(0,NULL,$table);
     }
     //获取指定表数据
     public function selectTables(){
     	//接收post来的数据
		$dataPost = file_get_contents('php://input'); 
		//POST来的数据转换成json格式
		$post =json_decode($dataPost,1);
		//获取表名
		if(empty($post['table'])){
       		return get_status(3,3,NULL);
       	}else{
			$table = $post['table'];
       	}
		//获取查询数量
		if(empty($post['number'])){
       		$number = 200;
       	}else{
       		$number = $post['number'];
       	}
       	//获取页面数量
		if(empty($post['page'])){
       		$page = 0;
       	}else{
       		$page = $post['page'];
       	}
       	$data = Db::table($table)->where('id','>','0')->page($page,$number)->select();
		return get_status(0,0,$data);
		
		
     }
     //数据表排序
     public function orderTables()
     {
     	//接收post来的数据
		$dataPost = file_get_contents('php://input'); 
		//POST来的数据转换成json格式
		$post =json_decode($dataPost,1);


		//判断是否接收到post来的数据 没收到返回错误码3
		if(empty($post['order']) && empty($post['field'])){
       		return get_status(3,3,NULL);
       	}
       	if(empty($post['table'])){
       		return get_status(3,3,NULL);
       	}
  		
  		//获取表名
		$table = $post['table'];
		//获取排序信息
		$order = $post['order'];
		//获取目标字段
		$field = $post['field'];

		//获取查询数量
		if(empty($post['number'])){
       		$number = 200;
       	}else{
       		$number = $post['number'];
       	}
       	//获取页面数量
		if(empty($post['page'])){
       		$page = 0;
       	}else{
       		$page = $post['page'];
       	}
		$return['data'] = Db::table($table)->where('id','>','0')->order("$field $order")->page($page,$number)->select();
		$return['count'] = Db::table($table)->where('id','>','0')->order("$field $order")->count();
		return get_status(0,0,$return);
     }
     //数据库指定表字段分组
     public function groupTables()
     {
     	//接收post来的数据
		$dataPost = file_get_contents('php://input'); 
		//POST来的数据转换成json格式
		$post =json_decode($dataPost,1);
		//判断是否接收到post来的数据 没收到返回错误码3
		if(empty($post['table']) && empty($post['group'])){
       		return get_status(3,3,NULL);
       	}
       	//获取需要分组的字段 
		$group = $post['group'];
		//获取表名
		$table = $post['table'];
		//执行查询语句 
		$returnData = Db::query("SELECT $group,COUNT($group) FROM $table GROUP BY $table .$group");
		//执行添加语句
		for($i = 0;$i < count($returnData);$i++){
       		$insertData['data'] = $returnData[$i][$group];
       		$insertData['count'] = $returnData[$i]["COUNT($group)"];
       		$insertData['class'] = $table;
      		$insert = Db::table('sdata_d_group')->insert($insertData);
		}
		//返回json数据
		return get_status(0,0,$returnData);

     }
     //查询表单行数据
     public function likeTables()
     {
     	//接收post来的数据
		$dataPost = file_get_contents('php://input'); 
		//POST来的数据转换成json格式
		$post =json_decode($dataPost,1);
		// //判断是否接收到post来的数据 没收到返回错误码3
		// if(empty($post['table']) && empty($post['field'])){
  //      		return get_status(3,3,NULL);
  //      	}
		// //获取表名
		// $table = $post['table'];
		// $field = $post['field'];
		// $data = '%' . $post['data'] . '%';
		//获取页面数量
		// if(empty($post['page'])){
  //      		$page = 0;
  //      	}else{
  //      		$page = $post['page'];
  //      	}

		$table = 'sdata_d_attack';
		$field = 'AttackClass';
		$a = '注入';
		$data = '%' . $a . '%';
		$page = 20;
		$number = 100;



		//执行查询语句 
		$returnData['data'] = Db::table($table)->where($field,'like',$data)->page($page,$number)->select();
		$returnData['count'] = Db::table($table)->where($field,'like',$data)->count();

		echo '<pre>';
		var_dump($returnData);
		echo '</pre>';

		//返回json数据
		//return get_status(0,0,$returnData);

     }
     //删除表单行数据
      public function deleteSelectTable()
     {
     	//接收post来的数据
		$dataPost = file_get_contents('php://input'); 
		//POST来的数据转换成json格式
		$post =json_decode($dataPost,1);
		//判断是否接收到post来的数据 没收到返回错误码3
		if(empty($post['table']) && empty($post['field'])){
       		return get_status(3,3,NULL);
       	}
		//获取表名
		$table = $post['table'];
		$field = $post['field'];
		$data = $post['data'];
		//执行查询语句 
		$returnData = Db::query("SELECT * FROM $table WHERE $field` LIKE $data");
		//返回json数据
		return get_status(0,0,$returnData);

     }
  //    //修改表单行数据
  //     public function updateSelectTable()
  //    {
  //    	//接收post来的数据
		// $dataPost = file_get_contents('php://input'); 
		// //POST来的数据转换成json格式
		// $post =json_decode($dataPost,1);
		// //判断是否接收到post来的数据 没收到返回错误码3
		// if(empty($post['table']) && empty($post['field'])){
  //      		return get_status(3,3,NULL);
  //      	}
		// //获取表名
		// $id = $post['id']
		// $table = $post['table'];
		// $field = $post['field'];
		// $data = $post['data'];
		

		// //执行修改语句 
		// $returnData = Db::query("UPDATE $table SET $field = $data WHERE $table.`id` = $id;");
		// //返回json数据
		// return get_status(0,0,$returnData);

  //    }




}
