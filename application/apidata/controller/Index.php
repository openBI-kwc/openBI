<?php
namespace app\apidata\Controller;
use think\Db;
use think\Request;
use think\Session;

class Index
{

	public  function attackTime()
	{
		$mintime = 	Db::table('sdata_d_apache_log')->min('time');
		$maxtime = 	Db::table('sdata_d_apache_log')->max('time');
		$time['min'] = $mintime;
		$time['max'] = $maxtime;

		$data = Db::table('sdata_d_apache_log')->field('ip,time')->order('time','asc')->select();
		import('checkIp/ConvertIpNew', EXTEND_PATH);
		$setip=new \checkIp\ConvertIpNew\ipAddress("./qqwry.dat");
		$coordinateDB = Db::table('sdata_d_coordinate')->field('name,coordinate,class')->select();
		$count = count($coordinateDB);
		for($c = 0;$c < $count;$c++){
			$name = $coordinateDB[$c]['name'];
			$coordinate[$name] = $coordinateDB[$c]['coordinate'];
		}
		
		for($i = 0;$i < count($data);$i++){

			if(!preg_match("/^[\d]+\.[\d]+\.[\d]+\.[\d]+$/isU",$data[$i]['ip'])){
			  unset($data[$i]);
			}else{
				$location=$setip->getlocation($data[$i]['ip']);

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
				//{'name': '" . $addr . "', 'value': [" . $coordinate[$addr] . "]},"
             $str = "{'name': '" . $addr . "', 'value': [" . $coordinate[$addr] . "]},";

             	$data[$i]['data'] = $str;
             	$data[$i]['name'] = $addr;
             	$data[$i]['month'] = $month = date('Y-m',$data[$i]['time']);

				// $data[$i]['time'] = date('Y-m-d',$data[$i]['time']);
				// $data[$i]['coordinate'] = $coordinate[$addr];
				// $data[$i]['addr'] = $addr;
				    
				$insert = Db::table('sdata_z_attack')->insert($data[$i]);
			}
			unset($data[$i]);
		}

		return $time;
		
			
	}
	public function  getAttack()
	{
		$month = Db::table('sdata_z_attack')->group('month')->field('month')->select();
		$data = array();

		for($i = 0;$i <count($month);$i++){
			$select[$i] = Db::table('sdata_z_attack')->where('month',$month[$i]['month'])->group('name')->field('count(name),data')->select();
			$data[$i] = $month[$i]['month'] . ":[";
			for($s = 0;$s<count($select[$i]);$s++){
				$str = substr($select[$i][$s]['data'],0,strlen($select[$i][$s]['data'])-3);
				$str = $str . ',' .  $select[$i][$s]['count(name)'] . ']},';
				$data[$i] .= $str;
			}
			$data[$i] .= "],";
			
			
		}
		return $data;
		// echo '<pre>';
		// var_dump( $data);
		// echo '</pre>';
		// echo '<hr />';



	}


