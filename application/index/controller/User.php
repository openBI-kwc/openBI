<?php
namespace app\index\Controller;
use think\Db;
use think\Controller;
use think\Request;
use think\Session;
use app\index\model\User as UserModel;

/**
 * token验证
 * 
 *
 * @param      全局数组变量$_SERVER
 *
 * @return     token是否合法
 */
function check_token($data,$username){
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
    if(isset($headers['token'])){
    	$time = time();
    	if($username == 'tryout'){
    		$db = Db::name('tryoutuser')->where('username',$username )->find();

	    	if($headers['token'] != $db['token']){
	    		$data = get_status('1','2037',NULL);
	    		return $data;
	    	}else{
	    		return $headers['token'];
	    	}
    	}else{
	    	
	    	$db = Db::name('user')->where('username',$username )->find();

	    	if($headers['token'] != $db['token']){
	    		$data = get_status('1','2037',NULL);
	    		return $data;
	    	}else{
	    		$data = get_status('0','0',NULL);
	    		return $data;
	    	}
    	}
    }else{
    	$data = get_status('1','2037',NULL);
	    return $data;
    }
}


/**
 * GenerateToken
 *
 * @param      长度
 *
 * @return     token (32位字符串乱码)
 */
function GenerateToken($length){ 
	$str = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';//62个字符 
	$strlen = 62; 
	while($length > $strlen){ 
		$str .= $str; 
		$strlen += 62; 
	} 
	$str = str_shuffle($str); 
	return substr($str,0,$length); 
} 

class User extends Controller 
{ 
	//userModel
	protected $userModel;
	//token
	protected $token;
	//核心组件
	public function Index()
	{

		$a = serverToken($_SERVER);

		return $a;
	}
	
	public function checkToken($data,$username){
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
    if(isset($headers['token'])){
    	$time = time();
    	if($username == 'tryout'){
    		$db = Db::name('tryoutuser')->where('username',$username )->find();

	    	if($headers['token'] != $db['token']){
	    		$data = get_status('1','2037',NULL);
	    		return $data;
	    	}else{
	    		return $headers['token'];
	    	}
    	}else{
	    	
	    	$db = Db::name('user')->where('username',$username )->find();

	    	if($headers['token'] != $db['token']){
	    		$data = get_status('1','2037',NULL);
	    		return $data;
	    	}else{
	    		$data = get_status('0','0',NULL);
	    		return $data;
	    	}
    	}
    }else{
    	return 'token不合法';
    }
}

	/**
	 * 测试登录用户帐号重定向接口
	 *
	 * @return     状态吗
	 */
	public function check()
	{	
		//post接收来的数据
		$dataPost = file_get_contents('php://input'); 
		$type =json_decode($dataPost,1); 

		$username = $type['username'];
		$timeHTML = $type['time'];
		//获取服务器时间
		$timePHP = time();
		//计算时间差
		$timeDifference =  $timePHP - $timeHTML;

		//判断是否为使用用户
		if($username == 'tryout'){
			//判断是时间是否异常
			if(abs($timeDifference) >= 300){
				$data['err'] = '1';
				$data['status'] = '2033';
				return $data;
			}else{
				//查询数据库
				$selectTryOut = Db::name('tryoutuser')->where('username',$username)->find();
				//判断数据库是否存在使用用户
				if(empty($selectTryOut)){
					//没有注册 重定向至试用用户注册接口
					$data['err'] = '0';
					$data['status'] = '2030';
					return $data;
				}else{
					//取出过期时间
					$timeDB = $selectTryOut['deadline'];
					//判断是否已经过期
					if($timePHP > $timeDB){
						//过期返回已经过期
						$data['err'] = '1';
						$data['status'] = '2034';
						return $data;
					}else{
						//没过期 返回可重定向至试用登录接口
						$data['err'] = '0';
						$data['status'] = '2032';
						return $data;
					}
				}
			}
		}else{
			//非试用用户 返回普通登录接口
			$data['err'] = '0';
            $data['status'] = '2031';
            return $data;
		}



		//return $this->fetch();
	}
	
