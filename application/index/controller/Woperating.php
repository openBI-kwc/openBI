<?php

namespace app\index\controller;

use app\base\controller\Base;
use app\index\model\User as userModel;
use think\Request;
use think\Validate;
use think\Db;

/**
 *  用于执行对用户的操作
 */
class Woperating extends Base
{
	protected $userModel;

	public function __construct(Request $request = null)
    {
        parent::__construct($request);
        //初始化userModel
        $this->userModel = new userModel();
    }


    /**
     * 增加用户组（角色）
     * rolename : 新增角色组名字
     * pid：新增角色组的权限
     * uid : 新增角色组下的用户
     * @return [data]成功与否
     */
    public function roleAdd()
    {
        $input = file_get_contents('php://input');
        // $input = '{"rolename":"aaaa","pid":"1,2,3,4,5","uid":"1","token":"a68a22217056c87ac34793a7b7bc9868"}';
        $input = json_decode($input,1);
        //获取用户组基本权限
        $basePid = $this->getBasePid();
        $input['pid'] = empty($input['pid']) ? $basePid : array_merge($basePid, $input['pid']);
        //验证角色组是否存在
        $vali  = $this->userModel->getMessg('role',['rolename' => $input['rolename']]);

        if($vali) {
            $arr = get_status(1,'角色组已存在' , 1021);
            return $arr;
        }

        //验证角色权限是否为空
        if(empty($input['pid'])){
            $arr = get_status(1,'请设置权限' , 1022);
            return $arr;
        }

        $data = [
            'rolename' => $input['rolename'],
            'createtime' => time()
        ];
        //获得自增ID
        Db::startTrans();
        try {
            $roleId = $this->userModel->messageAdd('role', $data);
            if(!$roleId) {
                throw new \Exception('添加失败');
            }

            if (!$roleId) {
                $arr = get_status(1, '增加角色组失败' ,1023);
                return $arr;
            }
            if (!empty($input['pid'])) {
                //判断pid类型
                if (is_array($input['pid'])) {
                    $pid = implode($input['pid'], ',');
                } else {
                    $pid = $input['pid'];
                }

                //角色组权限关联
                $data = [
                    'rid' => $roleId,
                    'pid' => $pid
                ];
            // dump($data);die;


                //关联权限存入数据库
                $permissionRole = $this->userModel->userAdd('role_permission', $data);

                if (!$permissionRole) {
                    throw new \Exception('添加失败');
                }
            }

            if (!empty($input['uid'])) {
                //关联用户存入数据
                //判断uid类型
                if (is_array($input['uid'])) {
                    foreach ($input['uid'] as $key => $value) {
                        $data = ['rid' => $roleId, 'uid' => $value];
                        $uesrRole = $this->userModel->userAdd('user_role', $data);
                    }
                } else {
                    $data = ['rid' => $roleId, 'uid' => $input['uid']];
                    $uesrRole = $this->userModel->userAdd('user_role', $data);
                }
                if (!$uesrRole) {
                    throw new \Exception('添加失败');
                }
            }
            
            Db::commit();
            $this->userLogs('增加角色权限成功--'.$input['rolename']);
            
            $arr = get_status(0,'增加角色组成功');
            return $arr;
        }catch (\Exception $e) {
            Db::rollback();
            $this->userLogs('增加角色权限失败--'.$input['rolename']);
            $arr = get_status(0,'增加角色组失败');
            return $arr;

        }


    }



    /**
     * 删除角色组
     * rid ： 角色组ID 必填
     */
    
