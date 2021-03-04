<?php

namespace app\index\model;

use think\Db;

class User
{
    //测试 获取用户信息
    public function users()
    {
       return Db::name('user')->select();
    }

    //获取所有的权限
    public function permission()
    {
        return Db::name('permission')->select();
    }

    //获取角色的名字 $rid 角色ID
    public function getRolename($rid)
    {
        return Db::name('role')->where(['rid' =>$rid])->find();
    }

    //获取角色ID $uid 用户ID
    public function getRole($uid)
    {
        return Db::name('user_role')->where(['uid' => $uid])->find();
    }

    //获取登录用户信息 $uid 用户ID
    public function getUser($uid)
    {
        return Db::name('user')->where(['uid' => $uid])->find();
    }

    //获取权限ID  $rid 角色ID 
    public function getPid($rid)
    {
        return Db::name('role_permission')->where(['rid' => $rid])->find();
    }

    //获取权限列表 $pid 权限ID  字符串
    public function getPermission($pid)
    {
        return Db::name('permission')->order('pid', 'asc')->select($pid);
    }

    //删除用户 $table 表名  $uid 用户ID
    public function del($table , $uid)
    {
        if($uid == 1) {
            return false;
        }
        return Db::name($table)->delete($uid);
    }


    //修改用户 $table 表名  $data 数据信息  $uid 用户ID
    public  function userUpdate($table, $uid ,$data )
    {
        return Db::name($table)->where(['uid' => $uid])->update($data);
    }

    //增加用户 $table 表名  $data 数据信息
    public function userAdd($table , $data)
    {
        return Db::name($table)->insert($data);
    }

    //查询信息 $table 表名 ， $where 条件 数组or字符串
    public function getMessg($table,$where = null,$id = null , $page = "1,99999")
    {
        return Db::name($table)->where($where)->page($page)->select($id);
    } 

    //查询信息 $table 表名 ， $where 条件 数组or字符串
    public function updateMessg($table,$where,$data)
    {
        return Db::name($table)->where($where)->update($data);
    } 

    //删除信息 $table 表名 ， $where 条件 数组or字符串
    public function deleteMessg($table,$where)
    {
        return Db::name($table)->where($where)->delete();
    } 

    //查询列信息 $table 表名 ， $where 条件 数组or字符串
    public function getColumn($table,$where = null , $column = null)
    {
        return Db::name($table)->where($where)->value($column);
    }

    //SQL查询
    public function sqlExec($sql)
    {
        return Db::query($sql);
    }


    //增加用户 return 自增ID
    public function messageAdd($table,$data)
    {
        return Db::name($table)->insertGetId($data);
    }

    //查询字段 table 表名 where 条件 field 字段名
    public function getField($table,$where = null ,$field, $id = null ,$page = "0,99999" , $order = null )
    {
        return Db::name($table)->where($where)->field($field)->page($page)->order($order)->select($id);
    }

    //模糊查询 table 表名  field 字段名(str) value条件(str)
    public function messageLike($table , $field , $value)
    {
        return Db::name($table)->where($field  , 'like' , '%'.$value.'%')->select();
    }

    //计算总数
    public function countNember($table , $id)
    {
        return Db::name($table)->count($id);
    }


}