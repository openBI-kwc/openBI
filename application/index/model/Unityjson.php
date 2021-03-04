<?php
/**
 * Created by PhpStorm.
 * User: 王伟康
 * Date: 2019/06/06
 * Time: 14:36
 */
namespace app\index\model;
use think\Db;

class Unityjson
{
    protected  $table = 'unityjson';

    /**
     * @param $id
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public  function singleScenesInfo()
    {
        return Db::name($this->table)->select();
    }

    public function deleteScenes($id)
    {
        return Db::name($this->table)->where('jsonid' , $id)->delete();
    }

    /**
     * 查询所有的设备
     */
    public function getAllDevice()
    {
        return Db::name($this->table)->select();
    }

}