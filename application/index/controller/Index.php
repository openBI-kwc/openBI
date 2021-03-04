<?php
namespace app\index\Controller;
use think\Db;
use think\Controller;
use think\Request;
use think\Session;
use app\base\controller\Base;
use PhpOffice\PhpSpreadsheet\IOFactory;

/**
 * @param      任意变量
 *
 * @return     变量结构 数据类型
 */

class Index extends Base 
{
	//核心组件
	public function Index()
	{
		return redirect('/openv/#/');
		// $webPath = $_SERVER['REQUEST_SCHEME'] .'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

        // if(strstr($webPath.'!!' , '/public/')) {
        //     $serverPath = $webPath;
        // }else{
        //     $serverPath = $_SERVER['REQUEST_SCHEME'] .'://'.$_SERVER['HTTP_HOST'];
        // }
        // return redirect($serverPath.'/openv');
	}
	
	//获取分类方法
	public function categain()
	{
		$uid = Db::name('token')->where('token',$this->token)->field('uid')->find();
		$category = Db::name('user')->where('uid',$uid['uid'])->field('sid')->find();	
		$cate = explode(',',$category['sid']);
		$groupdata = Db::name('screengroup')->where('sid','in',$cate)->field('sid,screenname')->select();
		
		return $groupdata;
       
	}


