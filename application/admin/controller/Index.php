<?php
namespace app\admin\Controller;
use think\Db;
use think\Request;
use think\Session;

class Index
{

	public  function index()
	{

			$src = './data/messages.1';
			$myfile = fopen($src , "rb");
			$str = fread($myfile,filesize($src));//指定读取大小，这里把整个文件内容读取出来
			$str = mb_convert_encoding($str, "UTF-8", "GBK"); 
			$strstr = str_replace("\r","<br />",$str);

			$textArr = explode("<br />",$strstr);//"<br />"作为分隔切成数组
			
	}
	//核心组件
	public function syslog()
	{
		$dir="./syslog/";
		$file=scandir($dir);
		for($d = 0;$d < count($file);$d++){
			$src = $dir . $file[$d];
			$myfile = fopen($src , "rb");
			$str = fread($myfile,filesize($src));//指定读取大小，这里把整个文件内容读取出来
			$str = mb_convert_encoding($str, "UTF-8", "GBK"); 
			$strstr = str_replace("\r","<br />",$str);

			$textArr = explode("<br />",$strstr);//"<br />"作为分隔切成数组
			                                     //
			
			//创建临时数据表`log_interim`
			$createtTABLE = Db::query("CREATE TABLE IF NOT EXISTS `log_interim`(
								   `id` INT UNSIGNED AUTO_INCREMENT,
								   `data` TEXT NOT NULL,
								   PRIMARY KEY ( `id` )
								)ENGINE=InnoDB DEFAULT CHARSET=utf8;"); 
			//打印数据库sdata中的所有表
			$tables = Db::query('show tables');
	    	//给他变成一维数组
	    	for($i = 0;$i<count($tables);$i++){
	    		$arrayTable = array_values($tables[$i]);
	    		for($j = 0;$j<count($tables[$i]);$j++){
	    			if($arrayTable[$j] == 'log_interim' ){
		    			$table['name'] = $arrayTable[$j];
	    			}
	    		}
	    	}
	    	//判断创建数据表log_interim是否成功
	    	if(!isset($table)){
	    		return '创建临时数据表失败';
	    	}


	    	Db::startTrans(); 
			try{ 

				for($r = 0;$r < count($textArr);$r++){
					if(strlen($textArr[$r]) <= 3){
						unset($textArr[$r]);
						continue;
					}
					Db::table('log_interim')->insert(['data' => $textArr[$r],'src'=>$src]); 
				}

				
				// 提交事务 
				Db::commit();
				unset($str);
				unset($textArr);
				echo '文件：' . $file[$d] . '导入临时数据表成功<br />'; 
				} catch (\Exception $e) {
				// 回滚事务 
				Db::rollback(); 
				echo '文件：' . $file[$d] . '导入临时数据表失败<br />';
			}

		}
	}

	public  function getSysLogs()
	{
		
		$maxId = Db::table('log_interim')->max('id');
		 Db::startTrans(); 
		for($i =0;$i<$maxId;$i++){

			$data = Db::table('log_interim')->field('data')->where('id',$i)->find();
			if(empty($data)){
				unset($data);unset($key);
				continue;
			}
			if(strpos($data['data'],'localhost') || strpos($data['data'],'superman')){
				unset($data);unset($key);
				continue;
			}
			if(!strpos($data['data'],'type=ips')){
				unset($data);unset($key);
				continue;
			}
			preg_replace("/[\s]+/is"," ",$data['data']);
			$insert['hostip'] = get_logip($data['data'],NULL);
			if(empty($insert['hostip'])){
				unset($data);unset($key);
				continue;
			}
			$insert['time'] = strtotime(get_logdata($data['data'],"time=\"",'"'));
			$insert['fw'] = get_logdata($data['data'],"fw=",' ');
			$insert['pri'] = get_logdata($data['data'],"pri=",' ');
			$insert['type'] = get_logdata($data['data'],"type=",' ');
			$insert['recorder'] = get_logdata($data['data'],"recorder=",' ');
			$insert['proto'] = get_logdata($data['data'],"proto=",' ');
			$insert['src'] = get_logdata($data['data'],"src=",' ');
			$insert['sport'] = get_logdata($data['data'],"sport=",' ');
			$insert['dst'] = get_logdata($data['data'],"dst=",' ');
			$insert['dport'] = get_logdata($data['data'],"dport=",' ');
			$insert['repeats'] = get_logdata($data['data'],"repeat=",' ');
			$insert['msg'] = get_logdata($data['data'],"msg=\"",'"');
			$insert['op'] = get_logdata($data['data'],"op=\"",'"');
			$insert['sdev'] = get_logdata($data['data'],"sdev=",' ');
			$return  = Db::table('sdata_ips')->insert($insert);
			if($i%5000==0){
			   Db::commit();
			   Db::startTrans(); 
			}

			unset($data);unset($key);
			unset($insert);
			unset($return);

		}
		Db::commit();
		
		
	}