    public function roleDel()
    {

        $input = $_REQUEST;
        // $input['rid']  = explode(',' , $input['rid']);
        //获取用户组ID
        $rid = $input['rid'];
        if(!is_array($rid)) {
            $msg = $this->userModel->getMessg('role',['rid' => $rid]);
        }else {
            foreach ($rid as $value) {
                $msg = $this->userModel->getMessg('role',['rid' => $value]);
            }
        }

        Db::startTrans();
            try{
                // 查询用户组是否拥有权限
                if(is_array($rid)){
                    foreach ($rid as $v) {

                        //查询用户组是否拥有角色
                        $ur = $this->userModel->getMessg('user_role' , ['rid' => $v]);
                        if($ur) {
                            //删除用户组和角色的关联
                            $deleteur = $this->userModel->getMessg('user' , ['uid' => $ur[0]['uid']]);
                            if($deleteur) {
                                Db::rollback();
                                $this->userLogs('修改用户组失败--'.$msg[0]['rolename']);
                                $arr = get_status(1,'该成员组下必须没有成员' , 1024 );
                                return json($arr);
                            }
                        }

                        if($v == 1) {
                            Db::rollback();
                            $this->userLogs('修改用户组失败--'.$msg[0]['rolename']);
                            $arr = get_status(1,'系统超级无敌管理员不能被删除' , 1025 );
                            return json($arr);
                        }
                        if($v == $this->role['rid']) {
                            Db::rollback();
                            $this->userLogs('修改用户组失败--'.$msg[0]['rolename']);
                            $arr = get_status(1,'不能删除所在权限组' , 1026 );
                            return json($arr);
                        }
                        //删除用户组记录
                        $roleDel = $this->userModel->deleteMessg('role',['rid' => $v]);
                        
                        if(!$roleDel) {
                            throw new \Exception('删除组失败');
                        }
                        $this->userLogs('修改用户组成功--'.$msg[0]['rolename']);
                        $rp = $this->userModel->getMessg('role_permission' , ['rid' => $v]);
                        if($rp){
                            //删除用户组和权限的关联
                            $deleterp = $this->userModel->deleteMessg('role_permission' , ['rid' => $v]);
                            if(!$deleterp) {
                                throw new \Exception('删除组失败');
                            }
                        }


                    }
                }else{

                    //查询用户组是否拥有角色
                    $ur = $this->userModel->getMessg('user_role' , ['rid' => $rid]);
                    if($ur) {
                        //删除用户组和角色的关联
                        $deleteur = $this->userModel->getMessg('user' , ['uid' => $ur[0]['uid']]);
                        if($deleteur) {
                            Db::rollback();
                            $this->userLogs('修改用户组失败--'.$msg[0]['rolename']);
                            $arr = get_status(1,'该成员组下必须没有成员' , 1024	 );
                            return json($arr);
                        }
                    }
                    if($rid == 1) {
                        Db::rollback();
                        $this->userLogs('修改用户组失败--'.$msg[0]['rolename']);
                        $arr = get_status(1,'系统超级无敌管理员不能被删除' , 1025	 );
                        return json($arr);
                    }
                    if($rid == $this->role['rid']) {
                        Db::rollback();
                        $this->userLogs('修改用户组失败--'.$msg[0]['rolename']);
                        $arr = get_status(1,'不能删除所在权限组' , 1026	 );
                        return json($arr);
                    }
                    //删除用户组记录
                    $roleDel = $this->userModel->deleteMessg('role',['rid' => $rid]);

                    if(!$roleDel) {
                        throw new \Exception('删除组失败');
                    }
                    $this->userLogs('修改用户组成功--'.$msg[0]['rolename']);
                    $rp = $this->userModel->getMessg('role_permission' , ['rid' => $rid]);

                    if($rp){
                        //删除用户组和权限的关联
                        $deleterp = $this->userModel->deleteMessg('role_permission' , ['rid' => $rid]);
                        if(!$deleterp) {
                            throw new \Exception('删除组失败');
                        }
                    }


                }

                Db::commit();
                $arr = get_status(0,'删除角色组成功');
                return json($arr);
            } catch (\Exception $e) {
                // 回滚事务
                Db::rollback();
                if(!is_array($rid)) {
                    $msg = $this->userModel->getMessg('role',['rid' => $rid]);
                    $this->userLogs('修改用户组成功--'.$msg[0]['rolename']);
                }else {
                    foreach ($rid as $value) {
                        $msg = $this->userModel->getMessg('role',['rid' => $value]);
                        $this->userLogs('修改用户组成功--'.$msg[0]['rolename']);
                    }
                }
                $arr = get_status(1,'删除组失败' , 1027);
                return json($arr);
            }
    }