	public function checkToken($data)
	{
		// 忽略获取的header数据
		$ignore = array('host','accept','content-length','content-type');
		//声明headers为一个空数组
	    $headers = array();
	    //遍历去除$_server中的数据
        foreach($data as $key=>$value){
            //提取HTTP开头的键
            if(substr($key, 0, 5)==='HTTP_'){
                $key = substr($key, 5);
                $key = str_replace('_', ' ', $key);
                $key = str_replace(' ', '-', $key);
                $key = strtolower($key);

                if(!in_array($key, $ignore)){
                    $headers[$key] = $value;
                }
            }
        }

    }
	/**
	 * 添加可视化
	 *
	 * @return     成功返回{err:0;id:$id} 失败返回{err:1;id:$id} 未接收到数据{err:3;id:$id} 名称重复{err:4;id:$id} 
	 */
	public function screenInfo()
	{
		//接收post来的数
		$put = file_get_contents('php://input'); 
		//json->数组 数据类型转换
		$data = json_decode($put,1); 

       	//执行查询语句
       	if(empty($data['name'])){
       		return $dataJson['err'] = 3;
       	}
        $selectData = Db::name('screen')->where('name',$data['name'])->field('id')->find();
        //判断是否查询成功 
        if(!empty($selectData)){
			return get_status(1,'大屏已存在',2018);
		}
		if($data['tmpid'] == '-1'){
			$screenOption = json_encode($data['screenOption']);
		}else{
			$screenOption = [];
		}
		// 将大屏信息取出
		$screendata = [
			'name' => $data['name'],
			'sid' => $data['sid'],
			'screentype' => 0,
			'data' => $screenOption,
			'createtime' => time(),
			'updatetime' => time(),
			'publishuser'  =>$this->user['uid']
		];
		//执行添加语句
		$id = Db::name('screen')->insertGetId($screendata);
		//如果是默认模板这直接返回
		// if ($screendata['sid'] == 1) {
		// 	return get_status(0,['screenId' => $id]);
		// }
		
		//查询模板
		$dataTmp = Db::name('screen')->where('id' , $data['tmpid'])->field('data')->find();
		//如果未找到模板直接返回大屏ID
		if(!$dataTmp) {
			return get_status(0,['screenId' => $id]);
		}else {
			//将大屏data换成模板的data
			$screenUpdateData = Db::name('screen')->where('id',$id)->update(['data' => $dataTmp['data']]);
		}
		//查询模板相关的所有图表
		$chartTmp = Db::name('screenchart')->where('screenid', $data['tmpid'])->select();
		$chartTmpConfig = Db::name('screencharttconfig')->where('screenid', $data['tmpid'])->select();
		//判断模板下是否有图表
		if(!$chartTmp || !$chartTmpConfig) return get_status(0,['screenId' => $id]);
		Db::startTrans();
		try{
			//执行图表添加语句
			//遍历模板下的图表
			foreach ($chartTmpConfig as $k => $v) {
				foreach ($chartTmp as $key => $value) {
					if ($value['tid'] == $v['tid']) {
						unset($value['tid']);
						$value['screenid'] = $id;
						$value['createtime'] = time();
						$value['updatetime'] = time();
						// $value = array_filter($value);
						// dump($value);die;
						$tid = Db::name('screenchart')->insertGetId($value);
						$v['tid'] = $tid;
						unset($v['id']);
						$v['id'] = $tid;
						$v['screenid'] = $id;
						Db::name('screencharttconfig')->insert($v);
					}
				}
			}
			// 提交事务
			Db::commit();
			return get_status(0,['screenId' => $id]);
		} catch (\Exception $e) {
			// 回滚事务
			Db::rollback();
			return get_status(1,'模板图表添加失败',2019);
		}
	} 
	/**
	 * 获取大屏列表
	 *
	 * @return     返回err: 0 ;data:屏幕列表
	 */
	public function screenSummary()
	{
		//获取分类数据
		$groupdata = $this->categain();
		
		//将用户分组加入数组
    	for ($i=0; $i < count($groupdata); $i++) { 
    		$sid[] = $groupdata[$i]['sid'];
    	}
    	//判断该用户是否有分组
    	if(empty($sid)){
    		return ['err'=>0,'status'=>0,'data'=>[],'cate'=>$groupdata];
		}
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


        $get['sid'] = issetKey($get , 'sid' , 0);
		$get['currentPage'] = issetKey($get , 'currentPage' , 1);
		$get['pageSize'] = issetKey($get , 'pageSize' , 20);


        if ($get['sid'] == 0) {
			$data = Db::name('screen')->where('sid','in',$sid)
									  ->where('name' , 'like' , "%".$get['searchword']."%")
									  ->where('screentype',0)
									  ->field('id,name,imgdata,lock,publish,password,screentype,publishuser,createtime,updatetime')
                                      ->page($get['currentPage'] ,$get['pageSize'] )
									  ->order($get['order'].' DESC')
									  ->select();
			$total = Db::name('screen')->where('sid','in',$sid)
                                        ->where('name' , 'like' , "%".$get['searchword']."%")
                                        ->where('screentype',0)
                                        ->field('id,name,imgdata,lock,publish,password,screentype,publishuser,createtime,updatetime')
                                        ->order($get['order'].' DESC')
                                        ->count();
        }else{
			$data = Db::name('screen')->where('sid',$get['sid'])
									  ->where('name' , 'like' , "%".$get['searchword']."%")
									  ->where('screentype',0)
									  ->field('id,name,imgdata,lock,publish,password,screentype,publishuser,createtime,updatetime')
                                      ->page($get['currentPage'] ,$get['pageSize'] )
									  ->order($get['order'].' DESC')
									  ->select();
            $total = Db::name('screen')->where('sid',$get['sid'])
                                        ->where('name' , 'like' , "%".$get['searchword']."%")
                                        ->where('screentype',0)
                                        ->field('id,name,imgdata,lock,publish,password,screentype,publishuser,createtime,updatetime')
                                        ->order($get['order'].' DESC')
                                        ->count();
									
		}

		//遍历data
		foreach($data as $key => $value) {
            //判断当前用户名是否与发布用户名一致
            if($this->user['uid'] == $value['publishuser']) {
                $data[$key]['publishuser'] = 1;
            }else {
                $data[$key]['publishuser'] = 0;
            }
            $data[$key]['createtime'] = date("Y-m-d H:i:s" , $value['createtime']);
            $data[$key]['updatetime'] = date("Y-m-d H:i:s" , $value['updatetime']);
		}

		$datas = [
		    'list' => $data,
            'cate' => $groupdata,
            'total' => $total,
        ];

	    //返回数据
		return  ['err'=>0,'status'=>0,'data'=>$datas,];
	}




	/**
     * 通过token 获取用户名
     */
    public function getUserName(){
        //获取当前的token
        $header = get_all_header();
        //判断token是否存在
        if(!isset($header['token'])){
            //没有token返回NoUser
            return 'NoUser';
        }else{
            //将token加入到变量
            $token = $header['token'];
        }
        //通过token查询用户id 
        $uid = Db::name('token')->where('token',$token)->field('uid')->find();
        //判断查询是否成功
        if(!$uid){
            //uid
            return 'NoUser';
        }
        //通过uid获取用户信息
        $user = Db::name('user')->where('uid' , $uid['uid'])->field('username')->find();
        //判断查询是否成功
        if(!$user){
            //user
            return 'NoUser';
        }
        //返回user
        return $user['username'];
    }