	public  function ipToAddr()
	{

		$ipArray = Db::query("SELECT `ip` FROM `sdata_d_apache_log` GROUP BY `sdata_d_apache_log`.`ip` ;");
		//$ipArray = Db::query("SELECT `dst` FROM `sdata_ips`");
		
		import('checkIp/ConvertIpNew', EXTEND_PATH);
    	$setip=new \checkIp\ConvertIpNew\ipAddress("./qqwry.dat");

		for($i = 0;$i <count($ipArray);$i++){
			 if(!preg_match("/^[\d]+\.[\d]+\.[\d]+\.[\d]+$/isU",$ipArray[$i]['ip'])){
            	 echo  $i  . '------' . $ipArray[$i]['ip'] . '----IP地址错误<br />';
    		 }else{
    		 	$location=$setip->getlocation($ipArray[$i]['ip']);
			    $addr=$location['area'];
			    $addr=iconv("gb2312", "utf-8//IGNORE",$addr); //这边纯真IP数据库获取到的gb2312格式的文字,要先转成UTF8
			    //preg_match_all('/(.*?)省(.*?)市/',$str,$userLocation);
			    echo $i  . '------' . $ipArray[$i]['ip']  . '----'.  $addr . '<br />' ;
			   

    		 }
		}
           
    
     }

     public function getWebLog()
     {

     	$dir="./webLog/";
		$file=scandir($dir);
		$strstr = '';
		for($d = 0;$d < count($file);$d++){

			$src = $dir . $file[$d];
			$myfile = fopen($src , "rb");
			$str = fread($myfile,filesize($src));//指定读取大小，这里把整个文件内容读取出来
			$str = mb_convert_encoding($str, "UTF-8", "GBK"); 
			$strstr .= str_replace("\r","<br />",$str);

			
		}
		$textArr = explode("<br />",$strstr);//"<br />"作为分隔切成数组
		echo '<pre>';
		
		var_dump($textArr);

		echo '</pre>';

     }

