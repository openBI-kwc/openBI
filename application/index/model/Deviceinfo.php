<?php
namespace app\index\model;
use think\Db;

/**
 * Class Deviceinfo
 * @package app\index\model
 * 该类用于查询爱创数据 设备的和场景的数据库处理
 */

class Deviceinfo
{
    protected  $table = 'bindscenes';

    /*
     * 验证改设备是否已经绑定
     */
    public  function vailBindDevice($data)
    {
        //查询绑定信息数据
        return Db::name($this->table)->where($data)->find();
    }

    /**
     * 将绑定数据信息插入数据库
     * @param $data
     * @return bool
     */
    public  function bindDevice($data)
    {
        //绑定信息存入数据库
        return Db::name($this->table)->where("scenes" , $data['scenes'])->insert($data);
    }




}