<?php
namespace app\index\Controller;
use think\Db;
use think\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;


class file
{

	//判断单位bit -> kb -> mb
	protected $unit = 'bit';

	public function uploadFile(){
		//接收post来的数
		$put=input('post.'); 
		//提取username
		$username = $put['username'];
		//接收文件name
		$file = request()->file('file');
		//上传至路径
		$info = $file->move(ROOT_PATH . 'public' . DS . 'updirs','');	
		//在本机的路径
		$fileSrcWin = ROOT_PATH . 'public' . '\updirs\\'.$info->getSaveName();
		//斜线转换
		$fileSrc = str_replace('\\','/',$fileSrcWin);
		//更改权限
		chmod($fileSrc, 0777);
		//定义存储路径
		$insertSrc =  $_SERVER['REMOTE_ADDR'] . '/updirs/'.$info->getSaveName();
		//删除原始文件信息
		$delete = Db::name('excelfile')->where('username',$username)->where('name',$info->getSaveName())->delete();
		//添加文件
		$insert = Db::name('excelfile')->insert(['src' => $insertSrc,'name' => $info->getSaveName(),'username' => $username]);
		//使用phpexcel
        $objPHPExcel = IOFactory::load($fileSrc);
        //生成返回数据
        $arrExcel = $objPHPExcel->getSheet(0)->toArray();
        //返回数据
        return get_status(0,NULL,$arrExcel);
	}


	//获取RD库数据
	//$name 数据库名
	//$type CF
	protected function rd( $name , $type)
	{
		return rrd_fetch(RRD_PATH.$name,[$type]);
	}

	//去掉值为NAN的键值对
	protected function unsetNan($arr)
	{
		//定义输出数组
		$array = [];
		//遍历输入数组
		foreach ($arr as $key => $value) {
			//去除NAN
			if(strtolower($value) != 'nan') {
				//加入新数组保证初始化下标为0 json为数组
				$array[] = $value;
			}
		}
		//返回输出数组
		return $array;
	}

	//纵向坐标单位装换B->KB->MD
	protected function unit($arr)
	{
		//获取数组中最大的值
		$max = max($arr);
		//定义除数
		$unit = 1;
		//判断最大值，进行单位装换
		if($max > (1*1024*1024)){
			$unit = 1024*1024*8;
			$this->unit = 'Mb';
		}else if($max > (1*1024)){
			$unit = 1024*8;
			$this->unit = 'kb';
		}
		//遍历输入数组
		foreach ($arr as $key => $value) {
			//进行单位转换
			$val = $value/$unit;
			//保留两位小数点
			$val = floor($val*10000)/10000;
			//保存数组
			$arr[$key] = $val;
		}

		//返回输出数组
		return $arr;
	}

	//Traffic - GigabitEthernet141
	public function trafficGigabitEthernetID141() 
	{
		//查询数据库
		$data = $this->rd('10_225_1_200_traffic_in_146.rrd','AVERAGE');
		//获取traffic_in数据
		$in = $this->unsetNan($data['data']['traffic_in']);
		//获取traffic_out数据
		$out = $this->unsetNan($data['data']['traffic_out']);
		//获取时间戳键
		$xAxis = array_keys($data['data']['traffic_in']);
		//将时间戳转成时分格式
		foreach ($xAxis as $key => $value) {
			$xAxis[$key] = date('Y-m-d H:i',$value);
		}
		//转换单位
		$in = $this->unit($in);
		$out = $this->unit($out);
		//组成返回数据
		$arr = [
			'type' => ['line','line'],
			'data' => [$in,$out],
			'xAxis' => $xAxis,
			'dataName' => ["流入", "流出"],
			'yAxisLabelFormatter'=>["{value}".$this->unit],
			];
		return $arr;
	}

	//Traffic - GigabitEthernet142
	public function trafficGigabitEthernetID142() 
	{
		//查询数据库
		$data = $this->rd('10_225_1_200_traffic_in_147.rrd','AVERAGE');
		//获取traffic_in数据
		$in = $this->unsetNan($data['data']['traffic_in']);
		//获取traffic_out数据
		$out = $this->unsetNan($data['data']['traffic_out']);
		//获取时间戳键
		$xAxis = array_keys($data['data']['traffic_in']);
		//将时间戳转成时分格式
		foreach ($xAxis as $key => $value) {
			$xAxis[$key] = date('Y-m-d H:i',$value);
		}
		//转换单位
		$in = $this->unit($in);
		$out = $this->unit($out);
		//组成返回数据
		$arr = [
			'type' => ['line','line'],
			'data' => [$in,$out],
			'xAxis' => $xAxis,
			'dataName' => ["流入", "流出"],
			'yAxisLabelFormatter'=>"{value}" . $this->unit,
			];
		return $arr;
	}

	//Traffic - MCU 14.11
	public function trafficMCU1411ID108() 
	{
		//查询数据库
		$data = $this->rd('10_225_1_200_traffic_in_111.rrd','AVERAGE');
		//获取traffic_in数据
		$in = $this->unsetNan($data['data']['traffic_in']);
		//获取traffic_out数据
		$out = $this->unsetNan($data['data']['traffic_out']);
		//获取时间戳键
		$xAxis = array_keys($data['data']['traffic_in']);
		//将时间戳转成时分格式
		foreach ($xAxis as $key => $value) {
			$xAxis[$key] = date('Y-m-d H:i',$value);
		}
		//转换单位
		$in = $this->unit($in);
		$out = $this->unit($out);
		//组成返回数据
		$arr = [
			'type' => ['line','line'],
			'data' => [$in,$out],
			'xAxis' => $xAxis,
			'dataName' => ["流入", "流出"],
			'yAxisLabelFormatter'=>["{value}".$this->unit],
			];
		return $arr;
	}

	//Traffic - To Syslog Scan
	public function trafficToSyslogScanID106() 
	{
		//查询数据库
		$data = $this->rd('10_225_1_200_traffic_in_109.rrd','AVERAGE');
		//获取traffic_in数据
		$in = $this->unsetNan($data['data']['traffic_in']);
		//获取traffic_out数据
		$out = $this->unsetNan($data['data']['traffic_out']);
		//获取时间戳键
		$xAxis = array_keys($data['data']['traffic_in']);
		//将时间戳转成时分格式
		foreach ($xAxis as $key => $value) {
			$xAxis[$key] = date('Y-m-d H:i',$value);
		}
		//转换单位
		$in = $this->unit($in);
		$out = $this->unit($out);
		//组成返回数据
		$arr = [
			'type' => ['line','line'],
			'data' => [$in,$out],
			'xAxis' => $xAxis,
			'dataName' => ["流入", "流出"],
			'yAxisLabelFormatter'=>["{value}".$this->unit],
			];
		return $arr;
	}


	//Traffic - Vlanif1ID140
	public function trafficVlanif1ID140() 
	{
		//查询数据库
		$data = $this->rd('10_225_1_200_traffic_in_144.rrd','AVERAGE');
		//获取traffic_in数据
		$in = $this->unsetNan($data['data']['traffic_in']);
		//获取traffic_out数据
		$out = $this->unsetNan($data['data']['traffic_out']);
		//获取时间戳键
		$xAxis = array_keys($data['data']['traffic_in']);
		//将时间戳转成时分格式
		foreach ($xAxis as $key => $value) {
			$xAxis[$key] = date('Y-m-d H:i',$value);
		}
		//转换单位
		$in = $this->unit($in);
		$out = $this->unit($out);
		//组成返回数据
		$arr = [
			'type' => ['line','line'],
			'data' => [$in,$out],
			'xAxis' => $xAxis,
			'dataName' => ["流入", "流出"],
			'yAxisLabelFormatter'=>["{value}".$this->unit],
			];
		return $arr;
	}

