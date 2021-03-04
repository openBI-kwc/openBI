<?php
/**
 * Created by PhpStorm.
 * User: 王伟康
 * Date: 2019/07/19
 * Time: 16:36
 */
namespace app\index\model;
use think\Db;
use traits\model\SoftDelete;
use think\Model;

class Screen extends Model
{
    use SoftDelete;
    protected $table = "up_screen";
    protected $deleteTime = 'delete_time';

    //获取指定大屏的所有信息
    public function getScreenInfo($id)
    {
        return Db::table($this->table)->where("id" , $id)->field("data,name,lock,screentype,ratio,pixel")->find();
    }

    //查询模板列表
    public function templateSummary($where,$order,$currentPage,$pageSize,$like)
    {
        //查询自定义模板
    //     if($where['tempcate'] == 1){
    //         $data = Screen::where($where)->
    //                         where('name','like','%'.$like.'%')->
    //                         order($order,'DESC')->
    //                         page($currentPage,$pageSize)->
    //                         field('id,name,screenid,imgdata,publishuser,share')->
    //                         select();
    //     $total = Screen::where($where)->where('name','like','%'.$like.'%')->count();
        
    //     }else{
    //    //查询通用模板列表
    //     $data = Screen::where('tempcate=1 AND share=1')->
    //                     whereOr('tempcate',0)->
    //                     where('screentype',2)->
    //                     where('name','like','%'.$like.'%')->
    //                     order($order,'DESC')->
    //                     page($currentPage,$pageSize)->
    //                     field('id,name,screenid,imgdata,publishuser,share')->
    //                     select();
    //     //查询列表总数
    //     $total = Screen::where('tempcate=1 AND share=1')->whereOr('tempcate',0)->where('screentype',2)->where('name','like','%'.$like.'%')->count();
    //     }
        $data = Screen::where($where)->
        where('name','like','%'.$like.'%')->
        order($order,'DESC')->
        page($currentPage,$pageSize)->
        field('id,name,screen_sid,ratio,pixel,imgdata,publishuser,share')->
        select();
    $total = Screen::where($where)->where('name','like','%'.$like.'%')->count();

        $data = ['list'=>$data,'total'=>$total];
        return $data;
    }

    //对默认模板进行软删除
    public function softdel($id)
    {
        //进行软删除
        $del = Screen::destroy($id);
        if($del){
            return $del;
        }
    }

    //恢复默认模版
    public function recsof()
    {
        $id = [];
        //查询软删除的模版
        $del = Screen::onlyTrashed()->select();
        if(empty($del)){
            return true;
        }
        foreach($del as $k=>$v){
            $id[] = $v['id'];
        }
        foreach($id as $key=>$val){
            //查询要恢复的模版
            $del = Screen::onlyTrashed()->find($val);
            //进行恢复
            $del->restore();
        }
        return true;

    }
}