     public function getApacheLog()
     {
		//set_time_limit(0); 
		//error_reporting(E_ALL); 
		//ini_set('display_errors', 'on'); 
		$dir="./weblog/";
		$file=scandir($dir);
		import('checkIp/ConvertIpNew', EXTEND_PATH);
		$setip=new \checkIp\ConvertIpNew\ipAddress("./qqwry.dat");
		Db::startTrans(); 
		for($d = 0;$d < count($file);$d++){

			$src = $dir . $file[$d];
			$ac_arr = file($src);

			for($i = 0;$i <count($ac_arr);$i++){
				//获取IP
				$records = preg_split("/([0-9]+\.[0-9]+\.[0-9]+\.[0-9]+)/", $ac_arr[$i], -1, PREG_SPLIT_DELIM_CAPTURE); 

				if(empty($records[1]) || empty($records[2])){
					continue;
				}
				//提取IP
				$data['ip'] = $records[1]; 
				$location=$setip->getlocation($data['ip']);

				$addr=$location['area'];
				$addr=iconv("gb2312", "utf-8//IGNORE",$addr);
				if(!empty(strstr($addr,'省'))){
					$addr = strstr($addr,'省');
					$addr = mb_substr($addr,1,mb_strlen($addr,'utf-8'),'utf-8');
				}
				if(!empty(strstr($addr,'区')) && !empty(strstr($addr,'市'))){

					$hou = mb_substr($addr,mb_strpos($addr,'市')+1,mb_strlen($addr)); 
					$addr = str_replace($hou,"",$addr);
					
				}
				if(!empty(strstr($addr,'市'))){
					$addr = str_replace(array("省","市","内蒙古","宁夏","广西","新疆"),"",$addr);
				}
				if($addr == 'IANA'){
					$addr ='局域网';
				}
				if($addr == '恩施州'){
					$addr ='恩施土家族苗族自治州';
				}
				if($addr == '欧洲'){
					$addr ='英国';
				}
				if($addr == '孟加拉'){
					$addr ='孟加拉国';
				}
				if($addr == ''){
					unset($data[$i]);
					continue;
				}
				if($addr == NULL){
					unset($data[$i]);
					continue;
				}
				$data['addr'] = $addr;

				//除IP外剩下的数据
				$left_str = $records[2]; 

				// 开始提取时间
				preg_match("/\[(.+)\]/", $left_str, $match); 
				//判断是否提取成功
				$access_time = $match[1]; 
				if(empty($match[1])){
					continue;
				}

				//将提取的时间解析成时间戳
				$data['time'] = date('Y-m',strtotime($access_time));
				
				//分析剩下的日志数据 干掉$left_str里的时间
				$left_str = preg_replace("/^([- ]*)\[(.+)\]/", "", $left_str); 
				//去掉两边的空格
				$left_str = trim($left_str); 

				preg_match("/^\"[A-Z]{3,7} (.[^\"]+)\"/i", $left_str, $match); 

				if(empty($match[0]) || empty($match[1])){
					continue;
				}
				//路径及传输协议版本
				$full_path = $match[0];
				//http请求方式 ， 路径及传输协议版本
				$get = explode(" ",$full_path);
				$http = $match[1]; 
				//切分路径及传输协议版本
				$link = explode(" ", $http); 
				if(empty($link[0]) || empty($link[1])){
					continue;
				}
				//$data['src'] = $link[0];
				//$data['http'] = $link[1];

				//获取返回的状态吗数据
				$left_str = str_replace($full_path, "", $left_str); 

				//将返回的状态吗数据大三成数组
				$left_arr = explode(" ", trim($left_str)); 

				//获取状态吗
				preg_match("/([0-9]{3})/", $left_arr[0], $match);

				if(empty($match[1])){
					continue;
				}
				//$data['code'] = $match[1]; 

				$insert = Db::table('sdata_d_apache_log')->insert($data);
				if($i%20000==0){
				   Db::commit();
				   Db::startTrans(); 
				}				
			}
			
		}
		Db::commit();
		

     }
      public function getApacheAttackLog()
     {
		//set_time_limit(0); 
		//error_reporting(E_ALL); 
		//ini_set('display_errors', 'on'); 
		$dir="./apache_attacklog/";
		$file=scandir($dir);

		Db::startTrans(); 
		for($d = 0;$d < count($file);$d++){

			$src = $dir . $file[$d];
			$ac_arr = file($src);

			for($i = 0;$i <count($ac_arr);$i++){
				//获取IP
				$records = preg_split("/([0-9]+\.[0-9]+\.[0-9]+\.[0-9]+)/", $ac_arr[$i], -1, PREG_SPLIT_DELIM_CAPTURE); 

				if(empty($records[1]) || empty($records[2])){
					continue;
				}
				//提取IP
				$data['ip'] = $records[1]; 
				//除IP外剩下的数据
				$left_str = $records[2]; 

				// 开始提取时间
				preg_match("/\[(.+)\]/", $left_str, $match); 
				//判断是否提取成功
				$access_time = $match[1]; 
				if(empty($match[1])){
					continue;
				}

				//将提取的时间解析成时间戳
				$data['time'] = strtotime($access_time);
				
				//分析剩下的日志数据 干掉$left_str里的时间
				$left_str = preg_replace("/^([- ]*)\[(.+)\]/", "", $left_str); 
				//去掉两边的空格
				$left_str = trim($left_str); 

				preg_match("/^\"[A-Z]{3,7} (.[^\"]+)\"/i", $left_str, $match); 

				if(empty($match[0]) || empty($match[1])){
					continue;
				}
				//路径及传输协议版本
				$full_path = $match[0];
				//http请求方式 ， 路径及传输协议版本
				$get = explode(" ",$full_path);
				$http = $match[1]; 
				//切分路径及传输协议版本
				$link = explode(" ", $http); 
				if(empty($link[0]) || empty($link[1])){
					continue;
				}
				$data['src'] = $link[0];
				$data['http'] = $link[1];

				//获取返回的状态吗数据
				$left_str = str_replace($full_path, "", $left_str); 

				//将返回的状态吗数据大三成数组
				$left_arr = explode(" ", trim($left_str)); 

				//获取状态吗
				preg_match("/([0-9]{3})/", $left_arr[0], $match);

				if(empty($match[1])){
					continue;
				}
				$data['code'] = $match[1]; 

				$insert = Db::table('sdata_d_apache_attacklog')->insert($data);
				if($i%20000==0){
				   Db::commit();
				   Db::startTrans(); 
				}				
			}
			
		}
		Db::commit();
		

     }