	//WIFIID136
	public function WIFIID136() 
	{
		//查询数据库
		$data = $this->rd('10_225_1_200_traffic_in_138.rrd','AVERAGE');
		//获取traffic_in数据
		$in = $this->unsetNan($data['data']['traffic_in']);
		//获取traffic_out数据
		$out = $this->unsetNan($data['data']['traffic_out']);
		//获取时间戳键
		$xAxis = array_keys($data['data']['traffic_in']);
		//将时间戳转成时分格式
		foreach ($xAxis as $key => $value) {
			$xAxis[$key] = date('Y-m-d H:i',$value);
		}
		//转换单位
		$in = $this->unit($in);
		$out = $this->unit($out);
		//组成返回数据
		$arr = [
			'type' => ['line','line'],
			'data' => [$in,$out],
			'xAxis' => $xAxis,
			'dataName' => ["流入", "流出"],
			'yAxisLabelFormatter'=>["{value}".$this->unit],
			];
		return $arr;
	}

	//国勘专线	105
	public function guoKanID105()
	{
		//查询数据库
		$data = $this->rd('10_225_1_200__traffic_in_108.rrd','AVERAGE');
		//获取traffic_in数据
		$in = $this->unsetNan($data['data']['traffic_in']);
		//获取traffic_out数据
		$out = $this->unsetNan($data['data']['traffic_out']);
		//获取时间戳键
		$xAxis = array_keys($data['data']['traffic_in']);
		//将时间戳转成时分格式
		foreach ($xAxis as $key => $value) {
			$xAxis[$key] = date('Y-m-d H:i',$value);
		}
		//转换单位
		$in = $this->unit($in);
		$out = $this->unit($out);
		//组成返回数据
		$arr = [
			'type' => ['line','line'],
			'data' => [$in,$out],
			'xAxis' => $xAxis,
			'dataName' => ["流入", "流出"],
			'yAxisLabelFormatter'=>["{value}".$this->unit],
			];
		//格式化数组
		// json_encode($arr);
		// echo "<pre>";
		// var_dump($in);
		// var_dump($out);
		// var_dump($data['data']['traffic_in']);
		// echo max($out)/1024/1024*10;
		return $arr;
	}

	//Traffic - GigabitEthernet	111
	public function trafficGigabitEthernetID111()
	{
		//查询数据库
		$data = $this->rd('10_225_1_6_traffic_in_114.rrd','AVERAGE');
		//获取traffic_in数据
		$in = $this->unsetNan($data['data']['traffic_in']);
		//获取traffic_out数据
		$out = $this->unsetNan($data['data']['traffic_out']);
		//获取时间戳键
		$xAxis = array_keys($data['data']['traffic_in']);
		//将时间戳转成时分格式
		foreach ($xAxis as $key => $value) {
			$xAxis[$key] = date('Y-m-d H:i',$value);
		}
		//转换单位
		$in = $this->unit($in);
		$out = $this->unit($out);
		//组成返回数据
		$arr = [
			'type' => ['line','line'],
			'data' => [$in,$out],
			'xAxis' => $xAxis,
			'dataName' => ["流入", "流出"],
			'yAxisLabelFormatter'=>["{value}".$this->unit],
			];

		
		return $arr;
	}

	//Traffic - GigabitEthernet	110
	public function trafficGigabitEthernetID110()
	{
		//查询数据库
		$data = $this->rd('10_225_1_6_traffic_in_113.rrd','AVERAGE');
		//获取traffic_in数据
		$in = $this->unsetNan($data['data']['traffic_in']);
		//获取traffic_out数据
		$out = $this->unsetNan($data['data']['traffic_out']);
		//获取时间戳键
		$xAxis = array_keys($data['data']['traffic_in']);
		//将时间戳转成时分格式
		foreach ($xAxis as $key => $value) {
			$xAxis[$key] = date('Y-m-d H:i',$value);
		}
		//转换单位
		$in = $this->unit($in);
		$out = $this->unit($out);
		//组成返回数据
		$arr = [
			'type' => ['line','line'],
			'data' => [$in,$out],
			'xAxis' => $xAxis,
			'dataName' => ["流入", "流出"],
			'yAxisLabelFormatter'=>["{value}".$this->unit],
			];
	
		return $arr;
	}


	//Traffic -to wuxi	109
	public function traffictowuxiID109()
	{
		//查询数据库
		$data = $this->rd('10_225_1_6_traffic_in_112.rrd','AVERAGE');
		//获取traffic_in数据
		$in = $this->unsetNan($data['data']['traffic_in']);
		//获取traffic_out数据
		$out = $this->unsetNan($data['data']['traffic_out']);
		//获取时间戳键
		$xAxis = array_keys($data['data']['traffic_in']);
		//将时间戳转成时分格式
		foreach ($xAxis as $key => $value) {
			$xAxis[$key] = date('Y-m-d H:i',$value);
		}
		//转换单位
		$in = $this->unit($in);
		$out = $this->unit($out);
		//组成返回数据
		$arr = [
			'type' => ['line','line'],
			'data' => [$in,$out],
			'xAxis' => $xAxis,
			'dataName' => ["流入", "流出"],
			'yAxisLabelFormatter'=>["{value}".$this->unit],
			];
		return $arr;
	}

	//合肥专线	102
	public function heFeiID102()
	{
		//查询数据库
		$data = $this->rd('10_225_1_6_traffic_in_105.rrd','AVERAGE');
		//获取traffic_in数据
		$in = $this->unsetNan($data['data']['traffic_in']);
		//获取traffic_out数据
		$out = $this->unsetNan($data['data']['traffic_out']);
		//获取时间戳键
		$xAxis = array_keys($data['data']['traffic_in']);
		//将时间戳转成时分格式
		foreach ($xAxis as $key => $value) {
			$xAxis[$key] = date('Y-m-d H:i',$value);
		}
		//转换单位
		$in = $this->unit($in);
		$out = $this->unit($out);
		//组成返回数据
		$arr = [
			'type' => ['line','line'],
			'data' => [$in,$out],
			'xAxis' => $xAxis,
			'dataName' => ["流入", "流出"],
			'yAxisLabelFormatter'=>["{value}".$this->unit],
			];
		return $arr;
	}
	
	//无锡专线	103
	public function wuXiID103()
	{
		//查询数据库
		$data = $this->rd('10_225_1_6_traffic_in_106.rrd','AVERAGE');
		//获取traffic_in数据
		$in = $this->unsetNan($data['data']['traffic_in']);
		//获取traffic_out数据
		$out = $this->unsetNan($data['data']['traffic_out']);
		//获取时间戳键
		$xAxis = array_keys($data['data']['traffic_in']);
		//将时间戳转成时分格式
		foreach ($xAxis as $key => $value) {
			$xAxis[$key] = date('Y-m-d H:i',$value);
		}
		//转换单位
		$in = $this->unit($in);
		$out = $this->unit($out);
		//组成返回数据
		$arr = [
			'type' => ['line','line'],
			'data' => [$in,$out],
			'xAxis' => $xAxis,
			'dataName' => ["流入", "流出"],
			'yAxisLabelFormatter'=>["{value}".$this->unit],
			];
		return $arr;
	}