	/**
	 * 试用用户注册
	 *
	 * @return    过期时间  token 状态码
	 */
	public function tryoutRegister()
	{
		$dataPost = file_get_contents('php://input'); 
		$type =json_decode($dataPost,1); 
		$username = 'tryout';
		$timeHTML = time();
    	$timePHP = time();
    	$timeDifference =  $timePHP - $timeHTML;
    	$tryoutDB = Db::name('tryoutuser')->where('username',$username)->find();
    	if($username != 'tryout'){
    		$return['err'] = '1';
    		$return['status'] = '2031';
    		return $return;
    	}
    	//数据库中是否已存在tryout账号（如果有则证明已经试用过了），返回失败
    	if(!empty($tryoutDB)){
    		$return['err'] = '1';
    		$return['status'] = '2034';
    		return $return;

    	}
    	//判断前端发来数据中的 time 字段的时间是否与服务器时间相差过大(比如1小时)，过大则返回时钟不对错误
    	if(abs($timeDifference) >= 300){
    		$return['err'] = '1';
    		$return['status'] = '2033';
    		return $return;
    	}
    	//生成token
    	$token = md5(sha1(md5(GenerateToken(32))));
    	//设置添加语句
    	$insert['username'] = $username;
    	//传入token
    	$insert['token'] = $token;
    	//添加token过期时间
    	$insert['deadline'] = time() + 2592000;
    	//执行添加语句
    	$Db = Db::name('tryoutuser')->insert($insert);

    	if(empty($Db)){
    		//添加失败
    		$log = Db::name('log')->insert(['username'=>$username,'time'=>date('Y-m-d H:i:s'),'operation'=>'试用用户注册','state'=>'失败']);
    		return $data['err'] = 1;
    	}else{
    		//设置返回数据内容
    		$data['err'] = 0;
    		$data['deadline'] = $insert['deadline'];
    		$data['token'] = $token;
    		$log = Db::name('log')->insert(['username'=>$username,'time'=>date('Y-m-d H:i:s'),'operation'=>'试用用户注册','state'=>'成功']);
    		return $data;
    	}


	}	
	/**
	 * 试用用户登录
	 *
	 * @return     integer|string  ( description_of_the_return_value )
	 */
	public function checkTryoutLogin()
    {
    	//接收数据
        $dataPost = file_get_contents('php://input'); 
        //转化数据格式
        $type =json_decode($dataPost,1); 
        //提取用户名
        $username = $type['username'];
        //提取token
        $tokenPost = $type['token'];
        //提取前端时间
        $timeHTML = $type['time'];
        //获取服务器时间
        $timePHP = time();
        //计算时间差集
        $timeDifference = $timePHP - $timeHTML;
        //判断用户种类
        if($username != 'tryout'){
            //非试用用户 返回普通登录接口
            $return['err'] = '1';
    		$return['status'] = '2031';
    		return $return;
        }
        //判断时间是否异常
        if(abs($timeDifference) >= 300){
            $data['err'] = '1';
            $data['status'] = '2033';
            $log = Db::name('log')->insert(['username'=>$username,'time'=>date('Y-m-d H:i:s'),'operation'=>'试用用户登录_时间异常','state'=>'失败']);
            return $data;
        }
        //判断试用时间是否已经结束
        if(abs($timeDifference) >= 300){
            $data['err'] = '1';
            $data['status'] = '2033';
            $log = Db::name('log')->insert(['username'=>$username,'time'=>date('Y-m-d H:i:s'),'operation'=>'试用用户登录_时间异常','state'=>'失败']);
            return $data;
        }
        //判断是否接收到了token;
        if(empty($tokenPost)){
        	//制作token
            $updateData['token'] = md5(sha1(md5(GenerateToken(32))));
            //更改token
            $update = Db::name('tryoutuser')->where('username',$username)->update($updateData);
            $tryoutDB = Db::name('tryoutuser')->where('username',$username)->find();
            $tryoutDB['err']=0;

            Session::set('username',$tryoutDB['username']);
            Session::set('token',$tryoutDB['token']);
            $log = Db::name('log')->insert(['username'=>$username,'time'=>date('Y-m-d H:i:s'),'operation'=>'试用用户登录','state'=>'成功']);
            //返回数据
            return $tryoutDB;
        }else{
            //数据库查询
            $tryoutDB = Db::name('tryoutuser')->where('username',$username)->find();
            //判断post来的token是否与数据库token相同
            if($tokenPost != $tryoutDB['token']){
            	$log = Db::name('log')->insert(['username'=>$username,'time'=>date('Y-m-d H:i:s'),'operation'=>'试用用户登录_token不合法','state'=>'失败']);
                return '登录失败，token不正确。';
            }
            //计算token是否已经过期
            $deadlineTimeDifference = $timePHP - $tryoutDB['deadline'];
            if($deadlineTimeDifference >= 2592000){
            	$log = Db::name('log')->insert(['username'=>$username,'time'=>date('Y-m-d H:i:s'),'operation'=>'试用用户登录_token过期','state'=>'失败']);
                return 'token已经过期';
            }
            //错误码
            $tryoutDB['err']=0;
            Session::set('username',$tryoutDB['username']);
            Session::set('token',$tryoutDB['token']);
            $log = Db::name('log')->insert(['username'=>$username,'time'=>date('Y-m-d H:i:s'),'operation'=>'试用用户登录','state'=>'成功']);
            //返回数据
            return $tryoutDB;
        }
   

    }    