    /**
     * [roleUpdate 修改用户组]
     * @return [type] [description]
     * @param [arr] $[rid] [用户组ID] $userid 用户ID $permissionid 权限ID
     */
    public function roleUpdate()
    {
        $input = file_get_contents(    'php://input');
        $input = json_decode($input,1);
         //获取用户组基本权限
        $basePid = $this->getBasePid();
        $input['pid'] = empty($input['pid']) ? $basePid : array_merge($basePid, $input['pid']);
        //验证角色权限是否为空
        if(empty($input['rid'])){
            $arr = get_status(1,'非法设置' , 1022	);
            return $arr;
        }
        $pid = implode($input['pid'], ',');
        $rid = $input['rid'];
        $msg = $this->userModel->getMessg('role',['rid' => $rid]);

        //设定超级管理员能否被修改
        if($rid == 1) {
             $this->userLogs('修改用户组失败--'.$msg[0]['rolename']);
             $arr = get_status(1,'系统超级无敌管理员不能被修改' , 1025 );
             return json($arr);
         }

        $valirp = $this->userModel->getMessg('role_permission',['rid' => $rid , 'pid' => $pid]);

        $valirole = $this->userModel->getMessg('role' , ['rolename' => $input['rolename']]);

         Db::startTrans();
            try{
                //修改用户组权限
                if(!$valirp){
                    $rp = $this->userModel->updateMessg('role_permission',['rid' => $rid] , ['pid' => $pid]);
                    if(!$rp) {
                        throw new \Exception('修改权限失败');
                    }
                }

                if(!$valirole){
                    $rolenameEdit = $this->userModel->updateMessg('role',['rid' => $rid],['rolename' => $input['rolename']]);

                }else if($valirole[0]['rid'] == $rid){
                    $rolenameEdit = $this->userModel->updateMessg('role',['rid' => $rid],['rolename' => $input['rolename']]);

                }else{
                    Db::rollback();
                    $this->userLogs('用户组已存在--'.$msg[0]['rolename']);
                    $arr = get_status(1,'用户组已存在' , 1028  );
                    return json($arr);
                }


                Db::commit();
                $this->userLogs('修改用户组成功--'.$msg[0]['rolename']);
                $arr = get_status(0,'修改成功');
                //将日志中的用户名修改
                Db::name('userlog')->where(['rid' =>$input['rid']])->update(['userrole' => $input['rolename']]);
                return json($arr);
            }catch (\Exception $e) {
                // 回滚事务
                Db::rollback();
                $this->userLogs('修改用户组失败--'.$msg[0]['rolename']);
                $arr = get_status(1,'修改用户组失败' , 1029 );
                return json($arr);
            }




    }


    /**
     * [userAdd 增加用户]
     * @return [type] [description]
     * @param username 用户名   password 密码  rpassword 重复密码
     */
    public function userAdd()
    {
        $input =  file_get_contents('php://input');
        $input = json_decode($input,1);
        $input['pwd'] = decrypt($input['pwd'] , $input['pwdlen']);
        // 验证账号密码是否为空
        $validate = new Validate([
           'username' => 'require',
           'password' => 'require',
        ]);

        $data = [
          'username' => $input['username'],
          'password' => $input['pwd'],
        ];

        if(!$validate->check($data)) {
            $arr  = get_status(1,'用户名或密码不能为空' , 1012);
            return $arr;
        }



        // $result = $this->userModel->getMessge('user');
        $getname = $this->userModel->getMessg('user' , ['username' => $input['username']]);
        if($getname){
            $this->userLogs('用户已存在--'.$input['username']);
            $arr = get_status( 1 , '用户已存在' , 1030);
            return $arr;
        }
        //用户权限组关联
        $data = [];
        //设置基本权限，第一个用户为超级管理员 
        $result = $this->userModel->getMessg('user');
        //定义基本权限
        
        //!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!角色组
        
        $role = $input['character'];


        if($result){
            $data['rid'] = $role;
        }else {
            $data['rid'] = 1;
        }
        
        if(empty($input['adminPer'])){
            $arr = get_status( 1 , '用户分类权限不能为空',1031);
            return $arr;
        }

        //盐
        $salt = mt_rand(1000999,9999999);
        //处理sid
        $sid = implode(',' , $input['adminPer']);
        //用户信息
        $datas = [
            'username' => $input['username'],
            'password' => md5($input['pwd'].$salt),
            'email' => $input['mail'],
            'realname' => $input['name'],
            'address' => $input['adress'],
            'phone' => $input['tel'],
            'status' => $input['status'],
            'salt' => $salt,
            'createtime' => time(),
            'logins' => $this->user['logins'],
            'locktimeset' => $this->user['locktimeset'],
            'sid' => $sid
        ];

        Db::startTrans();
            try{
                $result = $this->userModel->messageAdd('user',$datas);
                if(!$result) {
                    throw new \Exception('添加失败');
                }
                if($result){
                    $data['uid']  = $result;
                }
                $results = $this->userModel->messageAdd('user_role',$data);
                if(!$results) {
                    throw new \Exception('添加失败');
                }
                // 提交事务
                Db::commit();
                $this->userLogs('添加用户成功--'.$input['username']);
                $arr = get_status( 0 , '添加成功');
                return $arr;
            } catch (\Exception $e) {
                // 回滚事务
                Db::rollback();
                $this->userLogs('添加用户失败--'.$input['username']);
                $arr = get_status( 1 , '添加失败' , 1032);
                return $arr;
            }

    }