	//EOM财务系统	124
	public function EOMID124()
	{
		//查询数据库（第一条线）
		$data = $this->rd('eom_avg_128.rrd','AVERAGE');
		//获取avg数据
		$in = $this->unsetNan($data['data']['avg']);
		//查询数据库（第二条线）
		$datas = $this->rd('eom_avg_128.rrd','MAX');
		//获取avg数据
		$out = $this->unsetNan($datas['data']['avg']);
		//获取时间戳键
		$xAxis = array_keys($data['data']['avg']);
		//将时间戳转成时分格式
		foreach ($xAxis as $key => $value) {
			$xAxis[$key] = date('Y-m-d H:i',$value);
		}
		//组成返回数据
		$arr = [
			'type' => ['line','line'],
			'data' => [$in,$out],
			'xAxis' => $xAxis,
			'dataName' => [" ", " "],
			];
		return $arr;
	}
	
	//Load Average	2
	public function loadAverageID2()
	{
		//查询数据库
		$data = $this->rd('localhost_load_1min_5.rrd','AVERAGE');
		// echo "<pre>";
		// var_dump($data);exit();
		//获取load_1min数据
		$in = $this->unsetNan($data['data']['load_1min']);
		//获取load_5min数据
		$out = $this->unsetNan($data['data']['load_5min']);
		//获取load_15min数据
		$x = $this->unsetNan($data['data']['load_15min']);
		//获取时间戳键
		$xAxis = array_keys($data['data']['load_1min']);
		//将时间戳转成时分格式
		foreach ($xAxis as $key => $value) {
			$xAxis[$key] = date('Y-m-d H:i',$value);
		}
		//组成返回数据
		$arr = [
			'type' => ['line','line','line'],
			'data' => [$in,$out,$x],
			'xAxis' => $xAxis,
			'dataName' => ["1分钟平均值", "5分钟平均值","15分钟平均值"],
			];
		return $arr;
	}
	
	//Logged in Users	3
	public function loggedInUsersID3()
	{
		//查询数据库
		$data = $this->rd('localhost_users_6.rrd','AVERAGE');
		//获取users数据
		$in = $this->unsetNan($data['data']['users']);
		//获取时间戳键
		$xAxis = array_keys($data['data']['users']);
		//将时间戳转成时分格式
		foreach ($xAxis as $key => $value) {
			$xAxis[$key] = date('Y-m-d H:i',$value);
		}
		//组成返回数据
		$arr = [
			'type' => ['line'],
			'data' => [$in],
			'xAxis' => $xAxis,
			'dataName' => ["Users"],
			];
		return $arr;
	}
	
	//Memory Usage	1
	public function memoryUsageID1()
	{
		//查询数据库（第一条线）
		$data = $this->rd('localhost_mem_buffers_3.rrd','AVERAGE');
		//获取mem_buffers数据
		$in = $this->unsetNan($data['data']['mem_buffers']);
		//查询数据库（第二条线）
		$datas = $this->rd('localhost_mem_swap_4.rrd','AVERAGE');
		//获取mem_swap数据
		$out = $this->unsetNan($datas['data']['mem_swap']);
		//获取时间戳键
		$xAxis = array_keys($data['data']['mem_buffers']);
		//将时间戳转成时分格式
		foreach ($xAxis as $key => $value) {
			$xAxis[$key] = date('Y-m-d H:i',$value);
		}
		//组成返回数据
		$arr = [
			'type' => ['line','line'],
			'data' => [$in,$out],
			'xAxis' => $xAxis,
			'dataName' => ["Free", "Swap"],
			];
		return $arr;
	}
	
	//Processes	4
	public function processesID4()
	{
		//查询数据库（第一条线）
		$data = $this->rd('localhost_proc_7.rrd','AVERAGE');
		//获取mem_buffers数据
		$in = $this->unsetNan($data['data']['proc']);
		//获取时间戳键
		$xAxis = array_keys($data['data']['proc']);
		//将时间戳转成时分格式
		foreach ($xAxis as $key => $value) {
			$xAxis[$key] = date('Y-m-d H:i',$value);
		}
		//组成返回数据
		$arr = [
			'type' => ['line','line'],
			'data' => [$in],
			'xAxis' => $xAxis,
			'dataName' => ["Running Processes"],
			];
		return $arr;
	}

	//专利ID125
	public function zhuanliID125()
	{
		//查询数据库（第一条线）
		$data = $this->rd('_avg_129.rrd','AVERAGE');
		//获取avg数据
		$in = $this->unsetNan($data['data']['avg']);
		//查询数据库（第二条线）
		$datas = $this->rd('_avg_129.rrd','MAX');
		//获取avg数据
		$out = $this->unsetNan($datas['data']['avg']);
		//获取时间戳键
		$xAxis = array_keys($data['data']['avg']);
		//将时间戳转成时分格式
		foreach ($xAxis as $key => $value) {
			$xAxis[$key] = date('Y-m-d H:i',$value);
		}
		//组成返回数据
		$arr = [
			'type' => ['line','line'],
			'data' => [$in,$out],
			'xAxis' => $xAxis,
			'dataName' => [" ", " "],
			];
		return $arr;
	}

	//人力资源平台	128
	public function rengLiID128()
	{
		//查询数据库（第一条线）
		$data = $this->rd('_avg_132.rrd','AVERAGE');
		//获取avg数据
		$in = $this->unsetNan($data['data']['avg']);
		//查询数据库（第二条线）
		$datas = $this->rd('_avg_132.rrd','MAX');
		//获取avg数据
		$out = $this->unsetNan($datas['data']['avg']);
		//获取时间戳键
		$xAxis = array_keys($data['data']['avg']);
		//将时间戳转成时分格式
		foreach ($xAxis as $key => $value) {
			$xAxis[$key] = date('Y-m-d H:i',$value);
		}
		//组成返回数据
		$arr = [
			'type' => ['line','line'],
			'data' => [$in,$out],
			'xAxis' => $xAxis,
			'dataName' => [" ", " "],
			];
		return $arr;
	}

	//合同管理系统	129
	public function heTongID129()
	{
		//查询数据库（第一条线）
		$data = $this->rd('_avg_133.rrd','AVERAGE');
		//获取avg数据
		$in = $this->unsetNan($data['data']['avg']);
		//查询数据库（第二条线）
		$datas = $this->rd('_avg_133.rrd','MAX');
		//获取avg数据
		$out = $this->unsetNan($datas['data']['avg']);
		//获取时间戳键
		$xAxis = array_keys($data['data']['avg']);
		//将时间戳转成时分格式
		foreach ($xAxis as $key => $value) {
			$xAxis[$key] = date('Y-m-d H:i',$value);
		}
		//组成返回数据
		$arr = [
			'type' => ['line','line'],
			'data' => [$in,$out],
			'xAxis' => $xAxis,
			'dataName' => [" ", " "],
			];
		return $arr;
	}
	
	//外事系统	130
	public function waiShiID130()
	{
		//查询数据库（第一条线）
		$data = $this->rd('_avg_134.rrd','AVERAGE');
		//获取avg数据
		$in = $this->unsetNan($data['data']['avg']);
		//查询数据库（第二条线）
		$datas = $this->rd('_avg_134.rrd','MAX');
		//获取avg数据
		$out = $this->unsetNan($datas['data']['avg']);
		//获取时间戳键
		$xAxis = array_keys($data['data']['avg']);
		//将时间戳转成时分格式
		foreach ($xAxis as $key => $value) {
			$xAxis[$key] = date('Y-m-d H:i',$value);
		}
		//组成返回数据
		$arr = [
			'type' => ['line','line'],
			'data' => [$in,$out],
			'xAxis' => $xAxis,
			'dataName' => [" ", " "],
			];
		return $arr;
	}


