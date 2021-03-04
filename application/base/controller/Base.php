<?php
/**
 * 该控制器继承Controller 如需定义操作权限直接继承该控制器
 */
namespace app\base\controller;

use app\index\model\User as UserModel;
use think\Request;
use think\Db;
use app\base\controller\Addons;

//所有接口都需要uid 用户ID  和token
class Base extends Addons
{
    //将用户信息存入变量
    protected $user;
    //将用户的角色存入变量
    protected $role;
    //将权限信息存入变量
    protected $permission;
    // 设置token
    protected $token;
    //初始化userModel
    protected $userModel;

    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        if (strtolower($_SERVER['REQUEST_METHOD']) == 'options') {
           exit();
        }
        $webPath = $_SERVER['REQUEST_SCHEME'] .'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

        if(strstr($webPath.'!!' , '/public/')) {
            $webPath = substr($webPath , 0 , strrpos($webPath , 'public'));
            $serverPath = $webPath.'public';
        }else{
            $serverPath = $_SERVER['REQUEST_SCHEME'] .'://'.$_SERVER['HTTP_HOST'];
        }
        //入口文件 域名
        define('WEB_PATH', $serverPath);
        //直接实例化request类方便后期调用
        $this->request = $request;

        $request = Request::instance();
        //获取当前模块
        $module =  strtolower($request->module());
        //获取当前控制器
        $controller = strtolower($request->controller());
        //获取当前方法
        $action = strtolower($request->action());



        //接口为index/index/index 不使用base；
        if($module ==='index' && $controller == 'index' && $action == 'index') {
           return 1;
        }
        //给定一个解救标识
        $getScreenInfo = 0;
        //接口为index/index/singlescreensummary 不使用base；
        if($module ==='index' && $controller == 'index' && $action == 'getscreeninfo') {
            //将接口标识设置为true
            $getScreenInfo = 1;
        }
        //接口为index/index/singlescreensummary 不使用base；
        if($module ==='index' && $controller == 'index' && $action == 'singlescreensummary') {
            //将接口标识设置为true
            $getScreenInfo = 1;
        }

        if(!$getScreenInfo){
            //初始化userModel
            $this->userModel = new UserModel();
            //获取uid和token
            $arr = get_all_header();
            if(isset($arr['token'])){
                $this->token = $arr['token'];    
            }else {
                $data = get_status(1,'非法用户访问' ,10001 );
                ajaxReturn($data);
            }
            $input['token'] = $arr['token'];
            $getUid = $this->userModel->getMessg('token' ,['token' => $arr['token']]);
            if(!$getUid) {
                $data = get_status(1,'Token验证失败' , 10002);
                ajaxReturn($data);
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
                    //ajaxReturn($online);
                    $userTokenDel = $this->userModel->updateMessg('user' , ['uid' => $input['uid']] , ['online' => $online]);
                    $tokendel = $this->userModel->deleteMessg('token' , ['token' => $input['token']]);
                    $data = get_status(1,'由于您长时间未操作系统自动退出' , 10003);
                    ajaxReturn($data);
                }else {
                    //token未过期 ，操作加token过期时间
                    $data  = [
                        'tokentime' => time()+720000
                    ];
                    $tokentime = $this->userModel->updateMessg('token' , ['token' => $input['token']] , $data);
                }
            }else{
                $data = get_status(1,'请先登录' , 10004);
                ajaxReturn($data);
            }

            //通过用户id获得角色id
            $result = $this->userModel->getRole($this->user['uid']);
            if($result) {
                $rid = $result['rid'];
            }else{
                $data = get_status(1,'该用户没有指定角色',1004 );
                ajaxReturn($data);
            } 

            //通过角色ID获得角色名称
            $result = $this->userModel->getRolename($rid);
            
            //将角色组存入用户变量
            $this->role = $result;

            //通过角色ID获得权限ID
            $result = $this->userModel->getPid($rid);
            if($result) {
                    $pid = $result['pid'];
                }else{
                    $arr = get_status(1,'无权限访问',0005);
                    ajaxReturn($arr);
            }
            
            //通过权限ID获取权限列表
            $result = $this->userModel->getPermission($pid);
            if($result) {
                //将用户权限存入变量
                $this->permission = $result;
            }else{
                $arr = get_status(1,'无权限访问',0005);
                ajaxReturn($arr);
            }
            
            if(!$this->token){
                $arr = get_status(1,'无权限访问',0005);
                ajaxReturn($arr);
            }



            //定义i判断条件 1为没有权限 0位通过权限验证
            $m = $module .'/'. $controller .'/'.$action;
            $i = 1;

            foreach ($this->permission as $value) {
                //判断用户有没有操作该操作的权利
                if(trim(strtolower($value['urlm'])) == strtolower($module) && trim(strtolower($value['urlc'])) == strtolower($controller) && trim(strtolower($value['urla'])) == strtolower($action)) {
                    $i = 0;
                    break;
                }
            }

            //处理lv为2的权限
            //查询接口
            $urlLv = $this->userModel->getMessg('permission' , ['urlm' =>$module , 'urlc' => $controller , 'urla' => $action]);

            //判断lv是否是2
            if($urlLv) {
                if($urlLv[0]['lv'] == 2){
                    $i = 0;
                }
            }

            if($i){
                unset($i);
                $arr = get_status(1,'没有操作权限',0006);
                ajaxReturn($arr);
            }
        }
    }


    //获取用户权限列表
    public function getPermission($uid)
    {
        //将用户信息存入变量
        $user = $this->userModel->getUser($uid);
        if($user){
            $this->user = $user;
        }
        
        //通过用户id获得角色id
        $result = $this->userModel->getRole($uid);
        if($result) {
            $rid = $result['rid'];
        }else{
             // ajaxReturn_encode(['err' => 1 ,'data' => '该用户没有指定角色']);
             $data = get_status(1,'该用户没有指定角色' , 1004  );
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

}