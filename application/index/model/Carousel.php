<?php
/**
 * Created by PhpStorm.
 * User: 王伟康
 * Date: 2019/08/02
 * Time: 11:10
 */
namespace app\index\model;
use think\Db;

class Carousel
{
    protected  $table = 'carouselrelease';

    //生成一个ID
    public function getInsertID($data)
    {
        if(empty($data['animation'])){
            return false; 
        }
        if(empty($data['cname'])){
            return false; 
        }
        if(empty($data['controlPos'])){
            return false; 
        }
        if(empty($data['crIdent'])){
            return false; 
        }
        if(empty($data['crLink'])){
            return false; 
        }
        if(empty($data['remarks'])){
            return false; 
        }
        if(empty($data['screens'])){
            return false; 
        }
        if(empty($data['time'])){
            return false; 
        }
        return Db::name($this->table)->insertGetId($data);
    }

    //修改信息
    public function updateCarousel($data , $where)
    {
        if(empty($data['animation'])){
            return false; 
        }
        if(empty($data['cname'])){
            return false; 
        }
        if(empty($data['controlPos'])){
            return false; 
        }
        if(empty($data['crIdent'])){
            return false; 
        }
        if(empty($data['crLink'])){
            return false; 
        }
        if(empty($data['createtime'])){
            return false; 
        }
        if(empty($data['remarks'])){
            return false; 
        }
        if(empty($data['screens'])){
            return false; 
        }
        if(empty($data['time'])){
            return false; 
        }
        if(empty($data['updatetime'])){
            return false; 
        }
        return Db::name($this->table)->where($where)->update($data);
    }

    //通过条件获取详情信息列表
    public function getCarouselList($where)
    {
        return Db::name($this->table)->where($where)->find();
    }

    //获取全部详情信息列表
    public function getCarouselListAll($where = null,$currentPage = null,$pageSize = null,$order = null)
    {
        return Db::name($this->table)->order($order,'desc')->where('cname','like','%'.$where.'%')->page($currentPage,$pageSize)->select();
    }

    public function deleteCarousel($where)
    {
         return Db::name($this->table)->where($where)->delete();

    }
}