    /**
     * 用户修改
     * uid ： 用户id  必须
     * 
     * @return
     */
    public function userUpdate()
    {
        $input = file_get_contents('php://input');
        $input = json_decode($input,1);

        $msg =  $this->userModel->getMessg('user' , ['uid' => $input['uid']]);
        if($input['uid'] == 1){
            $role = $this->userModel->getMessg('user_role' , ['uid' => $input['uid']]);
            $rid = $role[0]['rid'];
            $rolename = $this->userModel->getMessg('role' , ['rid' => $rid ]);
            $name = $this->userModel->getMessg('role' , ['rid' => $input['character'] ]);
            $rname = $rolename[0]['rolename'];
            $name = $name[0]['rolename'];
            if($name != $rname) {
                $this->userLogs('修改用户失败--'.$msg[0]['username']);
                $arr = get_status(1,'该用户为超级管理员，不能修改权限' , 1033 );
                return json($arr);
            }
            if($input['status']){
                $this->userLogs('修改用户失败--'.$msg[0]['username']);
                $arr = get_status(1,'该用户为超级管理员，不能被禁用' , 1034 );
                return json($arr);
            }
        }

//       开启事物
//       Db::startTrans();

       //处理sid
        if(empty($input['adminPer'])) {
            $arr = get_status( 1 , '用户分类权限不能为空',1031);
            return $arr;
        }

        $sid = implode(',' , $input['adminPer']);

        $data = [
            'username' => $input['username'],
            'email' => $input['mail'],
            'realname' => $input['name'],
            'address' => $input['adress'],
            'phone' => $input['tel'],
            'status' => $input['status'],
            'sid' => $sid
        ];

        if($data['status'] > 0) {
            $data['locktime'] = time()+(86400 * 3650);
        }
        //设置盐
        if(isset($input['pwd'])){
            $onlineVali = $this->userModel->getField('user',['uid' => $input['uid']],'online');
            if($onlineVali[0]['online'] > 1) {
                $this->userLogs('当前多终端登录修改密码失败--'.$msg[0]['username']);
                $arr = get_status(1,'多个用户登录',3015);
                return json($arr);
            }else {
                $salt = mt_rand(1000999,9999999);
                $input['pwd'] = decrypt($input['pwd'] , $input['pwdlen']);
                $data['password'] = md5($input['pwd'].$salt);
                $data['salt'] = $salt;
            }

        }
        $vali = $this->userModel->getMessg('user',$data);

        if($vali && $input['uid'] != $vali[0]['uid']) {
            $this->userLogs('修改用户参数一致--'.$msg[0]['username']);
            $arr = get_status(0,'修改成功');
            return json($arr);
        }

        $vali = $this->userModel->getMessg('user' , ['username' => $data['username']]);

        if($vali && $vali[0]['uid'] != $input['uid']){
            $this->userLogs('修改用户失败--'.$msg[0]['username']);
            $arr = get_status(1,'用户已存在',1030);
            return json($arr);

        }

        $result = $this->userModel->updateMessg('user',['uid' => $input['uid']] , $data);

        $rid = $input['character'];
        $ur = [
            'uid' => $input['uid'],
            'rid' => $rid,
        ];

        //验证用户角色
        $valiUserRole = $this->userModel->getMessg('user_role',['rid' => $rid,'uid' => $input['uid']]);

        if(!$valiUserRole) {
            $urUpd = $this->userModel->updateMessg('user_role' , ['uid' => $input['uid']], $ur);
            if(!$urUpd) {
                $this->userLogs('修改用户失败--'.$msg[0]['username']);
                $arr = get_status(1 , '修改用户失败',1036);
                return json($arr);
            }
        }

        

        $this->userLogs('修改用户成功--'.$msg[0]['username']);
        $arr = get_status(0,'修改用户成功');

        //查询更改后的角色
        $rolena = Db::name('role')->where(['rid'=>$input['character']])->value('rolename');
        //将日志中的用户名修改
        $update = Db::name('userlog')->where(['uid' =>$input['uid']])->update(['username' => $input['username'],'userrole'=>$rolena,'realname'=>$input['name']]);
        return json($arr);
    }