	/**
	 * 获取单个大屏信息
	 */
	public function singlescreenSummary()
	{
		//接收get数据
		$get = input('get.');
		if(!$get) {
			return get_status(1,'接收数据失败',2000);
		}
		//判断是否是查询发布的历史快照
		if(isset($get['sharetype'])) {
			//获取发布信息查看是否是历史快照发布
			$publish = Db::name('publish')->where('scid',$get['id'])->find();
			//将发布浏览次数+1
			Db::name('publish')->where('scid',$get['id'])->update(['viewsnum' => $publish['viewsnum']+1]);
			//判断是否是历史快照发布
			$get['id'] = $this->getpublishId($get['id']);	
		}
        //判断大屏是否已发布
        if(isset($get['id']['err'])) {
            return $get['id'];
        }
		//查询指定大屏信息
		$data = Db::name("screen")->where('id',$get['id'])->find();
		if ($data) {
			return get_status(0,$data);
		}else {
			return get_status(1,'大屏不存在或已被删除',2020);
		}
	}
	/**
	 * 获取发布大屏快照ID
	 */
	public function getpublishId($id)
	{
		//取出大屏的信息
		$data = Db::name("screen")->where('id',$id)->find();
		//判断是否发布
		if(!$data['publish']) {
			return get_status(1,'大屏未发布',2026);
		}
		//获取发布信息查看是否是历史快照发布
		$publish = Db::name('publish')->where('scid',$id)->find();
		//将发布浏览次数+1
		Db::name('publish')->where('scid',$id)->update(['viewsnum' => $publish['viewsnum']+1]);
		//判断是否是历史快照发布
		if($publish['ptype'] == 2) {
			//如果是历史快照发布则使用历史快照ID
			return $publish['shid'];
		}else {
			return $id;
		}

	}

	/**
	 * 复制大屏数据
	 *
	 * @return     返回err: 0 ;data:屏幕列表
	 */
	public function copyScreen()
	{
		$put = file_get_contents('php://input'); 
		//json->数组 数据类型转换
		$post = json_decode($put,1); 
		if(!isset($post)){
			return get_status(1,null,NULL);
		}
		$id = $post['id'];
		//执行查询语句
		$data = Db::name('screen')->where('id',$id)->find();
		//销毁查询出来的id防止id重复
		unset($data['id']);
        //------预留 模板名称替换位置
        $name = $data['name'];
        $data['name'] = $name.'副本';

		for ($i = 1 ; $i < 4 ; $i++) {
		    if ( $i == 1) {
                $data['name'] = $name.'副本';
            }else  {
                $data['name'] = $name.'副本' .$i;
            }
//            查询大屏名字是否存在
            $result = Db::name("screen")->where("name" , $data['name'])->select();
            if(!$result) {
                $data['createtime'] = time();
                $data['updatetime'] = time();
                $data['publish'] = 0;
                //插入新的大屏信息
                Db::name('screen')->insert($data);
                //获取大屏自增ID
                $screenid = Db::name('screen')->getLastInsID();
                break;
            }

        }
        if(isset($screenid)) {
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
            //查询可视化大屏列表
            $select =  Db::name('screen')->field('id,name,imgdata,lock')->select();
            //返回大屏列表
            return get_status(0,$select);
        }else {
            return get_status(1,"大屏复制上限" , 2044);
        }


	}

