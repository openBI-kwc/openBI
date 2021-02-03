<?php
/**
 * Created by PhpStorm.
 * User: 王伟康
 * Date: 2019/11/05
 * Time: 9:25
 */
namespace app\index\model;
use think\Db;
use think\Model;
class Gisdata extends Model
{
    protected $table = "gisdata";

    //获取增加图表时的gisdata
    public function getAddGisdata($where)
    {
        return Db::name($this->table)->where('gistype' , 'in' , $where)->column('gisdata','gistype');
    }
}