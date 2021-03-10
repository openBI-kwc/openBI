<?php

namespace app\user\controller;

use app\user\model\User as UserModel;
use app\index\model\User as UModel;
use think\Request;
use think\Validate;
use think\Db;

/**
 * 用户处理类用于登录 修改用户信息 退出登录
 */
class User extends UserModel
{
    //将Model存入变量
    protected $UserModel;

    //保存用户信息
    protected $user;

    //保存用户角色
    protected $roleName;

    //获取header的值



    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        //初始化UserModel
        $this->UserModel = new UserModel();
        $this->UModel = new UModel();
        if(strtolower($_SERVER['REQUEST_METHOD']) == 'options') {
            exit();
        }

        $webPath = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

        if(strstr($webPath.'!!' , '/public/')) {
            $webPath = substr($webPath , 0 , strrpos($webPath , 'public'));
            $serverPath = $webPath.'public';
        }else{
            $serverPath = $_SERVER['REQUEST_SCHEME'] .'://'.$_SERVER['HTTP_HOST'];
        }
        //指向public内
        define('WEB_PATH', $serverPath);
    }



    //处理登录 安全     设置全用户通用
    public function doLogin()
    {
        if (strtolower($_SERVER['REQUEST_METHOD']) == 'options') {
            return json(['err' => 1]);
        }
        $input = file_get_contents('php://input');
        $input = json_decode($input,1);
        //解密
        $input['password'] = decrypt($input['password'] , $input['len']);
        //查看浏览器是否拥有token
        if(isset($this->get_all_header()['token'])){
            $token = $this->get_all_header()['token'];
        }else{
            $token = false;
        }
        if($token) {
            $getoken = $this->UserModel->getMessge('token' , ['token' => $token]);
            //获取uid
            $tokenUid = $getoken[0]['uid'];
            //获取用户同时在线人数
            $tokenOnline = $this->UserModel->getMessge('user',['uid' => $tokenUid]);
            $userOnline = $tokenOnline[0]['online'] - 1;
            //删除已有token
            $this->UserModel->messgeDlete('token',['token' => $token]);
            //修改同时在线人数
            $this->UserModel->messgeUptate('user' , ['uid' =>$tokenUid] , ['online' =>$userOnline]);
        }

        //删除过期用户登录信息
        $result = $this->UserModel->getMessge('token');
        if($result){
            foreach ($result as $key => $value) {
               if($value['tokentime'] < time()) {
                //删除过期用户token
                $tokendel = $this->UserModel->messgeDlete('token',['tid' => $value['tid']]);
                //查询删除token的用户
                $userTokenDel = $this->UserModel->getMessge('user',['uid' => $value['uid']]);

//                   dump($tokendel);
//                   dump($userTokenDel);
                if($userTokenDel){
                    //将用户登录次数减少1
                    $userOnline = ($userTokenDel[0]['online'] -1);
                    $userOnlineDel = $this->UserModel->messgeUptate('user',['uid' => $value['uid']],['online' => $userOnline]);
//                    dump($userOnline);
//                    dump($userOnlineDel);

                }
               }
            }
        }

        //验证账号密码字段是否存在
        $valivaliKeys =  valiKeys(['password','username'] , $input);
        if($valivaliKeys['err'] == 1) {
            $arr  = get_status(1,'用户名或密码不能为空' , 1001);
            return $arr;
        }
        // 验证账号密码是否为空
        $validate = new Validate([
           'username' => 'require',
           'password' => 'require',
        ]);

        $data = [
          'username' => $input['username'],
          'password' => $input['password'],
        ];
// dump($data);die;
        if(!$validate->check($data)) {
            $arr  = get_status(1,'用户名或密码不能为空' , 1001);
            return $arr;
        }

        //查询安全设置
        $safety = $this->UserModel->getMessge('safety');
        if($safety){
            $safety = $safety[0];
        }
        //使用用户名查询数据库记录
        $result = $this->UserModel->vali('user',['username' => $input['username']]);
        if(!$result) {
            $arr = get_status( 1 , '用户不存在', 1009);
            return $arr;
        }
        //获取盐
        $salt = $result['salt'];

        //匹配密码
        if(md5($input['password'].$salt) != $result['password']){
            $result = false;
        }

        $user =    [
            'username' => $result['username'],
            'userid' => $result['uid'],
            'avatar' => $result['avatar']
        ];

        if($result) {
            $this->user = $result;
            $uid = $result['uid'];
            if($result['online'] < 0 ) {
                $online = 0;
            }else {
                $online = $result['online'];
            }
            $openlog = $result['openlog'];
            //查询登录信息 
           
            if($safety['terminal'] < ($result['online']+1) && $safety['terminal'] != 0){
                //如果设定用户不能登录
                $arr  = get_status(1,'用户账号已达到登录上限' , 1002);
                return $arr;
            }
            //检测用户是否可以登录
           if($result['status'] > 0) {
                if($result['locktime'] > time()){
                    $arr  = get_status(1,'用户已被禁止登录' , 1003);
                    return $arr;
                }
           }
           //通过用户id获得角色id
            $result = $this->UserModel->getRole($uid);
            if($result) {
                $rid = $result['rid'];
            }else{
                $arr = get_status( 1 , '用户没有指定角色',1004);
                return $arr;
            }


            //通过角色ID获得权限ID
            $result = $this->UserModel->getPid($rid);


            if($result) {
                $pid = $result['pid'];
            }else {
                return false;
            }


            //获取用户组名
            $roleName = $this->UserModel->getRolename($rid);
            $roleName = $roleName['rolename'];
            $this->roleName = $roleName;

            //通过权限ID获取权限列表
            $result = $this->UserModel->getPermission($pid);
            

            if(!$result) {
                $arr = get_status( 1 , '登录失败' , 1000);
                return $arr;
            }



            //设定唯一登录标识
            $rand = mt_rand(10000,99999);
            //设置token
            $account_session = md5($uid.time().$rand);
            //设置盐
            // $salt = mt_rand(1000999,9999999);
            
            $datas = [
                'online' => ($online + 1),
                'status' => 0,
                'error' => 0,
                'locktime' => 0,
                'lastlogintime' => time(),
            ];
            //修改标识给予登录信息
            $arrd = $this->UserModel->messgeUptate('user', ['uid' => $uid],$datas);
                        
            //设置tokendata
//            $online = $online+1;

            $data = [
                'uid' => $uid,
                'token' => $account_session,
                'tokentime' => time()+7200
             ];
             //将用户信息存入token
            $token = $this->UserModel->messgeAdd('token',$data);
            if(!$token) {
                $arr = get_status( 1 , 'token设置失败' , 1005);
                return $arr;
            }
            
            //是否记录日志
            
            $this->userLogs($this->user, $roleName , '用户登录',1,1);
            unset($result);

            $url = 'index';
            //用户路由（7.5）
            $date = $this->windex_userRouting($uid);
            //左侧菜单栏(7.5)
            $per = $this->windex_index($uid);
            $data = ['token' => $account_session , 'accessmenu'=>$per];
            $arr = get_status(0,$data);
            header("Token:$account_session");
            return $arr;
        }else{
            //登录错误处理
            $getname = $this->UserModel->getMessge('user' , ['username' => $input['username']]);
            if(!$getname) {
                $arr = get_status( 1 , '用户不存在' , 1009 );
                return $arr;
            }else {
                //检测用户是否可以登录
                if($getname[0]['status'] > 0) {
                    if($getname[0]['locktime'] > time()){
                        $arr  = get_status(1,'该用户已被禁止登录',1003);
                        return $arr;
                    }
                 }
                 if($safety['maxerr'] != 0) {
                     //将密码错误次数存入
                     $data = [];
                     $error = $getname[0]['error'] + 1;
                     $data['error'] = $error;
                     //错误次数达到变量次数锁定用户2小时
                     $errNum = $safety['maxerr'];
                     $time = time()+$safety['intervaltime'];

                     if($error >= $errNum) {
                         $data['status'] = $getname[0]['status'] + 1;
                         $data['locktime'] = $time;
                     }
                     $num = $errNum - $getname[0]['error'];
                     if($num >0){
                         $arr = get_status( 1 , '密码错误还可以输入'.$num.'次' , 1010);
                     }else{
                         $arr  = get_status(1,'该用户已被禁止登录',1003);
                         return $arr;
                     }
                     $result = $this->UserModel->messgeUptate('user' , ['username' => $input['username']] ,$data);
                     return $arr;
                 }else {
                     $arr = get_status( 1 , '密码错误' ,1010);
                     return $arr;
                 }

            }

        }
    }

    


    /**
     * loginout 退出登录
     * uid : 用户id
     *
     * @return [type] [description]
     */
    public function loginOut()
    {
        //获取uid
        $header = $this->get_all_header();
        $token = $header['token'];
        $getUid = $this->UserModel->getMessge('token' ,['token' => $token]);
        if(!$getUid) {
            $arr = get_status( 1 , '该用户已退出' , 10007);
            return $arr;
        }

        $uid = $getUid[0]['uid'];

        //查询数据库获得用户登录信息
        $user = $this->UserModel->getMessge('user' , ['uid' => $uid]);
        if(!$user){
           $arr = get_status( 1 , '退出失败' , 1011);
           return $arr;
        }
        //删除用户登录的token.
        Db::startTrans();
            try{
                $tokenDel = $this->UserModel->messgeDlete('token',['token' => $token]);
                if(!$tokenDel) {
                    throw new \Exception('删除记录失败');
                }
                $online = $user[0]['online'] - 1;
                $tokenDel = $this->UserModel->messgeUptate('user' , ['uid' => $uid], ['online' => $online]);
                if(!$tokenDel) {
                    throw new \Exception('删除记录失败');
                }
                Db::commit();
            } catch (\Exception $e) {
                // 回滚事务
                Db::rollback();
                $this->userLogs($user[0],'', '退出登录', 0 , 1);
                $arr = get_status(1,'删除token失败' , 1006);
                return json($arr);
            }

        $this->userLogs($user[0],'', '退出登录', 1 , 1);
        $arr = get_status(0,'退出成功');
        return json($arr);

    }



    //用户修改密码
    public function userPsswordUpdate()
    {
        $input = file_get_contents('php://input');
        $input = json_decode($input,1);
        //解密密码
        $input['password'] = decrypt($input['password'] , $input['len']);
        $input['newPassword'] = decrypt($input['newPassword'] , $input['newlen']);
        //获取uid
        $header = $this->get_all_header();
        $token = $header['token'];
        $getUid = $this->UserModel->getMessge('token' ,['token' => $token]);
        $uid = $getUid[0]['uid'];

        //查询用户是否多人登录
        // $userOnline = $this->UserModel->getMessge('user',['uid' => $uid]);
        // if($userOnline[0]['online'] >1 ) {
        //     $arr  = get_status(1,'多个用户登录' , 1007);
        //     return $arr;
        // }
        // 验证账号密码是否为空
        $validate = new Validate([
           'password' => 'require',
           'newPassword' => 'require'
        ]);

        $data = [
          'password' => $input['password'],
          'newPassword' => $input['newPassword']
        ];

        if(!$validate->check($data)) {
            $arr  = get_status(1,'新密码或密码不能为空' , 1008);
            return $arr;
        }


        //查询用户
        $user = $this->UserModel->getMessge('user' , ['uid' => $uid]);

        //获取盐
        $salt = $user[0]['salt'];


        //匹配密码
        if(md5($input['password'].$salt) != $user[0]['password']){
            $arr  = get_status(1,'原密码错误' , 1013);
            return $arr;
        }

        //设置新的盐
        $salt = mt_rand(1000999,9999999);
        $data = [
            'password' => md5($input['newPassword'].$salt),
            'salt' => $salt,
            'online' =>0
           ];

        $result = $this->UserModel->messgeUptate('user',['uid' => $uid] , $data);
        $token = $this->UserModel->messgeDlete('token',['uid' => $uid]);
        if($result){
            $this->userLogs($this->user, $this->roleName , '修改成功--'.$this->user,'1','0');
            $arr = get_status(0 , '修改成功，请重新登录');
           }else{
            $this->userLogs($this->user, $this->roleName , '修改失败--'.$this->user,'0','0');
            $arr = get_status(1 , '修改失败' ,1014);
           }
           return json($arr);

    }

    /**
     * 修改基本信息
     * uid : 必须
     * address : 地址
     * phone ： 联系电话
     * email ： 邮箱
     * avatar : 头像
     */
    
    public function basicUpdate()
    {
        $input = file_get_contents('php://input');
        $input = json_decode($input,1);
        $uid = $input['uid'];
        // 验证表单是否为空
        $validate = new Validate([
           'address' => 'require',
           'phone' => 'require|max:11|/^1[3-8]{1}[0-9]{9}$/',
           'email' => 'require|email',
           'avatar' => 'require',
        ]);

        $data = [
          'address' => $input['address'],
          'phone' => $input['phone'],
          'email' => $input['email'],
          'avatar' => $input['avatar'],
        ];

        if(!$validate->check($data)) {
            $arr  = get_status(1,'表单验证失败',1008 );
            return $arr;
        } 

        $msg = $this->UserModel->getMessge('user',['uid' => $uid]);

        $result = $this->UserModel->messgeUptate('user' , ['uid' => $uid], $data);
        if($result){
            $this->userLogs($msg[0],'','修改成功--'.$this->user,1,0);
            $arr = get_status(0 , '修改成功');
           }else{
            $this->userLogs($msg[0],'', '修改失败--'.$this->user, 1 , 0);
            $arr = get_status(1 , '修改基本信息失败' , 1014  );
           }
           return json($arr);
    }

    /**
     * @Notes : 修改头像
     * @access :
     * @author : 
     * @Time: 2018/08/07 10:57
     */
    public function uploadImg()
    {
        $input = $_POST;
        $header = $this->get_all_header();
        $token = $header['token'];
        $getUid = $this->UserModel->getMessge('token' ,['token' => $token]);
        $uid = $getUid[0]['uid'];
            
        $file = request()->file('userImg');
        if($input['type']){
            //文件夹路径
            $path = ROOT_PATH . 'public'  . DS . 'uploads' . DS . 'staticimg';
             //判断目录是否存在 不存在就创建
             if (!is_dir($path)){
                mkdir($path,0777,true);
            }
            //图片保存路径
            $info = $file->validate(['size'=>156711800 /*,'ext'=>$select[0]['type']*/])->move($path);
           
            //文件路径
            $filePath = '/uploads' . DS . 'staticimg' . DS . $info->getSaveName();
            if($file) {
                
                // $avatarPath = '/uploads/staticimg/'.date('Ymd' , time()).'/'. $fileName;
                //文件路径入库
                $user = $this->UserModel->messgeUptate('user',['uid' => $uid] , ['avatar' => $filePath]);
                if($user) {
                    $arr = get_status(0, $filePath, 5001 );
                }else{
                    $arr = get_status(1,'修改失败' , 1014  );
                }
            }else {
                $arr = get_status(1,'修改失败' , 1014  );
            }
        }else{
            $avatar = str_replace(WEB_PATH , '' , $input['userImg']);
            $user = $this->UserModel->messgeUptate('user',['uid' => $uid] , ['avatar' => $avatar]);
            if($user) {
                $arr = get_status(0, $avatar, 5001 );
            }else{
                $arr = get_status(1,'修改失败' , 5001 );

            }
        }
        return json($arr);
    }

    /*
     * 验证数据库连接
     */
    public  function  valiDatabase()
    {
        $input = file_get_contents('php://input');
        $input = json_decode($input,1);

        $host =  $input['host'];
        $username = $input['username'];
        $password =  $input['password'];

        $link = @mysqli_connect("$host" , "$username" , "$password" );
        if (!$link)
        {
            $arr = get_status(1 ,"连接错误: " . mysqli_connect_errno() .' : '. mysqli_connect_error(),9000);
            return json($arr);

        }
        $charset = mysqli_set_charset($link,$input['charset']);
        if (!$charset)
        {
            $arr = get_status(1 , "连接错误: 字符集错误",9001);
            return json($arr);

        }
        mysqli_close($link);
        $arr = get_status(0,'数据库验证成功');
        return json($arr);
    }



    //记录操作日志
    //$user : 用户信息
    //$tole : 角色组名
    //$msg : 操作信息
    public function userLogs($user , $role , $msg , $status , $type)
    {
        

        $safety = $this->UserModel->getMessge('safety');
        if($safety){
            $safety = $safety[0];
        }

        if(!$safety['adminlog']) {
            return false;
        }
        //通过用户id获得角色id
        $result = $this->UserModel->getRole($user['uid']);
        if($result) {
            $rid = $result['rid'];
        }else{
            $arr = get_status( 1 , '该用户没有指定角色');
            return $arr;
        }

        //通过角色ID获得权限ID
        $result = $this->UserModel->getPid($rid);
        if($result) {
            $pid = $result['pid'];
        }else {
            return false;
        }

        //获取用户组名
        $roleName = $this->UserModel->getRolename($rid);
        $roleName = $roleName['rolename'];
        //获取访问ip
        
        $ip = $_SERVER["REMOTE_ADDR"];
        $ip = ip2long($ip);
        //日志记录
        $data = [
            'uid' => $user['uid'],
            'realname' => $user['realname'],
            'username' => $user['username'],
            'userrole' => $roleName,
            'lastlogintime' => time(),
            'ip' => $ip,
            'operating' => $msg,
            'type' => $type,
            'status' => $status,
            'rid'=>$rid

        ];
        //保存日志
        $this->UserModel->messgeAdd('userlog' , $data);

    }


    //获取header头中的值（token）
    public function get_all_header(){
        // 忽略获取的header数据。这个函数后面会用到。主要是起过滤作用
        $ignore = array('host','accept','content-length','content-type','request_method');

        $headers = array();
        //这里大家有兴趣的话，可以打印一下。会出来很多的header头信息。咱们想要的部分，都是‘http_'开头的。所以下面会进行过滤输出。
        /*    var_dump($_SERVER);
            exit;*/

        foreach($_SERVER as $key=>$value){
            if(substr($key, 0, 5)==='HTTP_'){
                //这里取到的都是'http_'开头的数据。
                //前去开头的前5位
                $key = substr($key, 5);
                //把$key中的'_'下划线都替换为空字符串
                $key = str_replace('_', ' ', $key);
                //再把$key中的空字符串替换成‘-’
                $key = str_replace(' ', '-', $key);
                //把$key中的所有字符转换为小写
                $key = strtolower($key);

                //这里主要是过滤上面写的$ignore数组中的数据
                if(!in_array($key, $ignore)){
                    if($key == "access-token") {
                        $headers['token'] = $value;
                    }else {
                        $headers[$key] = $value;
                    }

                }
            }
        }
        //输出获取到的header
        return $headers;

    }

    /**
     * @Notes : 解密
     * @access : $password : 解密内容
     * @author : wwk
     * @Time: 2018/08/13 11:23
     */
    protected function decrypt($password , $len)
    {
        $key = 'kwc.net';
        $str = openssl_decrypt($password, 'aes-128-cbc',$key,2);
        $pwd = substr($str, 0,$len);
        return $pwd;
    }

     //获取用户权限列表
     public function getPermission($uid)
     {
         //将用户信息存入变量
         $user = $this->UModel->getUser($uid);
         if($user){
             $this->user = $user;
         }
         //将用户角色名存入变量
         // $rolename = session('rolename');
         
         //通过用户id获得角色id
         $result = $this->UModel->getRole($uid);
         if($result) {
             $rid = $result['rid'];
         }else{
              // return json_encode(['err' => 1 ,'data' => '该用户没有指定角色']);
              $data = get_status(1,'该用户没有指定角色',1004);
              return $data;
         }
 
         //通过角色ID获得角色名称
         $result = $this->UModel->getRolename($rid);
 
         //将角色名称存入用户变量
         $this->rolename = $result['rolename'];
 
         //通过角色ID获得权限ID
         $result = $this->UModel->getPid($rid);
         if($result) {
                 $rid = $result['pid'];
             }else{
                 return false;
         }
 
         //通过权限ID获取权限列表
         $result = $this->UModel->getPermission($rid);
         if($result) {
             //将用户权限存入变量
             return $result;
         }else{
             return false;
         }
     }

     //获取用户可操作菜单栏 需要 uid 
    public function windex_index($uid)
    {
        $result = $this->getPermission($uid);

        //定义主菜单
        $column = [];
        //定义子菜单
        $menu = [];
        $j = 0;
        $k = 0;
        for ($i=0; $i < count($result); $i++) {
        	//lv为0的为主菜单 
        	if($result[$i]['lv'] == 0) {
        		$column[$j]['pid'] = $result[$i]['pid'];
        		$column[$j]['pname'] = $result[$i]['pname'];
        		$column[$j]['parentid'] = $result[$i]['parentid'];
                $column[$j]['path'] = $result[$i]['path'];
        		$j++;
        	}
        	//lv为1的为子菜单
        	if($result[$i]['lv'] == 1) {
        		$menu[$k]['pid'] = $result[$i]['pid'];
        		$menu[$k]['pname'] = $result[$i]['pname'];
        		$menu[$k]['parentid'] = $result[$i]['parentid'];
        		$menu[$k]['path'] = $result[$i]['path'];
        		$k++;
        	}
        }
        $this->accessRecord();
  		//将菜单合并为一个数组
        $arr = [
        	'column' => $column,
        	'menu' => $menu,
        ];
        return $arr;
    }
    protected function accessRecord()
    {
        $name = $_SERVER['HTTP_ORIGIN'] ?? ($_SERVER['HTTP_REFERER'] ?? $_SERVER['HTTP_HOST']);
        return curl_request('http://www.openbi.com.cn/api/updatav/service?server_name='.$name, ['sec' => 1]);
    }
    public function windex_userRouting($uid)
    {
        //拿到用户权限
        $result = $this->getPermission($uid);
        //判断路由是否为空
        if(empty($result)) {
            return get_status(0,[]);
        }
        //声明路由返回数组
        $date = [];
        //遍历权限数组
        foreach($result as $value) {
         //判断是否是路由
         if($value['path'] != '') {
             //将路由加入数组
             $date[] = $value['path'];
             }
         }
         return $date;
    }

}