	/**
	 * 获取大屏数据
	 *
	 * @return     成功返回{err:0;data:大屏数据} 失败返回{err:1;data:null}
	 */
	public function getScreenInfo()
	{
		//接收post来的数
		$id = input('get.');
		
		if(isset($id['sharetype'])) {
			$id['id'] = $this->getpublishId($id['id']);
		}
		//判断大屏是否已发布
		if(isset($id['id']['err'])) {
		    return $id['id'];
        }

		//查询大屏信息
		$screendata = Db::name('screen')->where('id',$id['id'])->field('lock,password,data')->find();
		if(!$screendata) {
			return  get_status(1,"大屏不存在",2020);
		}
		//判断是否锁定且密码是否正确
		if(isset($id['password'])) {
			if($id['password'] != $screendata['password']) {
				return  get_status(1,'密码错误' , 2021);
			}
		}
		//查询相关大屏下的config
		$screenchart = Db::name('screenchart')->where(['screenid'=>$id['id']])->field('tid,tname')->order('position DESC')->select();
		// dump($screenchart);die;
		//大屏所有的图表
		$data['position'] = [];
		//大屏图表对应位置
		$data['layer'] = [];
		//查询相关大屏下的config
		$tconfig = Db::name('screencharttconfig')->where(['screenid'=>$id['id']])->field('id,chartSourceType,screenid,selectDaid',true)->where('parenttid','null')->select();
		//遍历图表，将图表id加入数组
		foreach($screenchart as $kk=>$vv){
			foreach($tconfig as $key=>&$val){
				if($vv['tid'] == $val['tid']){
					//将状态值改为bool
					$val['ishide'] = (bool)$val['ishide'];
					$val['islock'] = (bool)$val['islock'];
					$val['resizable'] = (bool)$val['resizable'];
					$val['showBorder'] = (bool)$val['showBorder'];
					$val['collection'] = (bool)$val['collection'];
					$val['chartData']=json_decode($val['chartData'],1);
					$val['dataOpt']=json_decode($val['dataOpt'],1);
					$val['incident']=json_decode($val['incident'],1);
					if(!empty($val['drilling'])){
						$val['drilling'] = json_decode($val['drilling'],1);
					}
					$data['position'][$val['key']] = $val;
					$data['layer'][] = $vv['tname'];
				}  
		}
	}
	// dump($screendata['data']);die;
		//返回数据
		$data['screenOption'] = json_decode($screendata['data'],1);
	
		return  get_status(0,$data);
	}

	/**
	 * 获取数组中没有的键
	 */
	protected function alias($array , $key)
	{
		if(!isset($array[$key])) {
			return $key;
		}else {
			$keys = $key.mt_rand(0,99);
			return $this->alias($array , $keys);
		}
	}

	/**
	 * 更改大屏名称
	 *
	 * @return     成功返回err:0  失败返回err:1
	 * 
	 */
	public  function updateScreenName()
	{
		//接收post来的数据
		$put=file_get_contents('php://input'); 
		//json->数组 数据类型转换
		$id =json_decode($put,1);
		//判断名称为空
		if(empty(trim($id['name']))){
			return get_status(1,'大屏名称不能为空',2029);
		} 
		//查询语句
		$name['name'] = $id['name'];
		$name['updatetime'] = time();
        $da = Db::name('screen')->where(['name'=>$id['name']])->find();
        if($da){
            return get_status(1,'大屏名称已重复',2003);
        }
		$data = Db::name('screen')->where('id','=',$id['id'])->update($name);
		$da = Db::name('publish')->where('scid','=',$id['id'])->update(['sname'=>$name['name']]);
		//判断是否查询成功
		if($data == null){
			$ifdata = 1;
		}else{
			$ifdata = 0; 
		}
		//定义返回数据
		$returndata['err'] = $ifdata;
		//返回数据
		return  get_status($ifdata,null,null);

	}

	/**
	 * 更改大屏封面
	 *
	 * @return    成功返回err:0  失败返回err:1 \
	 */
	public function updateScreenCover()
	{
		//接收post来的数据
		$post = input('post.');
		//执行上传
		$file = request()->file('imgdata');

        if(empty($file)){
           return  get_status(1,NULL,NULL);
        }
		//定义上传路径 
		$info = $file->move(ROOT_PATH . 'public' . DS . 'uploads', date('Ymd').'/'.md5(time()).'.png');
		//获取路径
		$imgSrcWin = '/uploads/'.$info->getSaveName();
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
			$imgSrcWin = './Cover/' . md5($info->getSaveName()) . '_image.jpg';
			//缩略并保存图片
			$image->thumb($scaleWidth, $scaleHeight)->save($imgSrcWin);
		}
		
		
		//base64
		//接收base64图片
		// $input = input('post.imgdata');
		// //处理图片,获取路径
		// $imgSrcWin = base64Image($input);
		// if(!$imgSrcWin) {
		// 	return get_status(1,null,null);
		// }
		

		

		//设置存储路径
		$imgdata['imgdata'] = trim($imgSrcWin,'.');
		$imgdata['updatetime'] = time();
		
		

		
		//设置更改语句
		$data = Db::name('screen')->where('id',$post['id'])->update($imgdata);
		//判断是否更改成功
		if($data == null){
			$ifdata = 1;
		}else{
			$ifdata = 0;
		}
	