    /**
     * 用户删除
     * uid : 必填
     * @return [type] [description]
     */
    public function userDel()
    {
        $input = $_REQUEST;
        if(is_string($input['uid'])) {
            $input['uid']  = explode(',' , $input['uid']);
        }
        foreach ($input['uid'] as $key => $val) {
            $msg =  $this->userModel->getMessg('user' , ['uid' => $val]);
            if($val == $this->user['uid']) {
                $this->userLogs('删除用户失败--'.$msg[0]['username']);
                $arr = get_status(1,'不能删除自己' ,1037);
                return json($arr);
            }
            if($val == 1) {
                $this->userLogs('删除用户失败--'.$msg[0]['username']);
                $arr = get_status(1,'该用户为超级管理员用户不可删除' ,1025);
                return json($arr);
            }
            Db::startTrans();
            try{
                $deluser = $this->userModel->deleteMessg('user',['uid' => $val]);
                if(!$deluser) {
                    throw new \Exception('删除用户失败');
                }
                $delur = $this->userModel->deleteMessg('user_role' , ['uid' => $val]);
                if(!$delur) {
                    throw new \Exception('删除用户失败');
                }

                $this->userModel->deleteMessg('token' ,['uid' => $val]);
                //查询分类列表
                Db::commit();
                $this->userLogs('删除用户成功--'.$msg[0]['username']);
                $arr = get_status(0 , '用户删除成功');
            } catch (\Exception $e) {
                // 回滚事务
                Db::rollback();
                $this->userLogs('删除用户失败--'.$msg[0]['username']);
                $arr = get_status(1,'删除用户失败' ,1038);
            }
        }


        return json($arr);
    }

    /**
     * 添加分类
     * screenname 类名
     * remarks 备注
     */
    public function classAdd()
    {   
        $input = file_get_contents('php://input');
        $input = json_decode($input,1);
        $data['screenname'] = $input['screenname'];
        $data['remarks'] = $input['remarks'];


        //查询是否已有该条分类
        $screenname = $this->userModel->getMessg('screengroup' , ['screenname' => $data['screenname']]);
        if($screenname){
            $this->userLogs('添加分类失败--重复添加');
            $arr = get_status(1,'分类已存在' , 1039);
            return json($arr);
        }

        $result = $this->userModel->messageAdd('screengroup',$data);

        //查询超级管理员用户
        $superUser = $this->userModel->getMessg('user_role',['rid' => 1]);
        $superUserIds = [];
        foreach ($superUser as $key => $value) {
            //查询超级用户sid
            $sid = $this->userModel->getField('user',['uid' => $value['uid']],'sid')[0];
            if(empty($sid)) {
                $userSid = $result;
            }else {
                $userSid = $sid['sid'].','.$result;
            }
            $res = $this->userModel->updateMessg('user' , ['uid' => $value['uid']] , ['sid' => $userSid]);
        }

        if($result) {
            $this->userLogs('添加分类成功--'.$data['screenname']);
            $arr = get_status(0,'添加分类成功');
            return json($arr);
        }
    }

    /**
     * 删除分类
     * sid : 分类ID 必须
     * @return [type] [description]
     */
    public function classDel()
    {

        $input = $_REQUEST;

        if($input['sid'] == 1) {
            $arr = get_status(1,'基础分类不能被删除',1040);
            return json($arr);
        }
        $data = $this->userModel->getMessg('screen',['sid' => $input['sid']]);
        if($data) {
            $this->userLogs('删除分类失败,分类下有大屏--');
            $arr = get_status(1,'分类下有大屏',1041);
            return json($arr);
        }
        $data = $this->userModel->getMessg('databasesource',['sid' => $input['sid']]);
        if($data) {
            $this->userLogs('删除分类失败,分类下有数据库源--');
            $arr = get_status(1,'分类下有数据库源',1049);
            return json($arr);
        }
        $sname = Db::name('screengroup')->where('sid' , $input['sid'] )->find();
        $data = $this->userModel->getMessg('datament',['cid' => $sname['screenname']]);
        if($data) {
            $this->userLogs('删除分类失败,分类下有数据源--');
            $arr = get_status(1,'分类下有数据源',1050);
            return json($arr);
        }


        $user = $this->userModel->getMessg('user');
        foreach ($user as $key =>$value) {
            if($value['sid'] == $input['sid']) {
                $user[$key]['sid'] = ' ';
            }else {
                $user[$key]['sid'] = trim(str_replace(','.$input['sid'] , '' , ','.$value['sid']) , ',');
            }
            $userUpdate = $this->userModel->updateMessg('user' , ['uid' => $value['uid']] , $user[$key]);
        }
        $screengroup = $this->userModel->getMessg('screengroup',['sid' => $input['sid']]);
        $result = $this->userModel->deleteMessg('screengroup' , ['sid' => $input['sid']]);

        if($result){
            $this->userLogs('删除分类成功--'.$screengroup[0]['screenname']);
            $arr = get_status(0,'删除分类成功');
            return json($arr);
        }else{
            $this->userLogs('删除分类失败--'.$screengroup[0]['screenname']);
            $arr = get_status(1,'删除分类失败',1042);
            return json($arr);
        }
    }


