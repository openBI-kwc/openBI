<?php
namespace app\index\controller;

use app\base\controller\Base;
use app\index\model\User as UserModel;
use think\Request;
use think\Validata;
use think\Db;

/**
 * 用户执行对用户的查询
 */
class Windex extends Base{

    protected $userModel;

    //将用户信息存入变量
    protected  $user;
    //将权限信息存入变量
    protected  $permission;
    //将角色名称存入变量
    protected  $rolename;


    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        //初始化userModel
        $this->userModel = new UserModel();


    }

    //获取用户可操作菜单栏 需要 uid 
    public function index()
    {

        $uid = $this->user['uid'];
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

  		//将菜单合并为一个数组
        $arr = [
        	'column' => $column,
        	'menu' => $menu,
        ];
        $data = get_status(0,$arr);
        return $data;    
    }

    /**
     *  用户拥有的路由 
     */
    public function userRouting()
    {
        //拿到用户ID
        $uid = $this->user['uid'];
        //拿到用户权限
        $result = $this->getPermission($uid);
        //判断路由是否为空
        if(empty($result)) {
            return get_status(0,[]);
        }
        //声明路由返回数组
        $data = [];
        //遍历权限数组
        foreach($result as $value) {
            //判断是否是路由
            if($value['lv'] == 0) {
                //将路由加入数组
                $data[] = $value;
            }
        }
        $datas = [];
        //获取二级级菜单下的路由
        foreach($data as $value) {
            if($value['path'] != '') {
                //一级菜单有路由的情况
                $datas[] = $value['path'];
            }
            //遍历总权限
            foreach($result as $val) {
                //获取一级菜单下的二级菜单
                if($value['pid'] == $val['parentid'] && $val['path'] != '') {
                    $datas[] = $val['path'];
                }
            }
        }
        return get_status(0,$datas);
    }

    /**
     * 获取首页路由
     */
    public  function  getIndexPath()
    {
        $uid = $this->user['uid'];
        $result = $this->getPermission($uid);
        $path  = "";
        foreach ($result as $key => $value){
            if($value['lv'] == 0 ) {
                foreach ($result as $k=>$v) {
                    if($v['parentid'] == $value['pid'] && $v['v'] = 1){
                        $path = $v['path'];
                        break;
                    }
                }
                break;
            }
        }
        if(!$path) {
            $arr = get_status(0,[]);
            return json($arr);
        }
        $arr = get_status(0,$path);
        return json($arr);

//        dump($result);
    }

    /**
     * 获取所有用户列表
     * 返回 对应用户和对应的角色组
     * 用户管理
     */
    public function userList()
    {
        $input = input('get.');
        $uid = $this->getUid();
        $input['pid'] = 8;
        $result = $this->getPermission($uid);
        $permission = [];

        foreach ($result as $key => $value) {
           //获取权限
           if($value['parentid'] == $input['pid'] && $value['lv'] == 3) {
            $permission[] = $value['pname'];

           }
        }
        $str = implode(',', $permission);

        //分页 $currentPage第几页
        if(isset($input['currentPage'])){
            $currentPage = $input['currentPage'];
        }else {
            $currentPage = 1;
        }
        //分页  $pageSize每页条数
        if(isset($input['pageSize'])){
            $pageSize = $input['pageSize'];
        }else {
            $pageSize = 10;
        }

       

    	$user = $this->userModel->getField('user' , '' , 'uid,username,lastlogintime,realname,status');
        
        // 获取表前缀
	    $p=config('database.prefix');
    
        $role = $p.'role';
        $user = $p.'user';
        $user_role = $p.'user_role';

        $sql = Db::table([$role  , $user  ,$user_role])
        ->where("$role.rid = $user_role.rid")
        ->where("$user.uid = $user_role.uid")
        ->page($currentPage.','.$pageSize)
        ->order("$user.createtime", 'asc')
        ->select();


        //声明空对象：
        $empty_object=(object)null;
        //把数组转换为对象：
        $arr=array();
        $empty_object=(object)$arr;

        $i = 0;
        //返回需要的数组
        $arr = [];
        foreach ($sql as $key => $value) {
        	$arr[$i] = [
        		'uid' => $value['uid'],
        		'username' => $value['username'],
        		'realname' => $value['realname'],
        		'rolename' => $value['rolename'],
                'lastlogintime' => ($value['lastlogintime'] * 1000),
        		'status' => $value['status'],
        	];
        	$i++;
        }

        $total = Db::table([$role  , $p.'user'  ,$p.'user_role' ])
        ->where("$role.rid = $user_role.rid")
        ->where("$user.uid = $user_role.uid")
        ->select();
        $total = count($total);

        $arr = [
            'list' => $arr,
            'total' => $total,

        ];

        $arr['options'] = [];
        if(strstr( ($str.'sss') , '新增')) {
            $arr['options']['add'] = '添加';
        }
        if(strstr(($str.'sss') , '修改' )) {
            $arr['options']['edit'] = '编辑';
        }
        if(strstr( ($str.'sss') , '删除')) {
            $arr['options']['del'] = '删除';
        }

        if(empty($arr['options'])) {
            $arr['options'] = $empty_object;
        }
    	$data = get_status(0,$arr);
    	return json($data);
    }


    /**
     * 用户组列表
     * [roleList description]
     * @return [数组] [用户组以及用于组下的用户]
     * @param [int] $[uid] [用户ID]
     */
        public function roleList()
    {
//        $input = file_get_contents('php://input');
//        $input = json_decode($input,1);
        $input = input('get.');
        $uid = $this->getUid();
        $input['pid'] = 9;

        $result = $this->getPermission($uid);
        $permission = [];
        foreach ($result as $key => $value) {
           //获取权限
           if($value['parentid'] == $input['pid'] && $value['lv'] == 3) {
            $permission[] = $value['pname'];
           }
        }

        //将操作名转换成字符串
        $str = implode(',', $permission);

 		//获取角色组


        //分页 $currentPage第几页
        if(isset($input['currentPage'])){
            $currentPage = $input['currentPage'];
        }else {
            $currentPage = 1;
        }
        //分页  $pageSize每页条数
        if(isset($input['pageSize'])){
            $pageSize = $input['pageSize'];
        }else {
            $pageSize = 10;
        }

        $total = $this->userModel->getMessg('role');
        $role['total'] = count($total);


        $roleList =  Db::name('role')->page($currentPage,$pageSize)->order('createtime','DESC')->select();
        $role['role'] = $roleList;
        $userList = $this->userModel->getField('user', '', 'uid,username');
        $urid = $this->userModel->getMessg('user_role');
        $i = 0;
        $role['options'] = [];

        foreach ($userList as $key => $value) {
            foreach ($urid as $k => $v) {
                if($value['uid'] == $v['uid']) {
                    $role['user'][$i]['username'] = $value['username'];
                    $role['user'][$i]['uid'] = $value['uid'];
                    $role['user'][$i]['rid'] = $v['rid'];
                    $i++;
                }
            }
        }

        if(strstr($str.'sss' , '修改' )) {
            $role['options']['edit'] = '编辑';
        }

        if(strstr( $str.'sss' , '新增')) {
            $role['options']['add'] = '添加';
        }

        if(strstr( $str.'sss' , '删除')) {
            $role['options']['del'] = '删除';
        }

        if(empty($role['options'])){
            $role['options'] = (object)null;
        }

        $data = get_status(0,$role);
        return $data;
    }


    /**
     * 分类管理
     */
    public function manageClass()
    {

        $input = input('get.');
        $input['pid'] = 10 ;
        $uid = $this->getUid();
        $result = $this->getPermission($uid);
        $permission = [];
        foreach ($result as $key => $value) {
           //获取权限
           if($value['parentid'] == $input['pid'] && $value['lv'] == 3) {
            $permission[] = $value['pname'];
           }
        }


        //分页 $currentPage第几页
        if(isset($input['currentPage'])){
            $currentPage = $input['currentPage'];
        }else {
            $currentPage = 1;
        }
        //分页  $pageSize每页条数
        if(isset($input['pageSize'])){
            $pageSize = $input['pageSize'];
        }else {
            $pageSize = 10;
        }

        //将操作名转换成字符串

        $str = implode(',', $permission);


    	$mc['list'] = $this->userModel->getField('screengroup','','screenname,sid,remarks',null , $currentPage . ','.$pageSize);

    	$mc['total'] = $this->userModel->countNember('screengroup','sid');
	
	   if(empty($mc['list'])){
	       $mc['list'] = [];
	   }
    	//每个分类总屏幕数
        foreach ($mc['list'] as $key=>$value) {
            //查询屏幕个数
            $num = $this->userModel->getMessg('screen' , ['sid' => $value['sid'],'screentype' => 0]);
            //统计屏幕个数
            if($num) {
                $number = count($num);
            }else {
                $number = 0;
            }
            //放入数组
            $mc['list'][$key]['number'] = $number;
        }

        //操作权限
        foreach ($mc as $key => $value) {
        	if(strstr( $str.'sss' , '编辑')) {
        		$mc['options']['rename'] = '编辑';
        	}
        	if(strstr( $str.'sss' , '删除')) {
        		$mc['options']['del'] = '删除';
        	}

        	if(strstr( $str.'sss' , '添加')) {
        		$mc['options']['add'] = '添加';
        	}
        }

    	$arr = $mc;
    	$arr = get_status(0,$arr);
    	return json($arr);

    }

    /**
     * [permissionList 权限列表显示]
     * @return [arr] [权限列表]
     */
    public function permissionList()
    {
//    	$input = file_get_contents('php://input');
//        $input = json_decode($input,1);
        $input = input('get.');
        $input['pid'] = 11;

        $uid = $this->getUid();

        $result = $this->getPermission($uid);

        $permission = [];
        foreach ($result as $key => $value) {
           //获取权限
           if($value['parentid'] == $input['pid'] && $value['lv'] == 3) {
            $permission[] = $value['pname'];
           }
        }
        $str = implode(',', $permission);


    	$result = $this->userModel->getField('permission','','pid,pname,lv,parentid,path');
    	$parent = [];
    	$child = [];
    	$i = 0;
    	$k = 0;
    	foreach ($result as $key => $value) {
    		if($value['lv'] == 0){
    			$parent[$i] = $value;
    			$i++;
    		}else{
				$child[$k] = $value;
    			$k++;
    		}

    	}

        $parent[$key] = [];
    	foreach ($parent as $key => $value) {
        	if(strstr($str , '添加应用' )) {
        		$parent[$key]['radd'] = '添加应用';
        	}

        	if(strstr( $str , '编辑')) {
        		$parent[$key]['update'] = '编辑';
        	}

        	if(strstr( $str , '删除')) {
        		$parent[$key]['del'] = '删除';
        	}

        	if(strstr( $str , '添加')) {
        		$parent[$key]['add'] = '添加';
        	}
          } 
        //将操作名转换成字符串
    	$arr = [
    		$parent,
    		$child
    	];
    	$arr = get_status(0,$arr);
    	return json($arr);
    	
    }


    /**
     * [logList 显示操作日志]
     * @return [arr] [操作日志]
     */
    public function logList()
    {
        $input = input('get.');
        if(isset($input['currentPage'])){
            $currentPage = $input['currentPage'];
        }else {
            $currentPage = 1;
        }
        //分页  $pageSize每页条数
        if(isset($input['pageSize'])){
            $pageSize = $input['pageSize'];
        }else {
            $pageSize = 10;
        }

        $result = Db::name('userlog')
                  ->where(['type' => 0])
                  ->order('lastlogintime' , 'desc')
                  ->page($currentPage .','. $pageSize)
                  ->select();

        $total = $this->userModel->getMessg('userlog',['type' => 0]);
        if($total) {
            $arr['total'] = count($total);
        }else {
            $arr['total'] = 0;
        }

    	foreach ($result as $key => $value) {
    		$arr['list'][$key]['time'] =  ($value['lastlogintime'] * 1000);
    		$arr['list'][$key]['ip'] = long2ip(intval($value['ip'])) ;
    		$arr['list'][$key]['username'] = $value['username'] ;
    		$arr['list'][$key]['name'] = $value['realname'] ;
    		$arr['list'][$key]['character'] = $value['userrole'] ;
    		$arr['list'][$key]['record'] = $value['operating'] ;

    	}

    	$arr = get_status(0,$arr);
    	return json($arr);

    }

    /**
     * @Notes : logUserList
     * @access :
     * @author : wwk
     * @Time: 2018/08/06 17:06
     */
    public function logUserList()
    {
	

//        $input = file_get_contents('php://input');
//        $input = json_decode($input,1);
        $input = input('get.');
        if(isset($input['currentPage'])){
            $currentPage = $input['currentPage'];
        }else {
            $currentPage = 1;
        }
        //分页  $pageSize每页条数
        if(isset($input['pageSize'])){
            $pageSize = $input['pageSize'];
        }else {
            $pageSize = 10;
        }
        //搜索条件按用户名搜索
        if(!isset($input['searchword'])){
            $input['searchword'] = '';
        }

//        $result['list'] = $this->userModel->getField('userlog' ,['type' => 1] , '',null , $currentPage .','. $pageSize);
        $result['list'] = Db::name('userlog')
            ->where(['type' => 1])
            ->where('username' , 'like' , "%".$input['searchword']."%")
            ->order('lastlogintime' , 'desc')
            ->page($currentPage .','. $pageSize)
            ->select();

        $total = $this->userModel->getMessg('userlog',['type' => 1]);
        if($total) {
            $arr['total'] = count($total);
        }else {
            $arr['total'] = 0;
        }

        if($result['list']){
            foreach ($result['list'] as $key => $value) {
                $arr['list'][$key]['time'] =  ($value['lastlogintime'] * 1000 );
                $arr['list'][$key]['username'] = $value['username'] ;
                $arr['list'][$key]['status'] = $value['status'] ;
                $arr['list'][$key]['character'] = $value['userrole'] ;
                $arr['list'][$key]['record'] = $value['operating'] ;
            }
            $arr = get_status(0,$arr);
        }else {
            $arr = get_status(0 , []);
        }
        return json($arr);
    }

    


    //获取角色组列表
    public function getRole()
    {
        $result = $this->userModel->getMessg('role');
        if($result) {

            $arr = get_status(0 , $result);
        }else {
            $arr = get_status(1 , '获取用户组失败' , 1015);
        }

        return $arr;
        
    }

    //获取所有用户列表
    public function getUser()
    {
        $result = $this->userModel->getField('user');
        if($result) {

            $arr = get_status(0 , $result);
        }else {
            $arr = get_status(1 , '获取用户列表失败',1016);
        }

        return $arr;
    }


    /**
     * 获取屏幕分类
     * 
     */
    public function getScreenGroup()
    {
        $result = $this->userModel->getMessg('screengroup');
        if($result) {
            $arr = get_status(0 , $result);
        }else {
            $arr = get_status(1 , '获取用户列表失败',1016);
        }

        return $arr;
    }




    //获取所有权限列表
    public function permissionGet()
    {
        $where['pid'] = ['IN' , [3,7,8,9,10,12,13,14,15,16,17,18]];
        $parent = $this->userModel->getMessg('permission',$where , null);
        //组成返回数组
        $arr['list'] =   $parent;
        //返回数据
        return get_status(0 , $arr);

    }

    //获取所有权限列表 2019-09-16 备份
    public function permissionGet_black()
    {
        $input = input('get.');
        $input['pid'] =  11;
        $result = $this->userModel->getMessg('permission');
        if(!$result) {
            $arr = get_status(1 , '获取用户列表失败' , 1017);
        }
        //分页 $currentPage第几页
        if(isset($input['currentPage'])){
            $currentPage = $input['currentPage'];
        }else {
            $currentPage = 1;
        }
        //分页  $pageSize每页条数
        if(isset($input['pageSize'])){
            $pageSize = $input['pageSize'];
        }else {
            $pageSize = 10;
        }

        $page = "$currentPage,$pageSize";

        $parent = $this->userModel->getMessg('permission',['lv' => 0] , null ,$page);
        $total = $this->userModel->getMessg('permission',['lv' => 0]);
        $total = count($total);
        $arr['total'] = $total;

        $child = [];
        $options = [];
        $k = 0;
        $o = 0;
        foreach ($result as $key => $value) {
            if($value['lv'] == 1){
                $child[$k] = $value;
                $k++;
            }else if($value['lv'] == 3) {
                if($value['urlc'] == 'windex'){
                    $options[$o] = $value;
                    $options[$o]['default'] = 'query';
                }else{
                    $options[$o] = $value;
                }
                $o++;
            }

        }

        $arr['list'] =  [
            'column' => $parent ,
            'menu' => $child ,
            'options' => $options,
        ];
        //获取用户操作权限
        $uid = $this->getUid();
        $result = $this->getPermission($uid);

        $permission = [];
        foreach ($result as $key => $value) {
            //获取权限
            if($value['parentid'] == $input['pid'] && $value['lv'] == 3) {
                $permission[] = $value['pname'];
            }
        }

        $arr['options'] = [];

        $str = implode(',', $permission);

        if(strstr( $str , '修改')) {
            $arr['options']['edit'] = '编辑';
        }

        if(strstr( $str , '删除')) {
            $arr['options']['del'] = '删除';
        }

        if(strstr( $str , '增加')) {
            $arr['options']['add'] = '添加';
        }

        $arr = get_status(0 , $arr);
        return $arr;

    }


    //获取角色组权限列表 20190916备份
    //rid ：  角色组ID必须
    public function rolePermission_black()
    {
        //获取前端rid
        $input = input('get.rid');
        $getPermission = $this->userModel->getMessg('role_permission',['rid' => $input]);
        if(!$getPermission){
            return get_status(1,'获取用户权限失败' , 1017);
        }
        $pid = $getPermission[0]['pid'];
        $permission = $this->userModel->getField('permission' , '' , 'pid,pname,lv,parentid' ,$pid);
        $parent = [];
        $child = [];
        $options = [];
        $i = 0;
        $k = 0;
        $o = 0;
        foreach ($permission as $key => $value) {
            if($value['lv'] == 0){
                $parent[$i] = $value;
                $i++;
            }else if($value['lv'] == 1){
                $child[$k] = $value;
                $k++;
            }else if($value['lv'] == 3) {
                $options[$o] = $value;
                $o++;
            }

        }
        $arr =  [
            'column' => $parent ,
            'menu' => $child ,
            'options' => $options,
        ];
        $arr = get_status(0 , $arr);
        return $arr;
    }

    //获取角色组权限列表
    public function rolePermission()
    {
        //获取前端rid
        $input = input('get.rid');
        //查询角色权限
        $getPermission = $this->userModel->getMessg('role_permission',['rid' => $input]);
        if(!$getPermission){
            return get_status(1,'获取用户权限失败' , 1017);
        }
        //需要查询的ID
        $pidList = [3,7,8,9,10,12,13,14,15,16,17,18];
        //用户的权限ID集合
        $resultPidList = explode(',' , $getPermission[0]['pid']);

        if(!$resultPidList) { return get_status(1,'获取用户权限失败' , 1017); }
        // 具体返回用户的ID
        $pid = '';
        //查询用户
        foreach ($resultPidList as $value) {
            if (in_array($value , $pidList)) {
                $pid .= ','.$value;
            }
        }
        //判断pid是否为空
        if($pid == '') { return get_status(0,['list' => []] ); }

        //去掉最前面的,
        $pid = ltrim($pid , ',');
        //查询具体权限信息
        $permission = $this->userModel->getField('permission' , '' , 'pid,pname,lv,parentid' ,$pid);
        //返回数据
        return get_status(0,['list' => $permission]);
    }

    //获取角色组的用户列表
    //rid ：  角色组ID必须
     public function roleUser()
    {
//        $input = file_get_contents('php://input');
//        $input = json_decode($input,1);

         $input = input('get.');

        $rid = $input['rid'];
        $getuser = $this->userModel->getMessg('user_role',['rid' => $rid]);
        
        if($getuser){
            $users = [];
            foreach ($getuser as $key => $value) {
               $user = $this->userModel->getField('user' ,['uid' => $value['uid']] , 'uid,username');
               $users[] = $user; 
            }
            $arr = get_status(0 , $users);
            return $arr;
        }
    }
    

    /**
     * 获取指定用户信息
     * uid : 用户ID  必须
     */
    public function getUserMessage()
    {
        $input = input('get.');

        $uid = $input['uid'];
        $usermsg = $this->userModel->getField('user' , ['uid' => $uid ] , 'username , email , status ,realname , phone ,address');
        if($usermsg) {
            $usermsg[0]['pwd'] = '';
        }else {
            $arr = get_status(1,'获取用户失败',1018);
            return json($arr);

        }

        $ur = $this->userModel->getMessg('user_role' , ['uid' => $uid]);
        if(!$ur) {
            $usermsg[0]['role'] = ['rid'=>0 , 'rolename' =>''];
        }else {
            $rid = $ur[0]['rid'];
            $role = $this->userModel->getField('role' ,['rid' => $rid] ,'rid ,rolename');
            $usermsg[0]['role'] = $role[0];

        }


        if($usermsg) {
            $arr = get_status(0,$usermsg[0]);
            return json($arr);

        }else {
            $arr = get_status(1,'获取用户失败',1018);
            return json($arr);
        }
    }

    /**
     * 获取指定用户角色
     * uid : 用户ID
     * 
     */
    public function getUserRole()
    {
//        $input = file_get_contents('php://input');
//        $input = json_decode($input,1);
        $input = input('get.');

        $uid = $input['uid'];

        $ur = $this->userModel->getMessg('user_role' , ['uid' => $uid]);

        $rid = $ur[0]['rid'];

        $role = $this->userModel->getField('role' ,['rid' => $rid] ,'rid ,rname');

        if($role) {
            $arr = get_status(0,$role[0]);
        }else {
            $arr = get_status(1,'获取用户失败',1018);
        }

        return json($arr);
    }

    /**
     * 获取指定分类
     * uid : 必须
     */

    public function getScreenGroupMessage()
    {
        $input = input('get.');
        $uid =  $input['uid'];
        //获取用户sid
        $sid = $this->userModel->getField('user',['uid' => $uid],'sid');
        $arr = [];
        if(!empty($sid[0]['sid'])) {
            $sid = $sid[0]['sid'];
            //将sid变成数组
            $sidarr = explode(',' , $sid);
            $i = 0 ;
            foreach ($sidarr as $value) {
                $screengroup = $this->userModel->getMessg('screengroup' , ['sid' => $value]);
                if($screengroup) {
                    $arr[$i] = $screengroup[0];
                }else {
                    $boj = (object)null; 
                    $arr = get_status(0,$boj);
                }
                $i++;
            }
        }
        $arr = get_status(0,$arr);
        return json($arr);
    }

    /**
     * @Notes : 获取指定分类信息
     * @access :sid 必须
     * @author : wwk
     * @Time: 2018/08/06 14:03
     */
    public  function  getScreenSid()
    {

        $input = input('get.');
        $sid = $input['sid'];
        $screen = $this->userModel->getMessg('screengroup',['sid' => $sid]);
        $arr = get_status(0,$screen[0]);
        return json($arr);
    }

    /**
     * @Notes : 获取指定权限
     * @access :pid
     * @author : wwk
     * @Time: 2018/08/07 16:28
     */

    public  function  getPermissionMessage()
    {
        $input = input('get.');

        $pid = $input['pid'];
        $screen = $this->userModel->getMessg('permission',['pid' => $pid]);
        $arr = get_status(0,$screen[0]);
        return json($arr);
    }


    /**
     * 获取用户基本信息
     */
    public function getUserMsg()
    {
        $arr = [
            'realname' => $this->user['username'],
            'role' => $this->role['rid'],
            'email' => $this->user['email'],
            'createtime' => ($this->user['createtime'] * 1000),
            'avatar' => WEB_PATH . $this->user['avatar'],
            'phone' => $this->user['phone'],
            'address' => $this->user['address'],
            'static' => [
                WEB_PATH .'/uploads/staticimg/perview_avatar_1.png',
                WEB_PATH .'/uploads/staticimg/perview_avatar_2.png' ,
                WEB_PATH .'/uploads/staticimg/perview_avatar_3.png'
            ],
        ];

        $arr = get_status(0,$arr);
        return json($arr);
    }

    //获取安全设置
    public function safeGet()
    {
        $safety = $this->userModel->getMessg('safety');

        $arr = [
            'logins' => $safety[0]['terminal'],
            'openlog' => $safety[0]['adminlog'],
            'maxerr' => $safety[0]['maxerr'],
            'locktimeset' => $safety[0]['intervaltime'],
        ];

        $arr = get_status(0,$arr);
        return json($arr);

    }

    /**
     * @Notes : 获取分类列表
     * @access :
     * @author :
     * @Time: 2018/08/09 11:30
     */
    public function getScreen()
    {
        $screen =$this->userModel->getField('screengroup', null,'sid,screenname');
        if($screen) {
            $arr = get_status(0,$screen);
        }else{
            $arr = get_status(1,'获取分类失败',1019);
        }
        return json($arr);
    }

    /**
     * @Notes : getScreenMsg 获取指定分类大屏
     * @access : sid 分类id
     * @author : Niewu
     * @Time: 2018/08/09 11:50
     */
    public function getScreenMsg()
    {

        $input = input('get.');

        $screen  = $this->userModel->getField('screen' , ['sid' => $input['sid']]);
        if($screen) {
            $arr = get_status(0,$screen);
        }else {
            $arr = get_status(1,'获取大屏失败',1020 );
        }
        return json($arr);

    }


    //获取用户权限列表
    public function getPermission($uid)
    {
        //将用户信息存入变量
        $user = $this->userModel->getUser($uid);
        if($user){
            $this->user = $user;
        }
        //将用户角色名存入变量
        // $rolename = session('rolename');
        
        //通过用户id获得角色id
        $result = $this->userModel->getRole($uid);
        if($result) {
            $rid = $result['rid'];
        }else{
             // return json_encode(['err' => 1 ,'data' => '该用户没有指定角色']);
             $data = get_status(1,'该用户没有指定角色',1004);
             return $data;
        }

        //通过角色ID获得角色名称
        $result = $this->userModel->getRolename($rid);

        //将角色名称存入用户变量
        $this->rolename = $result['rolename'];

        //通过角色ID获得权限ID
        $result = $this->userModel->getPid($rid);
        if($result) {
                $rid = $result['pid'];
            }else{
                return false;
        }

        //通过权限ID获取权限列表
        $result = $this->userModel->getPermission($rid);
        if($result) {
            //将用户权限存入变量
            return $result;
        }else{
            return false;
        }
    }


    /**
     * 获取uid
     * 获取header头中uid
     */
    public function getUid()
    {
//      $arr = get_all_header();
        $uid = $this->user['uid'];
        return $uid;
    }



}