		//返回数据
		return  get_status($ifdata,$imgdata['imgdata'],null);

	}
	
	/**
	 * 更改大屏数据
	 *
	 * @return    成功返回err:0  失败返回err:1 未做更改返回err:2 
	 */
	public function updateScreenInfo()
	{
		//接收post来的数
		$put=file_get_contents('php://input'); 

		if(empty($put)){
			return $data['err'] = 1;
		}
		$put = json_decode($put,1);
		// dump(json_encode($put['screenOpt']));die;
		//判断$put['screenOpt']是否是一个json
		//检查数据是否有修改
		$vali = Db::name('screen')->where(['id' => $put['sid'], 'data' => json_encode($put['screenOpt'])])->find();
		if($vali) {
			return get_status(0,'修改成功');
		}
		//转成数组
		// $config = json_decode($put['screenOpt'],1);
		$config = $put['screenOpt'];
		//求最小公倍数
		$ojld =  $this->ojld($config['width'],$config['height']);
		//求出屏幕比例
		$ratio = $config['width']/$ojld.':'.$config['height']/$ojld;
		//将配置加入到data数组中
		$data['pixel'] = $config['width'].' X '.$config['height'];//屏幕大小
		$data['ratio'] = $ratio;//屏幕比例
		$data['data'] = json_encode($put['screenOpt']);//配置
		//修改数据
		$update = Db::name('screen')->where('id',$put['sid'])->update($data);
		if(!$update) {
			return get_status(1,'修改失败',2022);
		}else {
			return get_status(0,'修改成功');
		}
	}

	//最小公倍数
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
	 * 删除大屏数据
	 *
	 * @return     删除成功返回{err:0;data:大屏列表} 删除失败返回{err:1;data:大屏列表}
	 */
	public function deleteScreen()
	{
		//接收post来的数据
		$put = file_get_contents('php://input');
		//json数据->php数组转换
		$id = json_decode($put,1);
		//检查大屏是否发布
		$vali = Db::name('screen')->where('id','=',$id['id'])->find();
		if($vali['publish']){
			return  get_status(1,'删除失败,大屏已发布',2024);
		}
		//查询大屏是否用于可视化矩阵
		$valis = Db::name('screendir')->where('screenid','like','%,'.$id['id'].',%')->find();
		if($valis) {
			return  get_status(1,'删除失败,大屏存在于可视化矩阵',2025);
		}
		//执行删除语句
		$data = Db::name('screen')->where('id','=',$id['id'])->delete();
		//删除大屏相关图表
		$data = Db::name('screenchart')->where('screenid','=',$id['id'])->delete();
		//删除大屏发布
		$selectchart = Db::name('publish')->where('sid','=',$id['id'])->delete();
		//删除tconfig
		$deltconfig = Db::name('screencharttconfig')->where('screenid',$id['id'])->delete();
		//执行查询语句
		// $screenSummary = Db::name('screen')->where('id','>','0')->where('screentype',0)->field('id,name,imgdata')->select();
		//判断是否删除成功 
		if($data){
			// return  get_status(0,$screenSummary);
			return  get_status(0,'删除成功');
		}else{
			//查询是否还有与大屏相关的图表
			// $selectchart = Db::name('screenchart')->where('screenid','=',$id['id'])->select();
			// if($selectchart) {
			// 	return  get_status(1,$screenSummary);
			// }else{
			// 	return  get_status(0,$screenSummary);
			// }
			return  get_status(0,'删除失败' , 4008);
		}
	}


   	/**
   	 * 上传背景图片接口 
   	 *
   	 * @return    成功返回{err:0;data:列表} 失败返回{err:1;data:列表}
   	 */
   	public function uploadBackground()
    {
    	
    	
    	//实现上传
       	$file = request()->file('image');
       	//定义上传路径
		$info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');	
		//获取路径
		$imgSrcWin = '\uploads\\'.$info->getSaveName();
		//执行数据添加语句
		$insertImg['src'] = $imgSrcWin;
		$insert = Db::name('backgroundimg')->insert($insertImg);
		//查询背景图片列表
		$background = Db::name('backgroundimg')->where('id','>','0')->select();
		//定义返回json变量
		$data['err'] = 0;
		$data['data'] = $background;
		//将路径中的\更换成/
		$data['imgUrl'] = str_replace('\\','/',$imgSrcWin);
		//判断是否添加成功		
		if(!$insert){
			return $data['err'] = 1;
		}
		//返回json
		return $data;
	}
	
	/**
	 * 获取背景图片列表 
	 *
	 * @return     成功返回{err:0;data:列表} 失败返回{err:1;data:null}
	 */
	public function getBackground()
	{
		
		
		//查询背景图片列表
		$background = Db::name('backgroundimg')->where('id','>','0')->select();
		if($background == null){
			return  get_status(1,null,null);
		}
		//返回查询结果
		return  get_status(0,null,$background);
	}	

	/**
	 * 删除背景 图片接口
	 *
	 * @return     成功返回{err:0;data:列表} 失败返回{err:1;data:列表}
	 */
	public function deleteBackground()
	{
		
		
		//接收post来的数据
		$put = file_get_contents('php://input');
		//json->数组 数据类型转换
		$id =json_decode($put,1); 
		//执行删除语句
		$delete = Db::name('backgroundimg')->where('id',$id['id'])->delete();
		//执行查找北京背景图片列表语句
		$background = Db::name('backgroundimg')->where('id','>','0')->select();
		//判断是否删除成功 杀出成功返回屏幕列表
		if($delete){
			return  get_status(0,$background);
		}else{
			return  get_status(1,$background);
		}
		

	}	
	

	/**
	 * 上传屏幕组件
	 *
	 * @return     成功返回{err:0;imgUrl:上传的图片路径；groupimg:上传组件列表} 成功返回{err:1;imgUrl:null；groupimg:上传组件列表}
	 */
	public function uploadGroup()
    {
    	
    	
    	//执行上传
       	$file = request()->file('group');
       	//var_dump($file);exit;
       	//定义上传路径
		$info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');	
		//获取组件路径
		$imgSrcWin = '\uploads\\'.$info->getSaveName();
		//获取上传来的图片宽和高
		list($width, $height)=getimagesize(ROOT_PATH . 'public' . DS . 'uploads' . DS . $info->getSaveName());
		//win化组件路径
		$data['imgUrl'] = str_replace('\\','/',$imgSrcWin);
		//定义添加数据
		$insertImg['src'] = $imgSrcWin;
		$insertImg['width'] = $width;
		$insertImg['height'] = $height;

		//执行添加语句
		$insert = Db::name('groupimg')->insert($insertImg);	
		//执行查询语句	
		$groupimg = Db::name('groupimg')->where('id','>','0')->select();
		//包装返回数据
		$data['groupimg'] = $groupimg;
		$data['err'] = 0;
		//判断是否添加成功
		if(!$insert){
			return $data['err'] = 1;
		}
		//返回数据
		return $data;
	}
	
	/**
	 * 删除自定义组件（图片）
	 *
	 * @return     成功返回{err:0;groupimg:组件列表} 成功失败{err:1;groupimg:组件列表}
	 */
	public function  deleteGroup()
	{
		
		
		//接收post来的信息
		$put = file_get_contents('php://input');
		//json->php数组格式
		$id = json_decode($put,1);
		//执行删除语句
		$data = Db::name('groupimg')->where('id','=',$id['id'])->delete();
		//返回组件列表
		$groupimg = Db::name('groupimg')->where('id','>','0')->select();
		//判断是否删除成功
		if($data == 0){
			$deleteRetuen['err'] = 1;
		}else{
			$deleteRetuen['err'] = 0;
		}
		//定义返回数据格式
		$deleteRetuen['groupimg'] = $groupimg;
		//返回数据
		return $deleteRetuen;
	}
	
	/**
	 * 获取组件列表
	 *
	 * @return     返回{err:0;GroupSummary:组件列表}
	 */
	public function GroupSummary()
	{
		
		//执行查询语句
		$data = Db::name('groupimg')->where('id','>','0')->select();
		if(empty($data)){
			$returndata['err'] = 1;
		}else{
			$returndata['err'] = 0;
		}
		//定义返回数据格式
		$returndata['GroupSummary'] = $data;
		//返回数据
		return  $returndata;
	}






	/**
	*建立大屏组
	*
	*
	*/

	public function screenDir()
	{
		//接收post来的数据
		$put=file_get_contents('php://input'); 
		//json->数组 数据类型转换
		//$put = '{"name":"sss","sid":24}'
		$post =json_decode($put,1); 
        //$post['sid'] = 24;
		if(empty($post['name'])){
			return get_status(1,'大屏名称不可以为空',2029);
		}
		$post['createtime'] = time();
        $da = Db::name('screendir')->where(['name'=>$post['name']])->find();
        if($da){
            return get_status(1,'大屏名称已重复',2003);
        }
        //添加数据并返回主键id
		$insert = Db::name('screendir')->insertGetId($post);
        $data['id'] = $insert;
		if(empty($insert)){
			return get_status(1,$data);
		}else{
			return get_status(0,$data);

		}

	}

	/**
	 * 保存屏幕组列表
	 *
	 * @return     返回err: 0 ;data:屏幕列表
	 */
	public function updateScreenDir()
	{
		//接收post来的数据
		$put=file_get_contents('php://input'); 
		//json->数组 数据类型转换
		$post =json_decode($put,1); 

		if(!isset($post['id'])){
			return get_status(3,NULL,NULL);
		}
		$update['data'] = $post['data'];
		//将$data转成数组
		$data = json_decode($post['data'] , 1);
		//声明ID的存储数组
		$id = [];
		//将data中的大屏ID取出
		foreach($data['screenList'] as $value) {
			//判断$value中是否有ID,防止出现空白大屏
			if(isset($value['id'])) {
				$id[] = $value['id'];
			}
		}
		//判断ID是否为空
		if(empty($id)) {
			$update['screenid'] = '';
		}else{
			$update['screenid'] = ','.implode(',' , $id).',';
		}
		$update['updatetime'] = time();
		$data = Db::name('screendir')->where('id',$post['id'])->update($update);

		if(empty($data)){
			return get_status(1,NULL);
		}else{
			return get_status(0,NULL);

		}

	}

	/**
	 * 获取大屏组数据
	 *
	 * @return     成功返回{err:0;data:大屏数据} 失败返回{err:1;data:null}
	 */
	public function getScreenDirInfo()
	{
		//接收post来的数
		$put=file_get_contents('php://input'); 
		//json->数组 数据类型转换
		$post =json_decode($put,1); 
		if(empty($post)){
			return get_status(3,NULL,NULL);
		}
		//执行查询语句
		$data['dir'] = Db::name('screendir')->where('id','=',$post['id'])->field('data,name')->find();
		$data['screenSummary'] = Db::name('screen')->field('id,imgdata,name')->select();
		//判断查询结果是否为空
		if($data == null){
			return get_status(1,$data);
		}else{
			return get_status(0,$data);
		}
	}
	/**
	 *   根据大屏组返回屏幕数据
	 *
	 * @return     成功返回{err:0;data:大屏数据} 失败返回{err:1;data:null}
	 */

	public function getDirScreenSummary()
	{
		//接收post来的数
		$put=file_get_contents('php://input'); 
		//json->数组 数据类型转换
		$post =json_decode($put,1); 
		if(empty($post)){
			return get_status(3,NULL,NULL);
		}
		for($i = 0;$i <count($post['id']);$i++){
			$select = Db::name('screen')->where('id','=',$post['id'][$i])->field('data')->find();

			$data[$post['id'][$i]] = $select;
		}

		return get_status(0,$data);

	}


	/**
	 * 获取屏幕组列表
	 *
	 * @return     返回err: 0 ;data:屏幕列表
	 */
	public function screenDirSummary()
	{
		$groupdata = $this->categain();
		for ($i=0; $i < count($groupdata); $i++) { 
    		$sid[] = $groupdata[$i]['sid'];
    	}
    	if(empty($sid)) {
    		return  ['err'=>0,'status'=>0,'data'=>[],'cate'=>$groupdata];
    	}
		//接收post来的数
		$get =input('get.'); 
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

        $get['sid'] = issetKey($get , 'sid' , 0);
        $get['currentPage'] =issetKey($get , 'currentPage' , 1);
        $get['pageSize'] = issetKey($get , 'pageSize' , 20);

		//执行查询语句
		if($get['sid'] == 0){
			$data = Db::name('screendir')->where('sid','in',$sid)
						->where('name' , 'like' , "%".$get['searchword']."%")
						->order($get['order'].' DESC')
                        ->page($get['currentPage'], $get['pageSize'])
						->select();
			$total = Db::name('screendir')->where('sid','in',$sid)
                        ->where('name' , 'like' , "%".$get['searchword']."%")
                        ->count();
		}else{
			$data = Db::name('screendir')->where('sid',$get['sid'])
					->where('name' , 'like' , "%".$get['searchword']."%")
					->order($get['order'].' DESC')
                    ->page($get['currentPage'], $get['pageSize'])
                    ->select();
            $total = Db::name('screendir')->where('sid',$get['sid'])
                    ->where('name' , 'like' , "%".$get['searchword']."%")
                    ->count();
		}

		$datas = [
		    "list" => $data,
            "total" => $total,
            'cate'=>$groupdata
        ];
		return  ['err'=>0,'status'=>0,'data'=>$datas,];
		//return  ['err'=>0,'status'=>NULL,'data'=>$data,'cate'=>$groupdata];
	}

	/**
	 * 删除屏幕组
	 *
	 * @return     删除成功返回{err:0;data:大屏列表} 删除失败返回{err:1;data:大屏列表}
	 */
	public function deleteScreenDir()
	{
		
		
		//接收post来的数据
		$put = file_get_contents('php://input');
		//json数据->php数组转换
		$id = json_decode($put,1);
		//执行删除语句
		$data = Db::name('screendir')->where('id','=',$id['id'])->delete();
		//判断是否删除成功 
		$result = Db::name('screendir')->select();
		return get_status(0,$result);
	
		
	}

	/**
	 * 复制页面组
	 *
	 * @return     删除成功返回{err:0;data:大屏列表} 删除失败返回{err:1;data:大屏列表}
	 */
	public function copyScreenDir()
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
		$data = Db::name('screendir')->where('id',$id)->find();
		unset($data['id']);
		
		//$data = unset($DbData['id']);

		$insert =  Db::name('screendir')->insert($data);
		$select =  Db::name('screendir')->select();
		$return = get_status(0,$select);
    	return $return;
		
	}

	/**
	 * 更改屏幕组名称
	 *
	 * @return     成功返回err:0  失败返回err:1
	 * 
	 */
	public  function updateScreenDirName()
	{
		
		//接收post来的数据
		$put=file_get_contents('php://input'); 
		//json->数组 数据类型转换
		$post =json_decode($put,1); 
		//检测名称不为空
		if(empty($post['name'])){
			return get_status(1,'大屏名称不可以为空',2029);
		}
		//判断是否接收到数据
		if(empty($post['id']) && empty($post['name'])){
			return get_status(3,NULL,NULL);
		}
		//查询语句
		$name['name'] = $post['name'];
        $da = Db::name('screendir')->where(['name'=>$post['name']])->find();
        if($da){
            return get_status(1,'大屏名称已重复',2003);
        }
		$data = Db::name('screendir')->where('id','=',$post['id'])->update($name);
		//判断是否查询成功
		if($data == null){
			return get_status(1,NULL);
		}else{
			return get_status(0,NULL);
		}
	}	


	//执行图表自动刷新
    public function chartScreenAuto()
    {

	}
	
	//保存为模版
	public function savetmp()
	{
		$post = input('post.');

		if(!isset($post)){
			return get_status(1,'数据接收失败',2000);
		}
		$id = $post['screenid'];

		//查询该大屏是否已经存在模板
		$data = Db::name('screen')->where('screen_sid',$id)->find();
		
		if($data){
			Db::name('screen')->where('id',$data['id'])->delete();
			Db::name('screenchart')->where('screenid',$data['id'])->delete();
			Db::name('screencharttconfig')->where('screenid',$data['id'])->delete();
		}
		//执行查询语句
		$data = Db::name('screen')->where('id',$id)->find();
		unset($data['id']);
		$data['createtime'] = time();
		$data['updatetime'] = time();
		$data['publish'] = 0;
		$data['password'] = '';
		$data['screentype'] = 2;
		if(!isset($data['tempcate'])){
			$data['tempcate'] = 1;
			$data['share'] = 0;
		}
		$data['tmp'] = 0;
		$data['screen_sid'] = $id;

		//插入新的大屏信息
		Db::name('screen')->insert($data);
		//获取大屏自增ID
		$screenid = Db::name('screen')->getLastInsID();

		if(!$screenid){
			return get_status(1,"添加模版失败" , 7001);
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
		return get_status(0,'添加模板成功');

	}


}


