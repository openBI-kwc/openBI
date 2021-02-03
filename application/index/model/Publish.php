<?php
namespace app\index\model;
use think\Db;

/**
 * Class Publish
 * @package app\index\model
 * Publish表操作
 */
class Publish
{
    protected  $table = 'publish';

    /**查询所有发布
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public  function getAllPublish()
    {
        //查询发布列表
        return Db::name($this->table)->field('pid,sname,link')->select();
    }

    /**将发布绑定用于用户验证
     * @param $data
     * @return bool
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public  function BindUser($data)
    {
        return Db::name($this->table)->where('pid',$data['linkID'])->update(['acid' => $data['user']]);
    }

    /**验证相同
     * @param $data
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function valiSameBindUser($data)
    {
        return Db::name($this->table)->where(['acid' => $data['user'] , 'pid' => $data['linkID']])->find();
    }

    public function getWhereList($where ,$field = "*")
    {
        return Db::name($this->table)->where($where)->field($field)->select();
    }
}