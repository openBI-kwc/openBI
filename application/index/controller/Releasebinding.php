<?php
namespace app\index\controller;
use think\Loader;

class Releasebinding
{
    //publish模型
    protected $publish;

    //初始化
    public function  __construct()
    {
        $this->publish = Loader::model('publish');
    }

    /**
     * 查询虽有的发布链接以及ID
     */
    public function  getAllPublish()
    {
        //获取所有的发布链接及ID
        $result = $this->publish->getAllPublish();
        if($result) {
            return get_status(0,$result);
        }else {
            return get_status(1,'查询失败',1111);
        }
    }

    /**
     * 绑定爱创的用户和发布的连接 user 用户标识 linkID 链接
     * {"user" : ["token1","token2","token3"],"linkIDs" : 21}
     */
    public function BindUserA()
    {
        $input = input('post.');
        //验证键
        $vali = valiKeys(['user','linkID'] , $input);
        if($vali['err']) {
            return get_status(1,$vali['data'].'键未设置',000);
        }
        //将user装换为字符串
        $input['user'] = implode( ',' , $input['user']);
        //验证相同
        $result = $this->publish->valiSameBindUser($input);
        if($result) {
            return get_status(0,'绑定成功');
        }
        //将数据用户绑定到数据库
        $result = $this->publish->BindUser($input);
        //判断绑定是否成功
        if($result) {
            return get_status(0,'绑定成功');
        }else {
            return get_status(1,'绑定失败');
        }
    }

    /**
     * 绑定爱创的用户和发布的连接 user 用户标识 linkID 链接
     * {"user" :"token1","linkID" : 21}
     */
    public function BindUsersS()
    {
        $input = input('post.');
        //验证键
        $vali = valiKeys(['user','linkID'] , $input);
        if($vali['err']) {
            return get_status(1,$vali['data'].'键未设置',000);
        }
        //获取当前发布链接的ACID

        //将当前ID拼接入原来ACID

        //验证相同
        $result = $this->publish->valiSameBindUser($input);
        if($result) {
            return get_status(0,'绑定成功');
        }
        //将数据用户绑定到数据库
        $result = $this->publish->BindUser($input);
        //判断绑定是否成功
        if($result) {
            return get_status(0,'绑定成功');
        }else {
            return get_status(1,'绑定失败');
        }
    }


}