    /**
     * 重命名分类(修改分类)
     * screenname : 重命名名字 必须
     * remarks : 备注
     * sid : 分类ID 必须
     */
    public function reName()
    {
        $input = file_get_contents('php://input');
        // $input = '{"sid":"3","screenname":"重命名分类","remarks":"重命名分类备注"}';
        $input = json_decode($input,1);
        $vali = $this->userModel->getMessg('screengroup' , ['screenname' => $input['screenname']]);

        if($vali && $input['sid'] != $vali[0]['sid']) {
            $this->userLogs('修改分类失败--'.$input['screenname']);
            $arr = get_status(1,'分类已存在' , 1039);
            return json($arr);
        }
        //修改分类的同时，修改数据管理表
        Db::name('datament')->where('ccid',$input['sid'])->update(['cid'=>$input['screenname']]);


        $data = [
            'screenname' => $input['screenname'],
            'remarks' => $input['remarks'],
        ];
        $results = $this->userModel->getMessg('screengroup' , $data);
        if($results) {
            $this->userLogs('修改分类成功--'.$input['screenname']);
            $arr = get_status(0,'修改分类成功');
            return json($arr);
        }

        $result = $this->userModel->updateMessg('screengroup' , ['sid' => $input['sid']] , $data);


        if($result) {
            $this->userLogs('修改分类成功--'.$input['screenname']);
            $arr = get_status(0,'修改分类成功');
            return json($arr);
        }else{
            $this->userLogs('修改分类失败--'.$input['screenname']);
            $arr = get_status(1,'修改分类失败' , 3008);
            return json($arr);
        }
    }

    
    /**
     * 增加权限
     * pname : 权限名
     * lv : 权限等级
     * urlm : 模块 (默认index)
     * urlc : 控制器
     * urla : 方法名
     * parentid : 父级ID （0位顶级）
     * path ： vue路径
     * identification : 权限标识
     * remarks : 备注
     */

    public function permissionAdd()
    {

        $input = file_get_contents('php://input');
        $input = json_decode($input,1);
        if($input['lv'] == 0 ) {
                $data  = [
                    'pname' => $input['pname'],
                    'lv' => $input['lv'],
                    'parentid' => 0,
                    'identification' => $input['identification'],
                    'remarks' => $input['remarks'],

                ];

                //验证权限是否存在
            $result = $this->userModel->getMessg('permission' ,['pname' => $input['pname'],'lv' => $input['lv'] ]);

            if($result) {
                $this->userLogs('新增权限失败--已存在权限');
                $arr = get_status(1,'已存在权限' , 1044 );
                return json($arr);
            }

            $result = $this->userModel->messageAdd('permission' , $data);
            if($result) {
                $this->userLogs('新增权限成功--'.$data['pname']);
                $arr = get_status(0,'新增权限成功');
                return json($arr);
            }else {
                $this->userLogs('新增权限失败--存入数据库失败');
                $arr = get_status(1,'存入数据库失败' , 1045);
                return json($arr);
            }
        }

        if($input['lv'] != 0) {
            $url = explode('/' , $input['curl']);
            if(!isset($url[0])){
                $this->userLogs('新增权限失败--路径参数不正确');
                $arr = get_status(1,'路径参数不正确',1046);
                return json($arr);
            }

            if(!isset($url[1])){
                $this->userLogs('新增权限失败--路径参数不正确');
                $arr = get_status(1,'路径参数不正确',1046);
                return json($arr);
            }
            if(!isset($url[2])) {
                $this->userLogs('新增权限失败--路径参数不正确');
                $arr = get_status(1,'路径参数不正确', 1046);
                return json($arr);
            }

            $data  = [
                'pname' => $input['pname'],
                'lv' => $input['lv'],
                'urlm' => $url[0],
                'urlc' => $url[1],
                'urla' => $url[2],
                'parentid' => $input['parentid'],
                'path' => $input['path'],
                'identification' => $input['identification'],
                'remarks' => $input['remarks'],

            ];
            //验证权限是否存在
            $result = $this->userModel->getMessg('permission' ,['pname' => $input['pname'],'lv' => $input['lv'] ]);
            if($result) {
                $this->userLogs('新增权限失败--已存在权限');
                $arr = get_status(1,'已存在权限' , 1044	 );
                return json($arr);
            }
            //验证接口是否存在
            $result = $this->userModel->getMessg('permission' ,['urlc' => $url[0] , 'urla' => $url[1] , 'urlm' => $url[2]]);
            if($result) {
                $this->userLogs('新增权限失败--已存在的方法');
                $arr = get_status(1,'已存在的方法' , 1047);
                return json($arr);
            }

            $result = $this->userModel->messageAdd('permission' , $data);
            if($result) {
                $data['pname'] = '查询'.$input['pname'];
                $data['parentid'] = $result;
                if($data['lv'] == 1) {
                    $data['lv'] = 3;
                    $result = $this->userModel->messageAdd('permission' , $data);
                }
                if($result) {
                    $this->userLogs('新增权限成功--'.$data['pname']);
                    $arr = get_status(0,'新增权限成功');
                    return json($arr);
                }else {
                    $this->userLogs('新增权限失败--存入数据库失败');
                    $arr = get_status(1,'存入数据库失败' , 1045	);
                    return json($arr);
                }

            }else {
                $this->userLogs('新增权限失败--存入数据库失败');
                $arr = get_status(1,'存入数据库失败' , 1045	);
                return json($arr);
            }
        }





    }