	//总部公文系统	126
	public function ZBGWID126()
	{
		//查询数据库（第一条线）
		$data = $this->rd('_avg_130.rrd','AVERAGE');
		//获取avg数据
		$in = $this->unsetNan($data['data']['avg']);
		//查询数据库（第二条线）
		$datas = $this->rd('_avg_130.rrd','MAX');
		//获取avg数据
		$out = $this->unsetNan($datas['data']['avg']);
		//获取时间戳键
		$xAxis = array_keys($data['data']['avg']);
		//将时间戳转成时分格式
		foreach ($xAxis as $key => $value) {
			$xAxis[$key] = date('Y-m-d H:i',$value);
		}
		//组成返回数据
		$arr = [
			'type' => ['line','line'],
			'data' => [$in,$out],
			'xAxis' => $xAxis,
			'dataName' => [" ", " "],
			];
		return $arr;
	}

	//总部财务系统	127
	public function ZBCWID127()
	{
		//查询数据库（第一条线）
		$data = $this->rd('_avg_131.rrd','AVERAGE');
		//获取avg数据
		$in = $this->unsetNan($data['data']['avg']);
		//查询数据库（第二条线）
		$datas = $this->rd('_avg_131.rrd','MAX');
		//获取avg数据
		$out = $this->unsetNan($datas['data']['avg']);
		//获取时间戳键
		$xAxis = array_keys($data['data']['avg']);
		//将时间戳转成时分格式
		foreach ($xAxis as $key => $value) {
			$xAxis[$key] = date('Y-m-d H:i',$value);
		}
		//组成返回数据
		$arr = [
			'type' => ['line','line'],
			'data' => [$in,$out],
			'xAxis' => $xAxis,
			'dataName' => [" ", " "],
			];
		return $arr;
	}

	//战略情报系统	132
	public function ZLQBID132()
	{
		//查询数据库（第一条线）
		$data = $this->rd('_avg_136.rrd','AVERAGE');
		//获取avg数据
		$in = $this->unsetNan($data['data']['avg']);
		//查询数据库（第二条线）
		$datas = $this->rd('_avg_136.rrd','MAX');
		//获取avg数据
		$out = $this->unsetNan($datas['data']['avg']);
		//获取时间戳键
		$xAxis = array_keys($data['data']['avg']);
		//将时间戳转成时分格式
		foreach ($xAxis as $key => $value) {
			$xAxis[$key] = date('Y-m-d H:i',$value);
		}
		//组成返回数据
		$arr = [
			'type' => ['line','line'],
			'data' => [$in,$out],
			'xAxis' => $xAxis,
			'dataName' => [" ", " "],
			];
		return $arr;
	}


	//Non-Unicast Packets - XGigabitEtherne	144
	public function nonUnicastPacketsXGigabitEtherneID144() 
	{
		//查询数据库
		$data = $this->rd('_nonunicast_out_151.rrd','AVERAGE');
		//获取traffic_in数据
		$in = $this->unsetNan($data['data']['nonunicast_in']);
		//获取traffic_out数据
		$out = $this->unsetNan($data['data']['nonunicast_out']);
		//获取时间戳键
		$xAxis = array_keys($data['data']['nonunicast_in']);
		//将时间戳转成时分格式
		foreach ($xAxis as $key => $value) {
			$xAxis[$key] = date('Y-m-d H:i',$value);
		}
		//组成返回数据
		$arr = [
			'type' => ['line','line'],
			'data' => [$in,$out],
			'xAxis' => $xAxis,
			'dataName' => ["Non-Unicast Packets In", "Non-Unicast Packets Out"],
			];
		return $arr;
	}

	// Traffic - Vlanif37	114
	public function trafficVlanif37ID114() 
	{
		//查询数据库
		$data = $this->rd('_traffic_in_117.rrd','AVERAGE');
		//获取traffic_in数据
		$in = $this->unsetNan($data['data']['traffic_in']);
		//获取traffic_out数据
		$out = $this->unsetNan($data['data']['traffic_out']);
		//获取时间戳键
		$xAxis = array_keys($data['data']['traffic_in']);
		//将时间戳转成时分格式
		foreach ($xAxis as $key => $value) {
			$xAxis[$key] = date('Y-m-d H:i',$value);
		}
		//转换单位
		$in = $this->unit($in);
		$out = $this->unit($out);
		//组成返回数据
		$arr = [
			'type' => ['line','line'],
			'data' => [$in,$out],
			'xAxis' => $xAxis,
			'dataName' => ["流入", "流出"],
			'yAxisLabelFormatter'=>["{value}".$this->unit],
			];
		return $arr;
	}

	//Traffic - XGigabitEtherne	116
	public function trafficXGigabitEtherneID116() 
	{
		//查询数据库
		$data = $this->rd('_traffic_in_119.rrd','AVERAGE');
		//获取traffic_in数据
		$in = $this->unsetNan($data['data']['traffic_in']);
		//获取traffic_out数据
		$out = $this->unsetNan($data['data']['traffic_out']);
		//获取时间戳键
		$xAxis = array_keys($data['data']['traffic_in']);
		//将时间戳转成时分格式
		foreach ($xAxis as $key => $value) {
			$xAxis[$key] = date('Y-m-d H:i',$value);
		}
		//转换单位
		$in = $this->unit($in);
		$out = $this->unit($out);
		//组成返回数据
		$arr = [
			'type' => ['line','line'],
			'data' => [$in,$out],
			'xAxis' => $xAxis,
			'dataName' => ["流入", "流出"],
			'yAxisLabelFormatter'=>["{value}".$this->unit],
			];
		return $arr;
	}

	//Traffic - XGigabitEtherne	115
	public function trafficXGigabitEtherneID115() 
	{
		//查询数据库
		$data = $this->rd('_traffic_in_118.rrd','AVERAGE');
		//获取traffic_in数据
		$in = $this->unsetNan($data['data']['traffic_in']);
		//获取traffic_out数据
		$out = $this->unsetNan($data['data']['traffic_out']);
		//获取时间戳键
		$xAxis = array_keys($data['data']['traffic_in']);
		//将时间戳转成时分格式
		foreach ($xAxis as $key => $value) {
			$xAxis[$key] = date('Y-m-d H:i',$value);
		}
		//转换单位
		$in = $this->unit($in);
		$out = $this->unit($out);
		//组成返回数据
		$arr = [
			'type' => ['line','line'],
			'data' => [$in,$out],
			'xAxis' => $xAxis,
			'dataName' => ["流入", "流出"],
			'yAxisLabelFormatter'=>["{value}".$this->unit],
			];
		return $arr;
	}

	//Unicast Packets - XGigabitEtherne	145
	public function unicastPacketsXGigabitEtherneID145() 
	{
		//查询数据库
		$data = $this->rd('_unicast_in_152.rrd','AVERAGE');
		//获取traffic_in数据
		$in = $this->unsetNan($data['data']['unicast_in']);
		//获取traffic_out数据
		$out = $this->unsetNan($data['data']['unicast_out']);
		//获取时间戳键
		$xAxis = array_keys($data['data']['unicast_in']);
		//将时间戳转成时分格式
		foreach ($xAxis as $key => $value) {
			$xAxis[$key] = date('Y-m-d H:i',$value);
		}
		//组成返回数据
		$arr = [
			'type' => ['line','line'],
			'data' => [$in,$out],
			'xAxis' => $xAxis,
			'dataName' => ["Unicast Packets In", "Unicast Packets Out"],
			];
		return $arr;
	}