     public function rrdLog()
     {

     	$post = file_get_contents("http://10.225.60.12:81/month.php");
     	$post = json_decode($post,true);


     	$array = Db::table('sdata_d_rrd_name')->field('src')->select();
     	for($a = 0;$a <count($array);$a++){
     		$srcname[$a] = $array[$a]['src'];
     	}


     	for($j = 0;$j<count($post);$j++)
     	{
	     	//取出数组
	     	$array = $post[$j];
	     	//取出表名
	     	$name = $array['name'];
	     	if(!in_array($name,$srcname)){
	     		continue;
	     	}

	     	//取出字段名
	     	$field = $field['time'] = 'time' .  $array[0];
	     	$field = explode(" ",$field);//"宫格"作为分隔切成数组
	     	$field = array_filter($field);//除空值
	     	if(in_array('values',$field)){
	     		$field[array_search("values",$field)] = 'valuess'; 
	     	}
	     	$field = array_values($field);//重新分配数组的键


	     	//干掉非数据的键
	     	unset($array[0]);
	     	unset($array['name']);
	     	$array = array_filter($array);//除空值
	     	$array = array_values($array);//重新分配数组的键

	     	                              
	     	//判断字段总数是否小于3个 小于的话存入sdaya_d_rrd_three表
	     	if(count($field) < 3){
	     		//循环 根据数据的总数
	     		for($i = 0;$i <count($array);$i++){
	     			//将单条数据切割成数组
			     	$arr_str = explode(" ",$array[$i]);//"宫格"作为分隔切成数组
			     	//重新分配数组的键
			     	$arr_str = array_values($arr_str);//重新分配数组的键 
			     	//获取数据类型           
			     	$type = $field[1];
			     	//封装新的字段变量
			     	$newField[0] = 'time';
			     	$newField[1] = 'data';
			     	//判断新字段总数与但条数据的键总数是否相同
			     	if(count($arr_str) == count($newField)){
			     		//封装新数据
			     		$key[$newField[0]] = substr($arr_str[0],0,strlen($arr_str[0])-1);
			     		for($k = 1;$k <count($newField);$k++){
					     	$key[$newField[$k]] = $arr_str[$k];
			     		}
			     		$key['src'] = $name;
		     			$key['type'] = $type;
		     		
					}else{
						continue;
					}
					//封装成$data用以执行添加语句
					$data[$i] = $key;
				}

				// Db::startTrans(); 
				$insert = Db::table('sdata_d_rrd_three_month')->insertall($data);
				// Db::commit();
				unset($data);unset($key);
				unset($key);
			}elseif(count($field) < 4 && in_array('traffic_in',$field))
			{
					for($i = 0;$i <count($array);$i++){
				     	$arr_str = explode(" ",$array[$i]);//"宫格"作为分隔切成数组
				     	$arr_str = array_values($arr_str);//重新分配数组的键               

				     	if(count($arr_str) == count($field)){
				     		$key[$field[0]] = substr($arr_str[0],0,strlen($arr_str[0])-1);
				     		for($k = 1;$k <count($field);$k++){
						     	$key[$field[$k]] = $arr_str[$k];
				     		}
				     		$key['src'] = $name;
						    
						}else{
							continue;
						}	
						$data[$i] = $key;
					}
					Db::startTrans(); 
					$insert = Db::table('sdata_d_rrd_month')->insertall($data);
					Db::commit();
					unset($data);unset($key);
			}else{
				continue;
			}

				
		}

		


    }

    public function deleteRrd()
    {
    	Db::startTrans(); 
    		$delete = Db::table('sdata_d_rrd')->where('id','>','0')->delete();
		Db::commit();

		Db::startTrans(); 
			$delete = Db::table('sdata_d_rrd_two')->where('id','>','0')->delete();
		Db::commit();

		Db::startTrans(); 
			$delete = Db::table('sdata_d_rrd_three')->where('id','>','0')->delete();
		Db::commit();

		Db::startTrans(); 
			$delete = Db::table('sdata_d_rrd_four')->where('id','>','0')->delete();
		Db::commit();

		Db::startTrans(); 
			$delete = Db::table('sdata_d_rrd_five')->where('id','>','0')->delete();
		Db::commit();

		Db::startTrans(); 
			$delete = Db::table('sdata_d_rrd_six')->where('id','>','0')->delete();
		Db::commit();

		Db::startTrans(); 
			$delete = Db::table('sdata_d_rrd_seven')->where('id','>','0')->delete();
		Db::commit();
    }

    

}
