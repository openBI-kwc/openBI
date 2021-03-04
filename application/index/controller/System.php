<?php
namespace app\index\Controller;
use think\Db;
use think\Request;
use think\Session;

/**
 * { 变量格式输出 }
 *
 * @param      任意变量
 *
 * @return     变量结构 数据类型
 */

class System
{
	//修改密码  changePassword
    public function changePassword()
    {
        //post接收来的数据
        $dataPost = file_get_contents('php://input'); 
        $type =json_decode($dataPost,1); 
        //声明一大堆变量
        $username = $type['username'];
        $oldPassword = md5($type['oldpassword']);
        $newPassword = md5($type['newpassword']);
        //查询原始信息
        $passData = Db::name('user')->where('username',$username)->find();

        //判断原始密码是否正确
        if($oldPassword != $passData['password']){
            get_Log($username,'修改密码_旧密码错误','失败');
            $data = get_status(1,2400,NULL);
            return $data;
        }
        //判断新密码是否与旧密码相同
        if($newPassword === $passData['password']){
            get_Log($username,'修改密码_新旧密码相同','失败');
            $data = get_status(1,2401,NULL);
            return $data;
        }
        //执行更新语句
        $udata['password'] = $newPassword;

        $change = Db::name('user')->where('username',$username)->update($udata);
        
        //判断是否更新成功
        if(empty($change)){
            get_Log($username,'修改密码_更改密码执行sql','失败');
            $data = get_status(1,NULL,NULL);
            return $data;
        }else{
            get_Log($username,'修改密码_更改密码','成功');
            $data = get_status(0,NULL,NULL);
            return $data;
        }

    }

    //修改用户名 changeUsername
    public function changeUsername()
    {
        //post接收来的数据
        $dataPost = file_get_contents('php://input'); 
        $type =json_decode($dataPost,1); 


        $username = $type['username'];
        $newUsername = $type['newusername'];
        $Password = md5($type['password']);
        if($username === $newUsername){
             return get_status(1,NULL,NULL);
        }
        $data = Db::name('user')->where('username',$username)->find();

        if($Password != $data['password']){
           return get_status(1,NULL,NULL);
        }

        $return = Db::name('user')->where('username',$username)->update(['username'=>$newUsername]);



        if(empty($change)){
          return get_status(0,NULL,NULL);
        }else{
          return get_status(1,NULL,NULL);
        }

    }
    //服务器地址修改
    public function  updateIp()
    {
        //post接收来的数据
        $dataPost = file_get_contents('php://input'); 
        $type =json_decode($dataPost,1); 

        $ip = $typr['ip'];

        $update = Db::name('system')->where('id','1')->update($ip);
        return $data['err'] = '0';
    }

    //获取用户列表
    public function getUser()
    {
        $data = Db::name('user')->field('username,power')->select();

        return  get_status(0,0,$data);

    }
    //删除用户
    public  function deleteUser()
    {
        //接收post来的数据
        $dataPost = file_get_contents('php://input'); 
        //POST来的数据转换成json格式
        $post =json_decode($dataPost,1); 
        //获取用户名
        $user = $post['user'];
        //获取删除用户名
        $username = $post['username'];
        //获取超级管理员密码
        $password = md5($post['password']);
        //判断操作用户是否为超级管理员
        if($user != 'admin'){
            return get_status(1,0,NULL);
        }
        //验证超级管理员密码
        $Dbpassword = Db::name('user')->where('username', $user)->field('password')->find();
        if($Dbpassword != $password){
            return get_status(1,0,NULL);
        }

        //执行删除
        $delete = Db::name('user')->where('username',$username)->delete();
        $DbUser = Db::name('user')->field('username')->select();

        //返回删除后user列表
        return get_status(0,0,$DbUser);
    }

    //注册用户
    public function register()
    {
        //接收post来的数据
        $dataPost = file_get_contents('php://input'); 
        //POST来的数据转换成json格式
        $post =json_decode($dataPost,1); 
        //获取注册用户名
        $username = $post['username'];
        //获取注册用户的密码
        $password = md5($post['password']);
        //获取注册用户的重复密码
        $repassword = md5($post['repassword']);
        //获取注册用户的权限
        $power = $post['power'];
        //获取操作用户的用户名
        $user = $post['user'];
        //获取操作用户的密码
        $pass = $post['pass'];
        //验证操作用户身份
        if($user != 'admin'){
            return get_status(1,0,NULL);
        }
        //验证注册用户的两次密码是否相同
        if($password != $repassword){
            return get_status(1,0,NULL);
        }
        //验证用户名是否存在
        $Dbusername = Db::name('user')->where('username',$username)->find();
        if(!empty($Dnusername)){
             return get_status(1,0,NULL);
        }
        //执行添加语句
        $insertData['username'] = $username;
        $insertData['password'] = $password;
        $insertData['power'] = $power;
        $insert = Db::name('user')->insert($insertData);
        //查询数据表
        $data = Db::name('user')->select();

        //返回数据列表
        return get_status(0,0,$data);

    }
    //获取日志
    public function getLog()
    {
        $data = Db::name('log')->select();

        return get_status(0,0,$data);
    }
    //获取权限列表
    public function power()
    {
        $data = Db::name('power')->select();

        return get_status(0,0,$data);

    }
	
	

}