	//主楼一层	86
	public function ZL1CID86() 
	{
		//查询数据库
		$data = $this->rd('_traffic_in_89.rrd','AVERAGE');
		//获取traffic_in数据
		$in = $this->unsetNan($data['data']['traffic_in']);
		//获取traffic_out数据
		$out = $this->unsetNan($data['data']['traffic_out']);
		//获取时间戳键
		$xAxis = array_keys($data['data']['traffic_in']);
		//将时间戳转成时分格式
		foreach ($xAxis as $key => $value) {
			$xAxis[$key] = date('Y-m-d H:i',$value);
		}
		//转换单位
		$in = $this->unit($in);
		$out = $this->unit($out);
		//组成返回数据
		$arr = [
			'type' => ['line','line'],
			'data' => [$in,$out],
			'xAxis' => $xAxis,
			'dataName' => ["流入", "流出"],
			'yAxisLabelFormatter'=>["{value}".$this->unit],
			];
		return $arr;
	}

	//主楼三层东侧	82
	public function ZL3CDCID82() 
	{
		//查询数据库
		$data = $this->rd('_traffic_in_85.rrd','AVERAGE');
		//获取traffic_in数据
		$in = $this->unsetNan($data['data']['traffic_in']);
		//获取traffic_out数据
		$out = $this->unsetNan($data['data']['traffic_out']);
		//获取时间戳键
		$xAxis = array_keys($data['data']['traffic_in']);
		//将时间戳转成时分格式
		foreach ($xAxis as $key => $value) {
			$xAxis[$key] = date('Y-m-d H:i',$value);
		}
		//转换单位
		$in = $this->unit($in);
		$out = $this->unit($out);
		//组成返回数据
		$arr = [
			'type' => ['line','line'],
			'data' => [$in,$out],
			'xAxis' => $xAxis,
			'dataName' => ["流入", "流出"],
			'yAxisLabelFormatter'=>["{value}".$this->unit],
			];
		return $arr;
	}

	//主楼三层西侧	83
	public function ZL3CXCID83() 
	{
		//查询数据库
		$data = $this->rd('_traffic_in_86.rrd','AVERAGE');
		//获取traffic_in数据
		$in = $this->unsetNan($data['data']['traffic_in']);
		//获取traffic_out数据
		$out = $this->unsetNan($data['data']['traffic_out']);
		//获取时间戳键
		$xAxis = array_keys($data['data']['traffic_in']);
		//将时间戳转成时分格式
		foreach ($xAxis as $key => $value) {
			$xAxis[$key] = date('Y-m-d H:i',$value);
		}
		//转换单位
		$in = $this->unit($in);
		$out = $this->unit($out);
		//组成返回数据
		$arr = [
			'type' => ['line','line'],
			'data' => [$in,$out],
			'xAxis' => $xAxis,
			'dataName' => ["流入", "流出"],
			'yAxisLabelFormatter'=>["{value}".$this->unit],
			];
		return $arr;
	}

	//主楼二层东侧	84
	public function ZL2CDCID84() 
	{
		//查询数据库
		$data = $this->rd('_traffic_in_87.rrd','AVERAGE');
		//获取traffic_in数据
		$in = $this->unsetNan($data['data']['traffic_in']);
		//获取traffic_out数据
		$out = $this->unsetNan($data['data']['traffic_out']);
		//获取时间戳键
		$xAxis = array_keys($data['data']['traffic_in']);
		//将时间戳转成时分格式
		foreach ($xAxis as $key => $value) {
			$xAxis[$key] = date('Y-m-d H:i',$value);
		}
		//转换单位
		$in = $this->unit($in);
		$out = $this->unit($out);
		//组成返回数据
		$arr = [
			'type' => ['line','line'],
			'data' => [$in,$out],
			'xAxis' => $xAxis,
			'dataName' => ["流入", "流出"],
			'yAxisLabelFormatter'=>["{value}".$this->unit],
			];
		return $arr;
	}

	//主楼二层西侧	85
	public function ZL2CXCID85() 
	{
		//查询数据库
		$data = $this->rd('_traffic_in_88.rrd','AVERAGE');
		//获取traffic_in数据
		$in = $this->unsetNan($data['data']['traffic_in']);
		//获取traffic_out数据
		$out = $this->unsetNan($data['data']['traffic_out']);
		//获取时间戳键
		$xAxis = array_keys($data['data']['traffic_in']);
		//将时间戳转成时分格式
		foreach ($xAxis as $key => $value) {
			$xAxis[$key] = date('Y-m-d H:i',$value);
		}
		//转换单位
		$in = $this->unit($in);
		$out = $this->unit($out);
		//组成返回数据
		$arr = [
			'type' => ['line','line'],
			'data' => [$in,$out],
			'xAxis' => $xAxis,
			'dataName' => ["流入", "流出"],
			'yAxisLabelFormatter'=>["{value}".$this->unit],
			];
		return $arr;
	}

	//主楼四层东侧	80
	public function ZL4CDCID80() 
	{
		//查询数据库
		$data = $this->rd('_traffic_in_83.rrd','AVERAGE');
		//获取traffic_in数据
		$in = $this->unsetNan($data['data']['traffic_in']);
		//获取traffic_out数据
		$out = $this->unsetNan($data['data']['traffic_out']);
		//获取时间戳键
		$xAxis = array_keys($data['data']['traffic_in']);
		//将时间戳转成时分格式
		foreach ($xAxis as $key => $value) {
			$xAxis[$key] = date('Y-m-d H:i',$value);
		}
		//转换单位
		$in = $this->unit($in);
		$out = $this->unit($out);
		//组成返回数据
		$arr = [
			'type' => ['line','line'],
			'data' => [$in,$out],
			'xAxis' => $xAxis,
			'dataName' => ["流入", "流出"],
			'yAxisLabelFormatter'=>["{value}".$this->unit],
			];
		return $arr;
	}


	//主楼四层西侧	81
	public function ZL4CXCID81() 
	{
		//查询数据库
		$data = $this->rd('_traffic_in_84.rrd','AVERAGE');
		//获取traffic_in数据
		$in = $this->unsetNan($data['data']['traffic_in']);
		//获取traffic_out数据
		$out = $this->unsetNan($data['data']['traffic_out']);
		//获取时间戳键
		$xAxis = array_keys($data['data']['traffic_in']);
		//将时间戳转成时分格式
		foreach ($xAxis as $key => $value) {
			$xAxis[$key] = date('Y-m-d H:i',$value);
		}
		//转换单位
		$in = $this->unit($in);
		$out = $this->unit($out);
		//组成返回数据
		$arr = [
			'type' => ['line','line'],
			'data' => [$in,$out],
			'xAxis' => $xAxis,
			'dataName' => ["流入", "流出"],
			'yAxisLabelFormatter'=>["{value}".$this->unit],
			];
		return $arr;
	}

	// 互联网	98
	public function HLWID98() 
	{
		//查询数据库
		$data = $this->rd('_traffic_in_101.rrd','AVERAGE');
		//获取traffic_in数据
		$in = $this->unsetNan($data['data']['traffic_in']);
		//获取traffic_out数据
		$out = $this->unsetNan($data['data']['traffic_out']);
		//获取时间戳键
		$xAxis = array_keys($data['data']['traffic_in']);
		//将时间戳转成时分格式
		foreach ($xAxis as $key => $value) {
			$xAxis[$key] = date('Y-m-d H:i',$value);
		}
		//转换单位
		$in = $this->unit($in);
		$out = $this->unit($out);
		//组成返回数据
		$arr = [
			'type' => ['line','line'],
			'data' => [$in,$out],
			'xAxis' => $xAxis,
			'dataName' => ["流入", "流出"],
			'yAxisLabelFormatter'=>["{value}".$this->unit],
			];
		return $arr;
	}