    /**
     * 修改权限
     * pid : 权限ID
     * pname : 权限名
     * lv : 权限等级
     * urlm : 模块 (默认index)
     * urlc : 控制器
     * urla : 方法名
     * parentid : 父级ID （0位顶级）
     * path ： vue路径
     * identification : 权限标识
     * remarks : 备注
     */
    
    public function permissionUpdate()
    {
        $input = file_get_contents('php://input');

        $input = json_decode($input,1);

        $url = explode('/' , $input['curl']);

        if(!isset($url[0])){
            $this->userLogs('新增权限失败--路径参数不正确');
            $arr = get_status(1,'路径参数不正确' , 1046	);
            return json($arr);
        }

        if(!isset($url[1])){
            $this->userLogs('新增权限失败--路径参数不正确');
            $arr = get_status(1,'路径参数不正确',1046	);
            return json($arr);
        }
        if(!isset($url[2])) {
            $this->userLogs('新增权限失败--路径参数不正确');
            $arr = get_status(1,'路径参数不正确',1046	);
            return json($arr);
        }


        $pid = $input['pid'];
        $data  = [
            'pname' => $input['pname'],
            'lv' => $input['lv'],
            'urlm' => $url[0],
            'urlc' => $url[1],
            'urla' => $url[2],
            'parentid' => $input['parentid'],
            'path' => $input['path'],
            'identification' => $input['identification'],
            'remarks' => $input['remarks'],

        ];

        $result = $this->userModel->getMessg('permission' ,['pname' => $input['pname'] ,'lv' => $input['lv']]);

        if($result && $result[0]['pid'] != $input['pid']) {
            $this->userLogs('新增权限失败--已存在权限');
            $arr = get_status(1,'已存在权限' , 1044	);
            return json($arr);
        }

        $result = $this->userModel->getMessg('permission' ,['urlc' => $url[0] , 'urla' => $url[1] , 'urlm' => $url[2]]);
        if($result && $result[0]['urlc'] != $url[0] && $result[1]['urlc'] != $url[1] && $result[2]['urlc'] != $url[2]) {
            $this->userLogs('新增权限失败--已存在的方法');
            $arr = get_status(1,'已存在的方法' , 1047);
            return json($arr);
        }

        //查询数据库内书否有同样的数据
        $result = $this->userModel->getMessg('permission' , $data);
        if($result) {
            $this->userLogs('修改权限失败--未变更参数');
            $arr = get_status(1,'未变更参数' ,1048);
            return json($arr);
        }
        //修改I数据库
        $result = $this->userModel->updateMessg('permission' , ['pid' => $pid] , $data);
        if(!$result) {
            $this->userLogs('修改权限失败--修改数据库失败');
            $arr = get_status(1,'修改数据库失败',3012);
            return json($arr);
        }else {
            $this->userLogs('修改权限成功--' . $input['pname']);
            $arr = get_status(0,'修改权限成功',1014	);
            return json($arr);
        }
        
    }

    /**
     * 删除权限
     * pid : 权限ID
     * pname ： 权限名字
     */
    public function permissionDel()
    {
        $pid = $_REQUEST['pid'];
        $pid = explode(',' , $pid);

        foreach ($pid as $value) {
            $del = $this->removePermission($value);
            $result = $this->userModel->getMessg('permission' ,['pid' => $value]);
            $this->userLogs('删除权限成功--'.$result[0]['pname']);

        }

        if($del) {
            $arr = get_status(0,'删除权限成功');
            return json($arr);
        }else {
            $this->userLogs('删除权限失败--修改数据库失败');
            $arr = get_status(1,'删除数据库失败' ,1014);
            return json($arr);
        }


    }


