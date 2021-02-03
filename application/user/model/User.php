<?php
/**
 * @File name: User.php
 * @access :
 * @author : wwk
 * @Time: 2018/07/17 11:31
 */
namespace app\user\model;

use think\Model;
use think\Controller;
use think\Db;

class User extends Controller
{
   
    //验证用户名密码
    public function vali($data , $where)
    {
        $result = Db::name('user')->where($where)->find();
        if($result){
            return $result;
        }else{
            return false;
        }
    }

    //获取用户组
    public function getRole($uid)
    {
        $result = Db::name('user_role')->where(['uid' => $uid])->find();
        if($result){
            return $result;
        }else{
            return false;
        }
    }

    //获取权限名字
    public function getPid($rid)
    {
        $result = Db::name('role_permission')->where(['rid' => $rid])->find();
        if($result){
            return $result;
        }else{
            return false;
        }
    }

    //获取用户组名
    public function getRolename($rid)
    {
        $result = Db::name('role')->where(['rid' =>$rid])->find();
        if($result){
            return $result;
        }else{
            return false;
        }
    }

    //获取权限
    public function getPermission($pid)
    {
        $result = Db::name('permission')->select($pid);
        if($result){
            return $result;
        }else{
            return false;
        }
    }

    //插入 $table 表名  $data  数据
    public function messgeAdd( $table , $data)
    {
        $result = Db::name($table)->insert($data);
        if($result) {
            return $result;
        }else  {
            return false;
        }
    }


    //修改 $table 表名 $where 条件 $data 数据
    public function messgeUptate( $table , $where , $data )
    {
        
        $result = Db::name($table)->where($where)->update($data);
        if($result) {
            return $result;
        }else {
            return false;
        }
    }

    //删除 $table 表名 $where 条件 
    public function messgeDlete($table , $where)
    {
        $result = Db::name($table)->where($where)->delete();
        if($result) {
            return $result;
        }else {
            return false;
        }
    }

    //获取信息 $table 表名  $where 条件 $column  字段
    public function getMessge($table , $where = null )
    {
        $result = Db::name($table)->where($where)->select();
        if($result) {
            return $result;
        }else {
            return false;
        }
    }

    //删除信息 $table 表名 ， $where 条件 数组or字符串
    public function deleteMessg($table,$where)
    {
        $result = Db::name($table)->where($where)->delete();
        if($result) {
            return $result;
        }else {
            return false;
        }
    } 


    //增加用户 return 自增ID
    public function messageAdd($table,$data)
    {
        $result = Db::name($table)->insert($data);
        $userId = Db::name($table)->getLastInsID();
        if($result && $userId) {
            return $userId;
        }else {
            return false;
        }
    }


}