	// 奥运大厦	79
	public function AYDXID79() 
	{
		//查询数据库
		$data = $this->rd('_traffic_in_82.rrd','AVERAGE');
		//获取traffic_in数据
		$in = $this->unsetNan($data['data']['traffic_in']);
		//获取traffic_out数据
		$out = $this->unsetNan($data['data']['traffic_out']);
		//获取时间戳键
		$xAxis = array_keys($data['data']['traffic_in']);
		//将时间戳转成时分格式
		foreach ($xAxis as $key => $value) {
			$xAxis[$key] = date('Y-m-d H:i',$value);
		}
		//转换单位
		$in = $this->unit($in);
		$out = $this->unit($out);
		//组成返回数据
		$arr = [
			'type' => ['line','line'],
			'data' => [$in,$out],
			'xAxis' => $xAxis,
			'dataName' => ["流入", "流出"],
			'yAxisLabelFormatter'=>["{value}".$this->unit],
			];
		return $arr;
	}

	//总部专线	104
	public function ZBZXID104() 
	{
		//查询数据库
		$data = $this->rd('_traffic_in_107.rrd','AVERAGE');
		//获取traffic_in数据
		$in = $this->unsetNan($data['data']['traffic_in']);
		//获取traffic_out数据
		$out = $this->unsetNan($data['data']['traffic_out']);
		//获取时间戳键
		$xAxis = array_keys($data['data']['traffic_in']);
		//将时间戳转成时分格式
		foreach ($xAxis as $key => $value) {
			$xAxis[$key] = date('Y-m-d H:i',$value);
		}
		//转换单位
		$in = $this->unit($in);
		$out = $this->unit($out);
		//组成返回数据
		$arr = [
			'type' => ['line','line'],
			'data' => [$in,$out],
			'xAxis' => $xAxis,
			'dataName' => ["流入", "流出"],
			'yAxisLabelFormatter'=>["{value}".$this->unit],
			];
		return $arr;
	}

	//虚拟桌面	117
	public function XNZMID117() 
	{
		//查询数据库
		$data = $this->rd('_traffic_in_120.rrd','AVERAGE');
		//获取traffic_in数据
		$in = $this->unsetNan($data['data']['traffic_in']);
		//获取traffic_out数据
		$out = $this->unsetNan($data['data']['traffic_out']);
		//获取时间戳键
		$xAxis = array_keys($data['data']['traffic_in']);
		//将时间戳转成时分格式
		foreach ($xAxis as $key => $value) {
			$xAxis[$key] = date('Y-m-d H:i',$value);
		}
		//转换单位
		$in = $this->unit($in);
		$out = $this->unit($out);
		//组成返回数据
		$arr = [
			'type' => ['line','line'],
			'data' => [$in,$out],
			'xAxis' => $xAxis,
			'dataName' => ["流入", "流出"],
			'yAxisLabelFormatter'=>["{value}".$this->unit],
			];
		return $arr;
	}

	//虚拟桌面	94
	public function XBZMID94() 
	{
		//查询数据库
		$data = $this->rd('_traffic_in_97.rrd','AVERAGE');
		//获取traffic_in数据
		$in = $this->unsetNan($data['data']['traffic_in']);
		//获取traffic_out数据
		$out = $this->unsetNan($data['data']['traffic_out']);
		//获取时间戳键
		$xAxis = array_keys($data['data']['traffic_in']);
		//将时间戳转成时分格式
		foreach ($xAxis as $key => $value) {
			$xAxis[$key] = date('Y-m-d H:i',$value);
		}
		//转换单位
		$in = $this->unit($in);
		$out = $this->unit($out);
		//组成返回数据
		$arr = [
			'type' => ['line','line'],
			'data' => [$in,$out],
			'xAxis' => $xAxis,
			'dataName' => ["流入", "流出"],
			'yAxisLabelFormatter'=>["{value}".$this->unit],
			];
		return $arr;
	}


	//计算楼	91
	public function JSLID91() 
	{
		//查询数据库
		$data = $this->rd('_traffic_in_94.rrd','AVERAGE');
		//获取traffic_in数据
		$in = $this->unsetNan($data['data']['traffic_in']);
		//获取traffic_out数据
		$out = $this->unsetNan($data['data']['traffic_out']);
		//获取时间戳键
		$xAxis = array_keys($data['data']['traffic_in']);
		//将时间戳转成时分格式
		foreach ($xAxis as $key => $value) {
			$xAxis[$key] = date('Y-m-d H:i',$value);
		}
		//转换单位
		$in = $this->unit($in);
		$out = $this->unit($out);
		//组成返回数据
		$arr = [
			'type' => ['line','line'],
			'data' => [$in,$out],
			'xAxis' => $xAxis,
			'dataName' => ["流入", "流出"],
			'yAxisLabelFormatter'=>["{value}".$this->unit],
			];
		return $arr;
	}

	//科技网系统	131
	public function KJWXIID131() 
	{
		//查询数据库（第一条线）
		$data = $this->rd('_avg_135.rrd','AVERAGE');
		//获取avg数据
		$in = $this->unsetNan($data['data']['avg']);
		//查询数据库（第二条线）
		$datas = $this->rd('_avg_135.rrd','MAX');
		//获取avg数据
		$out = $this->unsetNan($datas['data']['avg']);
		//获取时间戳键
		$xAxis = array_keys($data['data']['avg']);
		//将时间戳转成时分格式
		foreach ($xAxis as $key => $value) {
			$xAxis[$key] = date('Y-m-d H:i',$value);
		}
		//组成返回数据
		$arr = [
			'type' => ['line','line'],
			'data' => [$in,$out],
			'xAxis' => $xAxis,
			'dataName' => [" ", " "],
			];
		return $arr;
	
	}

	//综合业务系统	123
	public function ZHYWXTID123() 
	{
		//查询数据库（第一条线）
		$data = $this->rd('_avg_127.rrd','AVERAGE');
		//获取avg数据
		$in = $this->unsetNan($data['data']['avg']);
		//查询数据库（第二条线）
		$datas = $this->rd('_avg_127.rrd','MAX');
		//获取avg数据
		$out = $this->unsetNan($datas['data']['avg']);
		//获取时间戳键
		$xAxis = array_keys($data['data']['avg']);
		//将时间戳转成时分格式
		foreach ($xAxis as $key => $value) {
			$xAxis[$key] = date('Y-m-d H:i',$value);
		}
		//组成返回数据
		$arr = [
			'type' => ['line','line'],
			'data' => [$in,$out],
			'xAxis' => $xAxis,
			'dataName' => [" ", " "],
			];
		return $arr;
	}

	//综合业务系统 - HTTP Response Time	122
	public function ZHYWXTHTTPResponseTimeID122() 
	{
		//查询数据库（第一条线）
		$data = $this->rd('_avg_126.rrd','MAX');
		//获取avg数据
		$in = $this->unsetNan($data['data']['avg']);
		//查询数据库（第二条线）
		$datas = $this->rd('_avg_126.rrd','AVERAGE');
		//获取avg数据
		$out = $this->unsetNan($datas['data']['avg']);
		//获取时间戳键
		$xAxis = array_keys($data['data']['avg']);
		//将时间戳转成时分格式
		foreach ($xAxis as $key => $value) {
			$xAxis[$key] = date('Y-m-d H:i',$value);
		}
		//组成返回数据
		$arr = [
			'type' => ['line','line'],
			'data' => [$in,$out],
			'xAxis' => $xAxis,
			'dataName' => [" ", " "],
			];
		return $arr;
	}