	public  function apacheTime()
	{
		$mintime = 	Db::table('sdata_d_apache_log')->min('time');
		$maxtime = 	Db::table('sdata_d_apache_log')->max('time');
		$time['min'] = $mintime;
		$time['max'] = $maxtime;

		$data = Db::table('sdata_d_apache_log')->group('ip')->field('ip,time')->select();
		import('checkIp/ConvertIpNew', EXTEND_PATH);
		$setip=new \checkIp\ConvertIpNew\ipAddress("./qqwry.dat");

		// echo '<pre>';
		// var_dump($data);
		// echo '</pre>';
		// echo '<hr />';

		for($i = 0;$i < count($data);$i++){

			if(!preg_match("/^[\d]+\.[\d]+\.[\d]+\.[\d]+$/isU",$data[$i]['ip'])){
			  unset($data[$i]);
			}else{
				$location=$setip->getlocation($data[$i]['ip']);

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
				if($addr == '内蒙古'){
					$addr ='呼和浩特';
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
				//echo $addr . '<br />';
				echo  $addr . '--' . $data[$i]['ip'] . '--' .'<br />' ;

				//$update = Db::table('sdata_d_apache_log')->where('ip',$data[$i]['ip'])->update(['addr' => $addr]);
				
			}
		}

		return $time;
		
			
	}

	//真实接口
	public  function  getLine()
	{
		
		$lineName = Request::instance()->get('name');

		
		$name = Db::table('sdata_d_rrd_name')->where('class','专线')->field('name,src')->select();

		for($i = 0;$i < count($name);$i++){
			$maxTime[$i] = Db::table('sdata_d_rrd')->where('src',$name[$i]['src'])->field('max(time) as time')->find();
			$time = $maxTime[$i]['time'];
			$data[$i] = Db::table('sdata_d_rrd')->where('time',$time)->where('src',$name[$i]['src'])->field('traffic_in as up,traffic_out as down')->find();
			//var_dump($maxTime[$i]);
			$str[$name[$i]['name']] = '<p>上行流量为:<b style="color:#009fe9;">' . number_format($data[$i]['up'],0,'','') . 'bps</b></p>。<br /><p>下行流量为:<b style="color:#009fe9;">' . number_format($data[$i]['down'],0,'','')  . 'bps。</b></p>';
		}

		return $str[$lineName];
	}



	public function getWork()
	{
		$workName = Request::instance()->get('name');
		
		$name = Db::table('sdata_d_rrd_name')->where('class','办公区域')->field('name,src')->select();
		$selectTime = time()-86400;

		for($i = 0;$i < count($name);$i++){
			//$data[$name[$i]['name']]
			$select[$i] = Db::table('sdata_d_rrd')->where('time','>',$selectTime)->where('src',$name[$i]['src'])->field('time,traffic_in as up,traffic_out as down')->select();
			$strTime = '';

			$strUp = '';
			$strDown = '';
			for($s = 0;$s <count($select[$i]);$s++){
				$time[$s] = date('H:i',$select[$i][$s]['time']);
				$up[$s] = number_format($select[$i][$s]['up'],0,'','');
				$down[$s] = number_format($select[$i][$s]['down'],0,'','');
				//$str = "{ 'xAxis': ["1月", "2月", "3月", "4月", "5月", "6月", "7月"],'data': [[10, 52, 200, 334, 390, 330, 220],  ]}"
				if(substr($time[$s], -2) == '00' || substr($time[$s], -2) == '30'){
					$strTime  .=  "'" . $time[$s] . "',";
				}else{
					$strTime  .=  "' '" . ',';
				}

				$strUp .= "'" . $up[$s] . "',";
				$strDown .= "'" . $down[$s] . "',";
				
			}
			$strTime = substr($strTime,0,strlen($strTime)-1);
			$strUp = substr($strUp,0,strlen($strUp)-1);
			$strDown = substr($strDown,0,strlen($strDown)-1);
			//$str = "{ 'xAxis': ["1月", "2月", "3月", "4月", "5月", "6月", "7月"],'data': [[10, 52, 200, 334, 390, 330, 220],  ]}"
			$data[$name[$i]['name']] = "{ 'xAxis': [" . $strTime . "],'data': [[" . $strUp . "], [" . $strDown . "]]}";
			
			

		}

			

		return $data[$workName];
			

	}



	public function getSystem()
	{
		$systemName = Request::instance()->get('name');
		
		$name = Db::table('sdata_d_rrd_name')->where('class','重点系统')->field('name,src')->select();


		for($i = 0;$i < count($name);$i++){
			//$data[$name[$i]['name']] 
			$select[$i] = Db::table('sdata_d_rrd_three')->where('time','>','0')->where('src',$name[$i]['src'])->field('time,data')->select();

			$strTime = '';
			$strData = '';
			for($s = 0;$s <count($select[$i]);$s++){
				$time[$s] = date('H:i',$select[$i][$s]['time']);
				$data[$s] = number_format($select[$i][$s]['data'],8,'.','');
				//$str = "{ 'xAxis': ["1月", "2月", "3月", "4月", "5月", "6月", "7月"],'data': [[10, 52, 200, 334, 390, 330, 220],  ]}"
				if(substr($time[$s], -2) == '00' || substr($time[$s], -2) == '30'){
					$strTime  .=  "'" . $time[$s] . "',";
				}else{
					$strTime  .=  "' '" . ',';
				}

				$strData .= "'" . $data[$s] . "',";
				
			}
			$strTime = substr($strTime,0,strlen($strTime)-1);
			$strData = substr($strData,0,strlen($strData)-1);
			//$str = "{ 'xAxis': ["1月", "2月", "3月", "4月", "5月", "6月", "7月"],'data': [[10, 52, 200, 334, 390, 330, 220],  ]}"
			$data[$name[$i]['name']] = "{ 'xAxis': [" . $strTime . "],'data': [[" . $strData . "]]}";
			
					
		}





		return $data[$systemName];		
			

	}

	


	public  function  getLineMonth()
	{
		$lineName = Request::instance()->get('name');
		
		$name = Db::table('sdata_d_rrd_name')->where('class','专线')->field('name,src')->select();
		$y = date('Y');
			$m = date('m');
			$m1 = $m-1;
			$d= date('d');
			$min = $y . '-' .$m1;
			$max = $y . '-' .$m;
			$mintime = strtotime($min); 
			$maxtime = strtotime($max); 
		for($i = 0;$i < count($name);$i++){
			//$data[$name[$i]['name']] 
			$select[$i] = Db::table('sdata_d_rrd_month')->where('time','>',$mintime)->where('time','<',$maxtime)->where('src',$name[$i]['src'])->field('time,traffic_in as up,traffic_out as down')->select();
			$strTime = '';

			$strUp = '';
			$strDown = '';
			for($s = 0;$s <count($select[$i]);$s++){
				$time[$s] = date('H:i',$select[$i][$s]['time']);
				if($select[$i][$s]['up'] == 'nan'){
					$select[$i][$s]['up'] = 0;
				}
				if($select[$i][$s]['down'] == 'nan'){
					$select[$i][$s]['down'] = 0;
				}
				$up[$s] = number_format($select[$i][$s]['up'],0,'','');
				$down[$s] = number_format($select[$i][$s]['down'],0,'','');	
			
				if(substr($time[$s], -2) == '00' || substr($time[$s], -2) == '30'){
					$strTime  .=  "'" . $time[$s] . "',";
				}else{
					$strTime  .=  "' '" . ',';
				}

				$strUp .= "'" . $up[$s] . "',";
				$strDown .= "'" . $down[$s] . "',";
				
			}
			$strTime = substr($strTime,0,strlen($strTime)-1);
			$strUp = substr($strUp,0,strlen($strUp)-1);
			$strDown = substr($strDown,0,strlen($strDown)-1);
			//$str = "{ 'xAxis': ["1月", "2月", "3月", "4月", "5月", "6月", "7月"],'data': [[10, 52, 200, 334, 390, 330, 220],  ]}"
			$data[$name[$i]['name']] = "{ 'xAxis': [" . $strTime . "],'data': [[" . $strUp . "],[" . $strDown . "]]}";
			
			
		}

		return $data[$lineName];		
	}

	public  function getWorkMonth()
		{
			$workName = Request::instance()->get('name');
			
			$name = Db::table('sdata_d_rrd_name')->where('class','办公区域')->field('name,src')->select();
			$y = date('Y');
				$m = date('m');
				$m1 = $m-1;
				$d= date('d');
				$min = $y . '-' .$m1;
				$max = $y . '-' .$m;
				$mintime = strtotime($min); 
				$maxtime = strtotime($max); 
			$data = '';
			for($i = 0;$i < count($name);$i++){
				//$data[$name[$i]['name']] 
				$select[$i] = Db::table('sdata_d_rrd_month')->where('time','>',$mintime)->where('time','<',$maxtime)->where('src',$name[$i]['src'])->field('time,traffic_in as up,traffic_out as down')->select();
				$strTime = '';

				$strUp = '';
				$strDown = '';
				for($s = 0;$s <count($select[$i]);$s++){
					$time[$s] = date('m-d H',$select[$i][$s]['time']);
					if($select[$i][$s]['up'] == 'nan'){
						$select[$i][$s]['up'] = 0;
					}
					if($select[$i][$s]['down'] == 'nan'){
						$select[$i][$s]['down'] = 0;
					}
					$up[$s] = number_format($select[$i][$s]['up'],0,'','');
					$down[$s] = 0 - number_format($select[$i][$s]['down'],0,'','');	
				
					if(substr($time[$s], -2) == '00' || substr($time[$s], -2) == '30'){
						$strTime  .=  "'" . $time[$s] . "',";
					}else{
						$strTime  .=  "' '" . ',';
					}

					$strUp .= "'" . $up[$s] . "',";
					$strDown .= "'" . $down[$s] . "',";
					
				}
				$strTime= substr($strTime,0,strlen($strTime)-1);
				//$strUp[$i] = substr($strUp,0,strlen($strUp)-1);
				//$strDown[$i] = substr($strDown,0,strlen($strDown)-1);
				//$str = "{ 'xAxis': ["1月", "2月", "3月", "4月", "5月", "6月", "7月"],'data': [[10, 52, 200, 334, 390, 330, 220],  ]}"     "{ 'xAxis': [" . $strTime . "],'data': ["  
				$data .= "[" .  $strUp . "],[" .  $strDown . "],";
				// [],[],
				
				
			}
				$return = "{ 'xAxis': [" . $strTime . "],'data': ["  . $data . "]}";
				return $return;

					
		}
	

	public  function getSystemMonth()
		{
			$systemName = Request::instance()->get('name');
			
			$name = Db::table('sdata_d_rrd_name')->where('class','重点系统')->field('name,src')->select();
			$y = date('Y');
				$m = date('m');
				$m1 = $m-1;
				$d= date('d');
				$min = $y . '-' .$m1;
				$max = $y . '-' .$m;
				$mintime = strtotime($min); 
				$maxtime = strtotime($max); 
				$sdata = '';
			for($i = 0;$i < count($name);$i++){
				//$data[$name[$i]['name']] 
				$select[$i] = Db::table('sdata_d_rrd_three_month')->where('time','>',$mintime)->where('time','<',$maxtime)->where('src',$name[$i]['src'])->field('time,data')->select();
				$strTime = '';
				$strData = '';
				for($s = 0;$s <count($select[$i]);$s++){
					$time[$s] = date('m-d H',$select[$i][$s]['time']);
					if($select[$i][$s]['data'] == 'nan'){
						$select[$i][$s]['data'] = 0;
					}
					
					$data[$s] = number_format($select[$i][$s]['data'],8,'.','');
				
					if(substr($time[$s], -2) == '00' || substr($time[$s], -2) == '30'){
					$strTime  .=  "'" . $time[$s] . "',";
				}else{
					$strTime  .=  "' '" . ',';
				}

				$strData .= "'" . $data[$s] . "',";
				
			}
			$strTime = substr($strTime,0,strlen($strTime)-1);
			//$strData = substr($strData,0,strlen($strData)-1);
			//$str = "{ 'xAxis': ["1月", "2月", "3月", "4月", "5月", "6月", "7月"],'data': [[10, 52, 200, 334, 390, 330, 220],  ]}"
			$sdata .= "[" .  $strData . "],";
				// [],[],
			//$data[$name[$i]['name']] = "{ 'xAxis': [" . $strTime . "],'data': [[" . $strData . "]]}";
			
				
				
			}

			$return = "{ 'xAxis': [" . $strTime . "],'data': ["  . $sdata . "]}";
				return $return;

		}
	

	

    

}
