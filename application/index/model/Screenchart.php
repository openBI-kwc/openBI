<?php
/**
 * Created by PhpStorm.
 * User: 王伟康
 * Date: 2019/06/27
 * Time: 18:27
 */
namespace app\index\model;
use think\Db;

class Screenchart
{
    protected $table = "Screenchart";


    //查询所有
    public function getAllScreenChart()
    {
        return Db::name($this->table)->select();
    }

    //获取总数
    public function getCount()
    {
        return Db::name($this->table)->count();
    }

    //分页查询

    public function pageSelect( $p  , $pageNum)
    {
        return Db::name($this->table)->field("tid,tconfig")->page($p,$pageNum)->select();
    }

    public function updateScreenChart($id, $data)
    {
        return Db::name($this->table)->where("tid",$id)->update($data);
    }

    //获取指定大屏下的所有图表
    public function getScreenList($id)
    {
        return Db::name($this->table)->where("screenid" , $id)->field("tid,screenid,talias,tname,tdata,islock,
                        link,collection,ishide,autoupdatetime,daid")->select();
    }

}