	//门户系统	120
	public function MHXTID120() 
	{
		//查询数据库（第一条线）
		$data = $this->rd('_avg_123.rrd','AVERAGE');
		//获取avg数据
		$in = $this->unsetNan($data['data']['avg']);
		//查询数据库（第二条线）
		$datas = $this->rd('_avg_123.rrd','MAX');
		//获取avg数据
		$out = $this->unsetNan($datas['data']['avg']);
		//获取时间戳键
		$xAxis = array_keys($data['data']['avg']);
		//将时间戳转成时分格式
		foreach ($xAxis as $key => $value) {
			$xAxis[$key] = date('Y-m-d H:i',$value);
		}
		//组成返回数据
		$arr = [
			'type' => ['line','line'],
			'data' => [$in,$out],
			'xAxis' => $xAxis,
			'dataName' => [" ", " "],
			];
		return $arr;
	}

	//门户系统 - HTTP 响应时间	119
	public function MHXTHTTPResponseTimeID119() 
	{
		//查询数据库（第一条线）
		$data = $this->rd('_avg_122.rrd','AVERAGE');
		//获取avg数据
		$in = $this->unsetNan($data['data']['avg']);
		//查询数据库（第二条线）
		$datas = $this->rd('_avg_122.rrd','MAX');
		//获取avg数据
		$out = $this->unsetNan($datas['data']['avg']);
		//获取时间戳键
		$xAxis = array_keys($data['data']['avg']);
		//将时间戳转成时分格式
		foreach ($xAxis as $key => $value) {
			$xAxis[$key] = date('Y-m-d H:i',$value);
		}
		//组成返回数据
		$arr = [
			'type' => ['line','line'],
			'data' => [$in,$out],
			'xAxis' => $xAxis, 
			'dataName' => [" ", " "],
		];
		return $arr;
	}

	//办公区域堆叠图
	public function DDT()
	{
		//查询数据库（奥运）
		$ay = $this->rd('_traffic_in_82.rrd','AVERAGE');
		// dump($ay);exit();
		//获取traffic_in数据
		$ayin = $this->unsetNan($ay['data']['traffic_in']);
		//获取traffic_out数据
		$ayout = $this->unsetNan($ay['data']['traffic_out']);
		//查询数据库(计算机)
		$jsj = $this->rd('_traffic_in_94.rrd','AVERAGE');
		//获取traffic_in数据
		$jsjin = $this->unsetNan($jsj['data']['traffic_in']);
		//获取traffic_out数据
		$jsjout = $this->unsetNan($jsj['data']['traffic_out']);
		//查询数据库(主楼一层)
		$zl1c = $this->rd('_traffic_in_89.rrd','AVERAGE');
		//获取traffic_in数据
		$zl1cin = $this->unsetNan($zl1c['data']['traffic_in']);
		//获取traffic_out数据
		$zl1cout = $this->unsetNan($zl1c['data']['traffic_out']);
		//查询数据库(主楼二层东侧)
		$zl2cdc = $this->rd('_traffic_in_87.rrd','AVERAGE');
		//获取traffic_in数据
		$zl2cdcin = $this->unsetNan($zl2cdc['data']['traffic_in']);
		//获取traffic_out数据
		$zl2cdcout = $this->unsetNan($zl2cdc['data']['traffic_out']);
		//查询数据库(主楼二层西侧)
		$zl2cxc = $this->rd('_traffic_in_88.rrd','AVERAGE');
		//获取traffic_in数据
		$zl2cxcin = $this->unsetNan($zl2cxc['data']['traffic_in']);
		//获取traffic_out数据
		$zl2cxcout = $this->unsetNan($zl2cxc['data']['traffic_out']);
		//查询数据库(主楼三层东侧)
		$zl3cdc = $this->rd('_traffic_in_85.rrd','AVERAGE');
		//获取traffic_in数据
		$zl3cdcin = $this->unsetNan($zl3cdc['data']['traffic_in']);
		//获取traffic_out数据
		$zl3cdcout = $this->unsetNan($zl3cdc['data']['traffic_out']);
		//查询数据库(主楼三层西侧)
		$zl3cxc = $this->rd('_traffic_in_86.rrd','AVERAGE');
		//获取traffic_in数据
		$zl3cxcin = $this->unsetNan($zl3cxc['data']['traffic_in']);
		//获取traffic_out数据
		$zl3cxcout = $this->unsetNan($zl3cxc['data']['traffic_out']);
		//查询数据库(主楼四层东侧)
		$zl4cdc = $this->rd('_traffic_in_83.rrd','AVERAGE');
		//获取traffic_in数据
		$zl4cdcin = $this->unsetNan($zl4cdc['data']['traffic_in']);
		//获取traffic_out数据
		$zl4cdcout = $this->unsetNan($zl4cdc['data']['traffic_out']);
		//查询数据库(主楼四层西侧)
		$zl4cxc = $this->rd('_traffic_in_84.rrd','AVERAGE');
		//获取traffic_in数据
		$zl4cxcin = $this->unsetNan($zl4cxc['data']['traffic_in']);
		//获取traffic_out数据
		$zl4cxcout = $this->unsetNan($zl4cxc['data']['traffic_out']);
		//获取时间戳键
		$xAxis = array_keys($zl4cxc['data']['traffic_in']);
		$xAxis = $this->average($xAxis);
		//将时间戳转成时分格式
		foreach ($xAxis as $key => $value) {
			$xAxis[$key] = date('Y-m-d H:i',$value);
		}
		//转换单位并分段计算平均值
		$ayin = $this->unit($ayin);
		$ayin = $this->average($ayin);
		$ayout = $this->unit($ayout);
		$ayout = $this->average($ayout);
		foreach ($ayout as $key => $value) {
			$ayouts[] = '-'.$value;
		}

		$jsjin = $this->unit($jsjin);
		$jsjin = $this->average($jsjin);
		$jsjout = $this->unit($jsjout);	
		$jsjout = $this->average($jsjout);
		foreach ($jsjout as $key => $value) {
			$jsjouts[] = '-'.$value;
		}	

		$zl1cin = $this->unit($zl1cin);
		$zl1cin = $this->average($zl1cin);
		$zl1cout = $this->unit($zl1cout);
		$zl1cout = $this->average($zl1cout);
		foreach ($zl1cout as $key => $value) {
			$zl1couts[] = '-'.$value;
		}	


		$zl2cdcin = $this->unit($zl2cdcin);
		$zl2cdcin = $this->average($zl2cdcin);
		$zl2cdcout = $this->unit($zl2cdcout);
		$zl2cdcout = $this->average($zl2cdcout);
		foreach ($zl2cdcout as $key => $value) {
			$zl2cdcouts[] = '-'.$value;
		}


		$zl2cxcin = $this->unit($zl2cxcin);
		$zl2cxcin = $this->average($zl2cxcin);
		$zl2cxcout = $this->unit($zl2cxcout);
		$zl2cxcout = $this->average($zl2cxcout);
		foreach ($zl2cxcout as $key => $value) {
			$zl2cxcouts[] = '-'.$value;
		}

		$zl3cdcin = $this->unit($zl3cdcin);
		$zl3cdcin = $this->average($zl3cdcin);
		$zl3cdcout = $this->unit($zl3cdcout);
		$zl3cdcout = $this->average($zl3cdcout);
		foreach ($zl3cdcout as $key => $value) {
			$zl3cdcouts[] = '-'.$value;
		}

		$zl3cxcin = $this->unit($zl3cxcin);
		$zl3cxcin = $this->average($zl3cxcin);
		$zl3cxcout = $this->unit($zl3cxcout);
		$zl3cxcout = $this->average($zl3cxcout);
		foreach ($zl3cxcout as $key => $value) {
			$zl3cxcouts[] = '-'.$value;
		}

		$zl4cdcin = $this->unit($zl4cdcin);
		$zl4cdcin = $this->average($zl4cdcin);
		$zl4cdcout = $this->unit($zl4cdcout);
		$zl4cdcout = $this->average($zl4cdcout);
		foreach ($zl4cdcout as $key => $value) {
			$zl4cdcouts[] = '-'.$value;
		}

		$zl4cxcin = $this->unit($zl4cxcin);
		$zl4cxcin = $this->average($zl4cxcin);
		$zl4cxcout = $this->unit($zl4cxcout);
		$zl4cxcout = $this->average($zl4cxcout);
		foreach ($zl4cxcout as $key => $value) {
			$zl4cxcouts[] = '-'.$value;
		}

		//组成返回数据
		$arr = [
			'type' => ['bar','bar','bar','bar','bar','bar','bar','bar','bar','bar','bar','bar','bar','bar','bar','bar','bar','bar'],
			'data' => [$ayin,$ayouts,$jsjin,$jsjouts,$zl1cin,$zl1couts,$zl2cdcin,$zl2cdcouts,$zl2cxcin,$zl2cxcouts,$zl3cdcin,$zl3cdcouts,$zl3cxcin,$zl3cdcouts,$zl4cdcin,$zl4cdcouts,$zl4cxcin,$zl4cxcouts],
			'xAxis' => $xAxis,
			'dataName' => ["奥运流入", "奥运流出","计算机流入", "计算机流出","一层流入", "一层流出","二层东流入", "二层东流出","二层西流入", "二层西流出","三层东流入", "三成东流出","三层西流入", "三层西流出","四层东流入", "四层东流出","四层西流入", "四层西流出"],
			'yAxisLabelFormatter'=>["{value}".$this->unit],
			];

		return $arr;
	}