    //递归删除权限
    public function removePermission($pid)
    {
        //删除记录
        $del = $this->userModel->deleteMessg('permission',['pid' =>$pid]);
        //查询角色关联
        $result =  $this->userModel->messageLike('role_permission' , 'pid' , $pid);
        //删除关联
        if($result) {
             foreach($result as $key => $value) {
             $result[$key]['pid'] = str_replace(",$pid" ,"" ,$value['pid']);
            }
            //存入数据库
            foreach ($result as $key => $value) {
                $result = $this->userModel->updateMessg('role_permission',['rpid' => $value['rpid']] , $value);
            
            }
        }
        
        //查询子记录
        $result = $this->userModel->getMessg('permission' , ['parentid' => $pid]);
        if($result) {
            foreach ($result as $key => $value) {
                $pid = $value['pid'];
                //调用自己实现递归
                $this->removePermission($pid);
            }
        }

        return true;
    }


    /**
     * 修改安全设置
     * uid : 用户ID 必须
     * errors : 最大错误登录次数
     * locktime : 错误间隔时间
     * loigns : 最大允许登录终端
     * openlog : 后台管理日志(0 or 1)
     * onOff : 是否允许多人同时在线(0 or 1)
     */

    public function safeSet()
    {
        $input = file_get_contents('php://input');
        $input = json_decode($input,1);

        $data = [
           'terminal' => $input['logins'],
           'adminlog' => $input['openlog'],
           'maxerr' => $input['maxerr'],
           'intervaltime' => $input['locktimeset'],
        ];
        //查询是否一致
        $re = Db::name('safety')->where($data)->find();
        if($re){
            $arr = get_status(0,'修改成功' , 5001	);
            return json($arr);
        }
        $result = Db::name('safety')->where(['said' => '1'])->update($data);

        if($result) {
          $this->userLogs('修改安全设置成功--'.$this->user['username']);
          $arr = get_status(0,'修改设置成功');
          return json($arr);
        }else {
          $this->userLogs('修改安全设置失败--'.$this->user['username']);
          $arr = get_status(1,'修改设置失败' , 1014	);
          return json($arr);
        }

    }


    /**
     * @Notes : 修改基本信息
     * @access : 地址address   电话phone   邮箱email
     * @author :
     * @Time: 2018/08/06 18:42
     */
    public function updateBasicMessage()
    {
        $input = file_get_contents('php://input');
        $input = json_decode($input,1);

        $input['uid'] = $this->user['uid'];

//        dump($input);exit();


        $data = [
            'address' => $input['address'],
            'phone' => $input['phone'],
            'email' => $input['email'],
        ];
        $re = Db::name('user')->where($data)->where(['uid'=>$input['uid']])->find();
        if($re){
            $arr = get_status(0,'修改成功' ,5001);
            return json($arr);
        }
        $result = $this->userModel->updateMessg('user' , ['uid' => $input['uid']] , $data);
        if($result) {
            $this->userLogs('修改安全设置成功--'.$this->user['username']);
            $arr = get_status(0,'修改设置成功');
            return json($arr);
        }else {
            $this->userLogs('修改安全设置失败--'.$this->user['username']);
            $arr = get_status(1,'修改设置失败' ,1014);
            return json($arr);
        }
    }

    /**
     * 获取用户基本的权限
     * @param
     */
    protected function getBasePid()
    {
        $where['pid'] = ['NOT IN' , [3,7,8,9,10,12,13,14,15,16,17,18]];
        $res = $this->userModel->getField('permission',$where , 'pid');
        $pid = array_column($res , 'pid');
        //返回数据
        return  $pid;
    }

    //记录操作日志
    //$user : 用户信息
    //$tole : 角色组名
    //$msg : 操作信息
    public function userLogs($msg)
    {
        $safety = $this->userModel->getMessg('safety');
        if($safety){
            $safety = $safety[0];
        }
        
      if($safety['adminlog']){
        //获取访问ip
        $ip = $_SERVER["REMOTE_ADDR"];
        $ip = ip2long($ip);
        //日志记录
        $data = [
            'uid' => $this->user['uid'],
            'realname' => $this->user['realname'],
            'username' => $this->user['username'],
            'userrole' => $this->role['rolename'],
            'lastlogintime' => $this->user['lastlogintime'],
            'ip' => $ip,
            'operating' => $msg,
            'rid' => $this->role['rid']
        ];
        //保存日志
        $this->userModel->messageAdd('userlog' , $data);

    }
  }


}