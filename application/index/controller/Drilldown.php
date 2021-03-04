<?php
namespace app\index\Controller;

use think\Db;
use app\base\controller\Addons;

class Drilldown extends Addons
{
    /**
     * drillid 当前下钻图表的id
     * drillpid 下钻图表的父级图表id
     * drillsid 下钻图表所在大屏的id
     */
    //新增下钻图表
    public function addDrillChart()
    {
        $post = input('post.');
   
        $data['screenid'] = $post['screenid'];
        $data['createtime'] = time();
        $data['updatetime'] = time();
        //取得type对应的data
        $data['tdata'] = Db::name('chartdata')->where('charttype',$post['drillconfig']['comkey'])->value('data');
        //更新时间
        Db::name('screen')->where('id',$post['screenid'])->update(['updatetime' => time()]);
        //将默认数据存入数据库中
        $input['tid'] = Db::name('screenchart')->insertGetId($data);
            //判断是否插入成功
            if(!$input['tid']) {
            return get_status(1,'添加图表失败',2004);
        }
        $input['screenid'] = $post['screenid'];
        //将图表类型取出
        $input['chartSourceType'] = $post['drillconfig']['dataOpt']['source']['type'];
        //序列化
        $input['dataOpt'] = json_encode($post['drillconfig']['dataOpt']);
        
        //序列化
        $input['chartData'] = json_encode($post['drillconfig']['chartData']);

        $id = [
            //当前下钻图表id
            'drillid' =>$input['tid'],
            //下钻图表父级id
            'drillpid'=>$post['tid'],
            //下钻图表大屏id
            'drillsid'=>$post['screenid']
        ];
        $post['drillconfig']['drilling'] = Array_merge($id,$post['drillconfig']['drilling']);
     
        //序列化
        if(isset($post['drillconfig']['drilling'])){
        $input['drilling'] = json_encode($post['drillconfig']['drilling']);
        }
        $input['charttype'] = $post['drillconfig']['charttype'];
        $input['name'] = $post['drillconfig']['name'];
        $input['comtype'] = $post['drillconfig']['comtype'];
        $input['comkey'] = $post['drillconfig']['comkey'];
        $input['width'] = $post['drillconfig']['width'];
        $input['height'] = $post['drillconfig']['height'];
        // $input['drilllevel'] = $post['drilllevel'];
        $input['parenttid'] = $post['tid'];
        $input['resizable'] = 0;
        //将tconfig存入表中
        $tconfig = Db::name('screencharttconfig')->insert($input);
    
        if(!$tconfig){
            return get_status(1,'添加图表失败',2004);
        }
        return get_status(0,['tid'=>$input['tid']]);   
    }

    //修改下钻图表
    public function updateDrillchart()
    {
        $post = input('put.');
        $post['drillconfig']['chartData'] = json_encode($post['drillconfig']['chartData']);
        $post['drillconfig']['drilling'] = json_encode($post['drillconfig']['drilling']);
        $post['drillconfig']['dataOpt'] = json_encode($post['drillconfig']['dataOpt']);
        unset($post['drillconfig']['tid']);
        $res = Db::name('screencharttconfig')->where(['tid'=>$post['tid']])->where($post['drillconfig'])->find();
        if($res){
            return get_status(0,'修改成功');
        }
        $update = Db::name('screencharttconfig')->where(['tid'=>$post['tid']])->update($post['drillconfig']);
        $upScreen['updatetime'] = time();
        $upScreenchart['updatetime'] = time();
        //更新图表更新时间
        Db::name('screenchart')->where('tid',$post['tid'])->update($upScreenchart);
        //更新大屏更新时间
        Db::name('screen')->where('id',$post['screenid'])->update($upScreen);
        if($update){
            return get_status(0,'修改成功');
        }else{
            return get_status(1,'图表更新失败',2034);

        }

    }

    //展示下钻图表
    public function queryDrillChart()
    {
        $get = input('get.');
        $res = Db::name('screencharttconfig')->where(['parenttid'=>$get['tid']])->field('id,parenttid,screenid,parenttid,showBorder,chartSourceType,collection,ishide,islock,key,resizable,x,y,selectDaid',true)->find();
        if(!$res){
            return get_status(1,'查询失败',5005);
        }
        $res['chartData'] = json_decode($res['chartData'],true);
        $res['dataOpt'] = json_decode($res['dataOpt'],true);
        $res['drilling'] = json_decode($res['drilling'],true);
        return get_status(0,['data'=>$res]);

    }

    //删除下钻图表
    public function deldrill()
    {
        $post = input('post.');
        //根据父级tid查找对应的下钻数据
        $tid = Db::name('screencharttconfig')->where(['parenttid'=>$post['chid']])->field('tid')->find();
        if(empty($tid)){
            return get_status(1,'暂无下钻图表',7010);
        }
        //根据下钻tid进行删除下钻图表
        $del = Db::name('screencharttconfig')->where($tid)->delete();
        if(!$del){
            return get_status(1,'图表数据删除失败',2005);
        }

        $delete = Db::name('screenchart')->where($tid)->delete();
        if(!$delete) {
            return get_status(1,'图表删除失败',2005);
        }else {
            return get_status(0 ,'图表删除成功');
        }
    }

    //下钻数据筛选
    public function choosedata()
    {
        $post = [
            'category'=>'北京',

        ];
        $data = json_decode(file_get_contents('http://192.168.30.119:8099/bingtu/xiazuan/biaozhuduibibingtu.php'),true);
        
        $data = $data['北京'];
        return $data;

        
    }
}