	//数组简化
	protected function average($arr)
	{
		//定义输出数组
		$array = [];
		//定义i
		$i = 0;
		//处理数组
		foreach($arr as $key => $value) {
			if(isset($arr[$i+11])){
				$val = $arr[$i]+$arr[$i+1]+$arr[$i+2]+$arr[$i+3]+$arr[$i+4]+$arr[$i+5]+$arr[$i+6]+$arr[$i+7]+$arr[$i+8]+$arr[$i+9]+$arr[$i+10]+$arr[$i+11];
				$array[] = floor($val/12*1000)/1000;
				$i = $i+12;
			}elseif(isset($arr[$i+10])){
				$val = $arr[$i]+$arr[$i+1]+$arr[$i+2]+$arr[$i+3]+$arr[$i+4]+$arr[$i+5]+$arr[$i+6]+$arr[$i+7]+$arr[$i+8]+$arr[$i+9]+$arr[$i+10];
				$array[] =  floor($val/12*1000)/1000;
				$i = $i+12;
			}elseif(isset($arr[$i+3])){
			}

		}
		//返回数组
		return $array;
	}


	//图形流量
	public function flow()
	{
		//获取参数
		$name = input('get.name');
		//查询数据库（国堪）
		$gk = $this->rd('10_225_1_200__traffic_in_108.rrd','AVERAGE');
		$gk = $this->unsetNan($gk['data']['traffic_in']);
		$gk = $this->last($gk);
		//查询数据库（奥运）
		$ay = $this->rd('_traffic_in_82.rrd','AVERAGE');
		$ay = $this->unsetNan($ay['data']['traffic_in']);
		$ay = $this->last($ay);
		//查询数据库（云中心）
		$yzx = $this->rd('_traffic_in_94.rrd','AVERAGE');
		$yzx = $this->unsetNan($yzx['data']['traffic_in']);
		$yzx = $this->last($yzx);
		//查询数据库（无锡）
		$wx = $this->rd('10_225_1_6_traffic_in_106.rrd','AVERAGE');
		$wx = $this->unsetNan($wx['data']['traffic_in']);
		$wx = $this->last($wx);
		//查询数据库（总部）
		$zb = $this->rd('_traffic_in_107.rrd','AVERAGE');
		$zb = $this->unsetNan($zb['data']['traffic_in']);
		$zb = $this->last($zb);
		//查询数据库（合肥）
		$hf = $this->rd('_traffic_in_107.rrd','AVERAGE');
		$hf = $this->unsetNan($hf['data']['traffic_in']);
		$hf = $this->last($hf);
		//查询数据库（互联网）
		$hlw = $this->rd('_traffic_in_101.rrd','AVERAGE');
		$hlw = $this->unsetNan($hlw['data']['traffic_in']);
		$hlw = $this->last($hlw);

		$load = mt_rand(13,32);

		$down = mt_rand(30,130);

		$str = '';
		switch ($name) {
			case 'gk':
				$str = "<pre style='color: white;font-size: 16px;'>
<p>上传速率:<span style='color: red;'>$gk/秒</span></p >
<p>下载速率:<span style='color: red;'>$down/秒</span></p >
<p>网络负载:<span style='color: red;'>$load%</span>";
				break;
			case 'ay':
				$str = "<pre style='color: white;font-size: 16px;'>
p>上传速率:<span style='color: red;'>$ay/秒</span></p >
<p>下载速率:<span style='color: red;'>$down/秒</span></p >
<p>网络负载:<span style='color: red;'>$load%</span>";
				break;
			case 'yzx':
				$str = "<pre style='color: white;font-size: 16px;'>
<p>上传速率:<span style='color: red;'>$ay/秒</span></p >
<p>下载速率:<span style='color: red;'>$down/秒</span></p >
<p>网络负载:<span style='color: red;'>$load%</span>";
				break;
			case 'wx':
				$str = "<pre style='color: white;font-size: 16px;'>
<p>上传速率:<span style='color: red;'>$ay/秒</span></p >
<p>下载速率:<span style='color: red;'>$down/秒</span></p >
<p>网络负载:<span style='color: red;'>$load%</span>";
				break;
			case 'zb':
				$str = "<pre style='color: white;font-size: 16px;'>
<p>上传速率:<span style='color: red;'>$ay/秒</span></p >
<p>下载速率:<span style='color: red;'>$down/秒</span></p >
<p>网络负载:<span style='color: red;'>$load%</span>";
				break;
			case 'hf':
				$str = "<pre style='color: white;font-size: 16px;'>
<p>上传速率:<span style='color: red;'>$ay/秒</span></p >
<p>下载速率:<span style='color: red;'>$down/秒</span></p >
<p>网络负载:<span style='color: red;'>$load%</span>";
				break;
			case 'hlw':
				$str = "<pre style='color: white;font-size: 16px;'>
<p>上传速率:<span style='color: red;'>$ay/秒</span></p >
<p>下载速率:<span style='color: red;'>$down/秒</span></p >
<p>网络负载:<span style='color: red;'>$load%</span>";
				break;
			default:
				$str = "<pre style='color: white;font-size: 16px;'>
<p>上传速率:<span style='color: red;'>$down M/秒</span></p >
<p>下载速率:<span style='color: red;'>$down M/秒</span></p >
<p>网络负载:<span style='color: red;'>$load% M/秒</span>";
				break;
		}

		return $str;


	}

	//获取数组最后一个值
	protected function last($arr)
	{
		$num = count($arr);
		// dump($arr);exit();
		$value = $arr[$num-1];
		//判断最大值，进行单位装换
		if($value > (1*1024*1024)){
			$unit = 1024*1024*8;
			$units = 'Mb';
		}else if($value > (1*1024)){
			$unit = 1024*8;
			$units = 'kb';
		}

		//进行单位转换
		$val = $value/$unit;
		//保留两位小数点
		$val = floor($val*10000)/10000;
		return $val.$units;		
	}

}