	/**
	 * 普通用户登录
	 *
	 * @return     用户名  错误码 token 权限 token 过期时间
	 */
	public function checkLogin()
    {	
    	

    	//接收数据
    	$dataPost = file_get_contents('php://input'); 
    	//转换格式
        $type =json_decode($dataPost,1); 
        //提取username
    	$username = $type['username'];


    	//提取password
    	$password = $type['password'];//查询数据库     

    	$userdata = Db::name('user')->where('username',$username)->find();

    	//用户不存在
    	if(empty($userdata)){
    		$return['err'] = '1';
    		$return['status'] = '2035';
    		$usernameNo = $type['username'] . '_非法用户';
    		$log = Db::name('log')->insert(['username'=>$usernameNo,'time'=>date('Y-m-d H:i:s'),'operation'=>'普通用户登录_用户不存在','state'=>'失败']);
    		return $return;
    	}
    	if(md5($password) !== $userdata['password']){
    		$return['err'] = '1';
    		$return['status'] = '2036';
    		$log = Db::name('log')->insert(['username'=>$username,'time'=>date('Y-m-d H:i:s'),'operation'=>'普通用户登录_密码不正确','state'=>'失败']);
    		return $return;
    	}

    	//判断token是否过期了
		$userdata['token'] = md5(sha1(md5(GenerateToken(32))));
		$userdata['tokenDeadline'] = time() + 86400;
		$update = Db::name('user')->where('id',$userdata['id'])->update($userdata);

    	//传入session
    	Session::set('username',$userdata['username']);
    	Session::set('power',$userdata['power']);
    	//删除userdata中的密码
    	unset($userdata['password']);
    	//错误码
    	$userdata['err'] = 0;
    	$log = Db::name('log')->insert(['username'=>$username,'time'=>date('Y-m-d H:i:s'),'operation'=>'普通用户登录','state'=>'成功']);
    	//返回数据
    	return $userdata;
    	
    } 

	//注册 
   	public function register()
    {
    	//传输格式
    	header("ACCESS-CONTROL-ALLOW-ORIGIN:*");
        //接收数据
        $dataPost = file_get_contents('php://input'); 
        //转换格式
        $post =json_decode($dataPost,1); 
        if(empty($post)){
            $data = get_status(1,0,'添加失败');
            return $data;
        }


    	$usernamePost = $post['username'];
    	$passwordPost = $post['password'];
        //预留
    	$power = 0;

    	$usernameDb = Db::name('user')->where('username',$usernamePost)->find();
    	if(!empty($usernameDb)){
            $data = get_status(1,0,'添加失败');
            return $data;
    	}

    	$deadline = time() + 1296000;
    	$insertData['password'] = md5($passwordPost);
    	$insertData['username'] = $usernamePost;
    	$insertData['power'] = $power;
    	$insertData['token'] = GenerateToken(32);
    	$insertData['tokenDeadline'] = $deadline;
    	$insert = Db::name('user')->insert($insertData);
    	if($insert == 0){
    		$data = get_status(1,0,'添加失败');
            return $data;
    	}


        $data = get_status(0,0,'添加成功');
        return $data;

	}


	/**
	 * 保持登录状态
	 */
	public function keepLogging(){
		//初始化userModel
		$this->userModel = new UserModel();
		//获取uid和token
		$arr = get_all_header();
		if(isset($arr['token'])){
			$this->token = $arr['token'];    
		}else {
			$data = get_status(1,'非法用户访问' ,10001 );
			return json($data);
		}
		$input['token'] = $arr['token'];
		//查询token是否存在于token表
		$getUid = $this->userModel->getMessg('token' ,['token' => $arr['token']]);
		if(!$getUid) {
				$data = get_status(1,'Token验证失败' , 10002);
				return json($data);
		}
		$input['uid'] = $getUid[0]['uid'];
		$uid = $input['uid'];

		//将用户信息存入变量
		$user = $this->userModel->getUser($uid);
		if($user){
				$this->user = $user;
		}
		// 查询token过期时间
		$token = $this->userModel->getMessg('token' , ['token' => $input['token']]);
		//判断token是否存在
		if($token){
				//判断token是否过期
				if(time() > $token[0]['tokentime']){
						//token过期 删除token  同时登录-1
						$userTokenDel = $this->userModel->getMessg('user' , ['uid' => $input['uid']]);
						$online = $userTokenDel[0]['online'] - 1;
						//return json($online);
						$userTokenDel = $this->userModel->updateMessg('user' , ['uid' => $input['uid']] , ['online' => $online]);
						$tokendel = $this->userModel->deleteMessg('token' , ['token' => $input['token']]);
						$data = get_status(1,'由于您长时间未操作系统自动退出' , 10003);
						return json($data);
				}else {
						//token未过期 ，操作加token过期时间
						$data  = [
								'tokentime' => time()+7200
						];
						$tokentime = $this->userModel->updateMessg('token' , ['token' => $input['token']] , $data);
				}
		}else{
				$data = get_status(1,'请先登录' , 10004);
				return json($data);
		}
	}

	

}
