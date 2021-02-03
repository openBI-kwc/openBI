<?php

namespace app\release\Controller;

use think\Db;

/**
 * 控制器用于展示发布页面
 */
class Index
{
    public $rollPage = 8; //分页栏每页显示的页数

    public function index()
    {
        $static = DS."static";
        //查询全部发布列表
        $result = Db::name('publish')->field('pid,viewsnum,ispuh,sname,is_pwd,img,token,expiredate,link,createtime,ptype')
            ->paginate($this->rollPage);

        $count = count($result);

        return $this->fetch('index' , ['static' => $static,'result' => $result,'count' => $count]);
    }

    /**
     * 验证发布密码
     */
    public function valiView()
    {
        //接收数据
        $input = input('get.');

        //判断数据是否接收成功
        if(!$input) {
            return jsonRetuen(1,'数据接收失败' , 2000);
        }
        $keys = ["pid"];
        //验证传值
        $vali = valiKeys($keys , $input);
        //判断传值是否满足
        if($vali['err'] != 0) {
            return jsonRetuen(1,$vali['data'] , 2000);
        }
        //查询相关发布
        $result = Db::name('publish')->where('scid',$input['pid'])->find();
        //判断查询是否为空
        if(empty($result)){
            return jsonRetuen(1,'未找到发布信息' ,6005);
        }
        //判断是否需要验证token
        if(!empty($result['token'])){
            //判断是否有token传入
            if(isset($input['token'])) {
                //验证token
                if($result['token'] != $input['token']) {
                    return jsonRetuen(1,'发布token验证失败' , 6006);
                }
            }else {
                return jsonRetuen(1,'非法进入页面' , 6007);
            }
        }
        //验证是否已过期
        if($result['expiredate'] < time() && $result['expiredate'] != 0) {
            return jsonRetuen(1,'发布链接已过期',6008);
        }

        //判断是否开启密码
        if($result['is_pwd'] == 0) {
            return jsonRetuen(0,'未开启密码保护');
        }

        //验证password是否传入
        $keys = ["password"];
        //验证传值
        $vali = valiKeys($keys , $input);

        //判断是否有password传入
        if(!isset($input['password'])) {
            return jsonRetuen(1,'发布链接访问密码错误' , 6009);
        }

        //判断密码是否一致
        if(decrypt($input['password'],$input['len']) == $result['password']) {
            return jsonRetuen(0,'密码验证成功');
        }else{
            return jsonRetuen(1,'发布链接访问密码错误' , 6009);
        }
    }

    /**
     * 获取发布名字及是否有密码
     */
    public function publishMsg()
    {
        //接收数据
        $input = input('get.');

        //判断数据是否接收成功
        if(!$input) {
            return jsonRetuen(1,'数据接收失败' , 2000);
        }
        //查询相关发布
        $result = Db::name('publish')->where('scid',$input['pid'])->find();

        //判断是否开启密码
        if($result) {
            //将发布浏览次数+1
            Db::name('publish')->where('scid',$input['pid'])->update(['viewsnum' => $result['viewsnum']+1]);
            return jsonRetuen(0,['name'=>$result['sname'] , 'is_pwd' => $result['is_pwd']]);
        }else {
            return jsonRetuen(1,'发布信息错误',6010);
        }
    }

}