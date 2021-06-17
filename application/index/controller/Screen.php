<?php
namespace app\index\Controller;

use app\addons\license\model\Carouselrelease;
use think\Db;
use think\Exception;
use think\Request;
use think\Session;
use \app\index\controller\Chart;
use \app\index\controller\Index;
use \app\index\controller\User;
use app\index\model\Screen as ScreenModel;
use app\index\model\Screenchart;
use app\index\model\Gisdata;
use app\base\controller\Addons;

/**
 * 图表处理 ,CURL 映射 预警 和一些图表的操作
 */
class Screen extends Addons
{
    protected $arrContextOptions = array(
                                    "ssl"=>array(
                                        "verify_peer"=>false,
                                        "verify_peer_name"=>false,
                                        ),
                                    );

    public function __construct(Request $request = null)
    {
        if (strtolower($_SERVER['REQUEST_METHOD']) == 'options') {
           exit();
        }
        parent::__construct($request);
        //获取当前方法
        $request = Request::instance();
        $action = strtolower($request->action());
        if ($action != 'getallchart' && $action != 'index') {
            $user = new User;
            $user->keepLogging();
        }
        $this->Request = Request::instance();
    }

    public function index()
    {

    }

    /**
     * 大屏加密
     * cid 大屏ID password 密码
     * return 成功or失败
     */
    public function screenLocking()
    {
        //接收数据
        $post = input('post.');
        //将接数据组成数组
        $data = [
            'lock' => 1,
            'password' => $post['password'],
            'updatetime' => time(),
        ];
        //修改大屏配置
        $update = Db::name('screen')->where(['id' => $post['cid']])->update($data);
        
        if($update) {
            return get_status(0,'大屏加密成功');
        }else {
            return get_status(1,'大屏加密失败',2001);
        }
    }

    /**
     * 大屏解锁
     */
    public function screenValiPassword()
    {
        //接收数据
        $input = input('post.');
        //判断数据是否接收成功
        if(!$input) {
            return get_status(1,'数据接收失败',2000);
        }
        //查出大屏的密码
        $res = Db::name('screen')->where('id',$input['cid'])->find();
        //验证码密码是否成功
        if($res['password'] == $input['password']) {
            return get_status(0,'密码验证成功');
        }else {
            return get_status(1,'密码错误',2002);
        }
    }
    /**
     * 大屏解密
     * cid 大屏ID 
     * return 成功or失败
     */
    public function screenUnLocking()
    {
        //接收数据
        $post = input('post.');
        //查询大屏密码
        $screen = Db::name('screen')->where(['id' => $post['cid']])->field('password')->find();
        if($screen['password'] != input('password')) {
            return get_status(1,'大屏解锁失败,密码错误',2002);
        }
        //将接数据组成数组
        $data = [
            'lock' => 0,
            'password' => '',
            'updatetime' => time(),
        ];
        //修改大屏配置
        $update = Db::name('screen')->where(['id' => $post['cid']])->update($data);
        
        if($update) {
            return get_status(0,'大屏解密成功');
        }else {
            return get_status(1,'大屏解密失败',2002);
        }
    }


    /**
     * 搜索大屏名字
     * serachword 搜索词
     * return arr 搜索出来的信息
     */
    public function getSerachScreenName()
    {
        //获取搜索词
        $serachword = input('get.serachword');
      

        //查询数据库
        $result = Db::name('screen')->where('name','like',"%".$serachword."%")->limit(5)->column('name');
        
        //返回数据
        return get_status(0,$result);
    }

    /**
     * 添加图表
     * @return mixed
     * @throws Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function addChart()
    {
         //接收数据
         $input = input('post.');
         //判断数据是否接收成功
         if(!$input) {
             return ;
         }
         if(input('charttype') != 'cloudComponent')
         {
            //从收藏表添加图表会携带收藏id，需删除
             if(isset($input['collectionid'])){
                 unset($input['collectionid']);
             }
             if(isset($input['is_col'])){
                unset($input['is_col']);
            }
             $data['screenid'] = $input['screenid'];
            //  dump($data);die;
             $data['tname'] = $input['key'];
            
             //将图表信息中加入图表创建时间
            $data['createtime'] = time();
            $data['updatetime'] = time();
            //查询图表中定位最大值
            $position = Db::name('screenchart')->where(['screenid' => $data['screenid']])->order('position DESC')->find();
            //判断大屏是否有图表
            if($position){
                //将定位最大值+1填入图表数据
                $data['position'] = $position['position'] + 1;
            }else{
                //没有图表默认位置为1
                $data['position'] = 0;
            }
            //TODO 处理gis地图
            if(in_array($input['comkey'] , ['amapgis']) ) {
                //获取用户选择的地图类型取得gistype对应的data
                $data['tdata'] = $this->getGisData($input);
            }else{
                //取得type对应的data
                $data['tdata'] = Db::name('chartdata')->where('charttype',$input['comkey'])->value('data');
            }
            //更新时间
            Db::name('screen')->where('id',$data['screenid'])->update(['updatetime' => time()]);
            //将默认数据存入数据库中
            $input['tid'] = Db::name('screenchart')->insertGetId($data);
             //判断是否插入成功
             if(!$input['tid']) {
                return get_status(1,'添加图表失败',2004);
            }
            if(!isset($input['dataOpt'])){
                $input['dataOpt'] = '';
            }else{
            //将图表类型取出
            $input['chartSourceType'] = $input['dataOpt']['source']['type'];
            //序列化
                $input['dataOpt'] = json_encode($input['dataOpt']);
            }
            //序列化
            $input['chartData'] = json_encode($input['chartData']);
            //序列化
            if(isset($input['drilling'])){
                    $input['drilling'] = json_encode($input['drilling']);
            }
            //序列化
            if(isset($input['incident'])){
                $input['incident'] = json_encode($input['incident']);
            }
            // dump($input);die;
            unset($input['text']);
            //将tconfig存入表中
            $tconfig = Db::name('screencharttconfig')->insert($input);
        
            if(!$tconfig){
                return get_status(1,'添加图表失败',2004);
            }
            return get_status(0,['tid' => intval($input['tid'])]);
        }else{
            //为插件组件
            //从收藏表添加图表会携带收藏id，需删除
             if(isset($input['collectionid'])){
                 unset($input['collectionid']);
             }
             if(isset($input['is_col'])){
                unset($input['is_col']);
            }
             $data['screenid'] = $input['screenid'];
            //  dump($data);die;
             $data['tname'] = $input['key'];
            
             //将图表信息中加入图表创建时间
            $data['createtime'] = time();
            $data['updatetime'] = time();
            //查询图表中定位最大值
            $position = Db::name('screenchart')->where(['screenid' => $data['screenid']])->order('position DESC')->find();
            //判断大屏是否有图表
            if($position){
                //将定位最大值+1填入图表数据
                $data['position'] = $position['position'] + 1;
            }else{
                //没有图表默认位置为1
                $data['position'] = 0;
            }
            //TODO 处理gis地图
            if(in_array($input['comkey'] , ['amapgis']) ) {
                //获取用户选择的地图类型取得gistype对应的data
                $data['tdata'] = $this->getGisData($input);
            }else{
                //取得type对应的data
                $data['tdata'] = Db::name('chartdata')->where('charttype',$input['comkey'])->value('data');
            }
            //更新时间
            Db::name('screen')->where('id',$data['screenid'])->update(['updatetime' => time()]);
            //将默认数据存入数据库中
            $input['tid'] = Db::name('screenchart')->insertGetId($data);
             //判断是否插入成功
             if(!$input['tid']) {
                return get_status(1,'添加图表失败',2004);
            }
            if(!isset($input['dataOpt'])){
                $input['dataOpt'] = '';
            }else{
                //将图表类型取出
                $input['chartSourceType'] = $input['dataOpt']['source']['type'];
                //序列化
                $input['dataOpt'] = json_encode($input['dataOpt']);
            }
            //序列化
            $input['chartData'] = json_encode($input['chartData']);
            //序列化
            if(isset($input['drilling'])){
                    $input['drilling'] = json_encode($input['drilling']);
            }
            //序列化
            if(isset($input['incident'])){
                $input['incident'] = json_encode($input['incident']);
            }
            //序列化
            $input['chartData'] = json_encode($input['chartData']);
            unset($input['text']);
            //将tconfig存入表中
            $tconfig = Db::name('screencharttconfig')->insert($input);
        
            if(!$tconfig){
                return get_status(1,'添加图表失败',2004);
            }
            return get_status(0,['tid' => intval($input['tid'])]);
        }
         
    }


    /**
     * 获取增加gis地图是的用户选择的数据
     * @param $input
     * @return array
     */
    public function getGisData($input)
    {
        //判断参数中是否有存储gis地图类型的键
        if(!isset($input['chartData']['layconfig'])) {
            return [];
        }
        //获取参数中的gis地图类型
        $gistype = array_keys($input['chartData']['layconfig']);
        //在数据库中查询gis地图类型对应的值
        $gisDataModel = new Gisdata();
        $gisdata = $gisDataModel->getAddGisdata($gistype);
        foreach ($gisdata as $key => $value) {
            $gisdata[$key] = json_decode($value,1);
        }
        $gisdatas[] = $gisdata;
        //返回数据
        return json_encode($gisdatas);

    }


    /**
     * 删除图表
     * chid 图表ID
     */
    public function delChart() 
    {
        //接收数据
        $input = input();

        //判断数据是否接收成功
        if(!$input) {
            return get_status(1,'数据接收失败' , 2000);
        }
        $res = Db::name('screenchart')->where('tid',$input['chid'])->field('position,screenid')->find();
        $screenid = $res['screenid'];
        //查询当前大屏中在当前图表的上层图表
        $result = Db::name('screenchart')->where('screenid',$screenid)->where('position','>',$res['position'])
                                            ->field('position,tid')->order('position DESC')->select();
        //将所有的上层图表位置-1
        foreach($result as $key => $value) {
            $update = Db::name('screenchart')->where('tid',$value['tid'])->update(['position' => $value['position']-1]);
        }
        //删除对应tconfig
        $del = Db::name('screencharttconfig')->where(['tid'=>$input['chid']])->delete();
        if(!$del){
            return get_status(1,'图表数据删除失败',2005);
        }
        //删除指定图表
        $delete = Db::name('screenchart')->delete($input['chid']);
        //删除关联的下钻图表
        $drill = Db::name('screencharttconfig')->where('parenttid',$input['chid'])->value('parenttid');
        if(!empty($drill)){
            Db::name('screencharttconfig')->where('parenttid',$input['chid'])->delete();
        }
        //判断是否删除成功
        if(!$delete) {
            return get_status(1,'图表删除失败',2005);
        }else {
            return get_status(0 ,'图表删除成功');
        }
    }

    public function updatechart()
    {
         //接收数据
         $input = input('put.');
         //判断数据是否接收成功
         if(!$input) {
             return get_status(1,'数据接收失败' ,2000);
         }

         //验证大屏ID和tid
        $valiKey = valiKeys(['screenid','tid'] , $input);
        if($valiKey['err']) { return get_status(0,'修改图表成功'); }

        if(isset($input['dataOpt'])){
            $input['dataOpt'] = json_encode($input['dataOpt']);
        }

        if(isset($input['chartData'])){
            $input['chartData'] = json_encode($input['chartData']);
         }
         if(isset($input['drilling']) && !empty($input['drilling'])){
            $input['drilling'] = json_encode($input['drilling']);
         }
        if(isset($input['incident']) && !empty($input['incident'])){
            $input['incident'] = json_encode($input['incident']);
        }
        //判断图表数据是否完全一致
         $vali = Db::name('screencharttconfig')->where($input)->select();

        if ($vali) {
             return get_status(0,'修改图表成功');
         }
         //判断是否被设置
         if(isset($input['dataOpt'])){
             //将json转为数组
            $dataOpt = json_decode($input['dataOpt'],1);
         }
         //判断类型是否被设置
         if(isset($dataOpt['source'])) {
            $source = $dataOpt['source'];
            //判断数据源类型是否是sql
            if(strtolower($source['type']) == 'sql') {
                //查看修改数据源类型
                try {
                    //获取修改数据源的ID 及sql语句 数据库配置
                    $id = $source['selectDaid'];
                    $sql = $source['sqlstr'];
                    
                    // $sql = "SELECT * FROM up_databasesource";
                    $config = Db::name('databasesource')->where('baseid',$source['dbLinkId'])->field('baseconfig')->find();
                    $config = json_decode($config['baseconfig'],1);
                    //将数据库密码解密
                    $config['password'] = decrypt($config['password'],$config['len']);
                    //通过配置文件执行sql
                    // if ($config['type'] == 'oracle') {
                    //     $data =  \OracleSource::connect($config)->execute($sql);
                    // } else {
                    //     $data = Db::connect($config)->query($sql);
                    // }
                    $data = \DataSource::connect($config)->query($sql);
                    
                    //将修改好的数据源信息执行修改
                    $update = [
                        'sid' => $source['dbLinkId'],
                        'returnsql' => $sql,
                        'data' => json_encode($data),

                    ];
                    //执行sql语句
//                    $updatesSql = Db::name('datament')->where('daid',$id)->update($update);
                    $chartSourcetype = Db::name('screencharttconfig')->where('tid',$input['tid'])->update(['chartSourceType'=>$source['type']]);
                } catch (\Exception $e) {
                    if(empty($source['selectDaid']) || empty($source['sqlstr'])){
                        return get_status(0,"请选择数据源");
                    }else {
                        return get_status(1,"请检查SQL语句或配置" , 2006);
                    }
                }
            }
            $input['chartSourceType'] = $dataOpt['source']['type'];
            
            $upScreenchart['daid'] = isset($dataOpt['source']['selectDaid']) ? (int)$dataOpt['source']['selectDaid'] : 0;
            // dump($dataOpt['source']['type']);die;
            // $chartSourcetype = Db::name('screencharttconfig')->where('tid',$input['tid'])->update(['chartSourceType'=>$dataOpt['source']['type']]);
        }
        $upScreen['updatetime'] = time();
        $upScreenchart['updatetime'] = time();
        //更新图表更新时间
        Db::name('screenchart')->where('tid',$input['tid'])->update($upScreenchart);
        //更新大屏更新时间
        Db::name('screen')->where('id',$input['screenid'])->update($upScreen);
        //将图表加入到数据中
        $update = Db::name('screencharttconfig')->where(['tid' =>$input['tid']])->update($input);
        if(!$update){
            return get_status(1,'图表更新失败',2034);
        }
        return get_status(0,'修改图表成功');


    }
    /**
     * 图表锁定
     * chid 图表ID
     */
    public function chartlock() 
    {
        //接收数据
        $input = input('put.');
        //判断数据是否接收成功
        if(!$input) {
            return get_status(1,'数据接收失败' , 2000);
        }
        //判断锁定OR解锁
        if(!$input['status']) {
            $islock = 0;
        }else {
            $islock = 1;
        }
        //验证是否已锁定
        $vali = Db::name('screencharttconfig')->where(['tid' => $input['chid'], 'islock' => $islock])->select();
        //设置返回值
        if(!$input['status']) {
            if($vali) {
                return get_status(0,'解锁成功');
            }
        }else {
            if($vali) {
                return get_status(0,'锁定成功');
            }
        }
        //修改锁定状态
        $update = Db::name('screencharttconfig')->where(['tid' => $input['chid']])->update(['islock' => $islock]);
        //设置返回值
        if(!$input['status']) {
            if(!$update) {
                return get_status(1,'大屏解锁失败',2007);
            }else{
                return get_status(0,'解锁成功');
            }
        }else {
            if(!$update) {
                return get_status(1,'大屏锁定失败',2008);
            }else{
                return get_status(0,'锁定成功');
            }
        }
    }

    /**
     * 隐藏图图表
     */
    public function charthidden() 
    {
        //接收数据
        $input = input('put.');
        //判断数据是否接收成功
        if(!$input) {
            return get_status(1,'数据接收失败');
        }
        //判断显示OR隐藏
        if(!$input['status']) {
            $hidden = 0;
        }else {
            $hidden = 1;
        }
        //验证是否已隐藏
        $vali = Db::name('screencharttconfig')->where(['tid' => $input['chid'] , 'ishide' => $hidden])->select();
        //设置返回值
        if(!$input['status']) {
            if($vali) {
                return get_status(0,'显示成功');
            }
        }else {
            if($vali) {
                return get_status(0,'隐藏成功');
            }
        }
        //修改隐藏状态
        $update = Db::name('screencharttconfig')->where(['tid' => $input['chid']])->update(['ishide' => $hidden]);
        //设置返回值
        if(!$input['status']) {
            if(!$update) {
                return get_status(1,'大屏显示失败',2009);
            }else{
                return get_status(0,'显示成功');
            }
        }else {
            if(!$update) {
                return get_status(1,'大屏隐藏失败',2019);
            }else{
                return get_status(0,'隐藏成功');
            }
        }
    }

    /**
     * 图表复制
     * "chid" : "int 图表ID"
     */
    public function chartcopy()
    {
        //接收数据
        $input = input('post.');
        //判断数据是否接收成功
        if(!$input) {
            return get_status(1,'数据接收失败' ,2000);
        }
        //查询相关图表信息
        $data = Db::name('screenchart')->where('tid',$input['tid'])->field('screenid,tname,tdata,islock,link,position,ishide')->find();
        
        //判断查询是否成功
        if(!$data) {
            return get_status(1,'复制失败',2011);
        }


        //加入创建时间和修改时间
        $data['createtime'] = time();
        $data['updatetime'] = time();
        //修改副本名字
        $data['tname'] = $input['key'];
        //获取该大屏中最大的图层
        $maxPosition = Db::name('screenchart')->where('screenid',$data['screenid'])->field('position')->order('position DESC')->find()['position'];
        //将复制的新图层放置为最大图层
        $data['position'] = $maxPosition + 1;
        //将副本加入数据库
        $tid = Db::name('screenchart')->insertGetId($data);
        //查询tconfig表
        $tconfig = Db::name('screencharttconfig')->field('id,tid',true)->where(['tid'=>$input['tid']])->find();
        $tconfig['key'] = $input['key'];
        $tconfig['tid'] = $tid;
        $insert = Db::name('screencharttconfig')->insert($tconfig);

        if(!$insert) {
            return get_status(1,'复制失败',2011);
        }else {
            //返回图表id
            return get_status(0,['tid' => intval($tid)]);
        }
    }

    /**
     * 移动图表
     * 
     * "data" : 
     *   {
     *      "tid" : "图表ID"
     *       "position" : "图表定位"
     *   },
     *   {
     *       "tid" : "图表ID"
     *       "position" : "图表定位"
     *   }
     *   ...
     * 
     */
    public function movechart()
    {   
        //接收数据
        $input = input('put.');
        //判断数据是否接收成功
        if(!$input) {
            return get_status(1,'数据接收失败',2000);
        }
        //判断是否有操作('Topping/end/moveUp/moveDown')
        if(!isset($input['operating'])) {
            return get_status(1,'必须有移动操作',2012);
        } 
        //判断是否有ID
        if(isset($input['tid'])) {
            //获取当前ID的位置
            $result = Db::name('screenchart')->where('tid',$input['tid'])->field('position,screenid')->find();
            //当前图表位置
            $position = $result['position'];
            //当前图表大屏
            $screenid = $result['screenid'];
        }
        //switch遍历
        switch ($input['operating']) {
            case 'top' ://置顶
                    //查询当前大屏中在当前图表的上层图表
                    $result = Db::name('screenchart')->where('screenid',$screenid)->where('position','>',$result['position'])
                                                    ->field('position,tid')->order('position DESC')->select();
                    if(!$result) {
                        //要移动的位置
                        $movePosition = $position;
                    }else {
                        //要移动的位置
                        $movePosition = $result[0]['position'];
                    }
                   
                    //将所有的上层图表位置-1
                    foreach($result as $key => $value) {
                        $update = Db::name('screenchart')->where('tid',$value['tid'])->update(['position' => $value['position']-1]);
                    }
                break;
            case 'end' ://置底
                    //查询当前大屏中在当前图表的上层图表
                    $result = Db::name('screenchart')->where('screenid',$screenid)
                                                    ->where('position','<',$result['position'])
                                                    ->field('position,tid')->order('position ASC')->select();
                    if(!$result) {
                        //要移动的位置
                        $movePosition = $position;
                    }else {
                        //要移动的位置
                        $movePosition = $result[0]['position'];
                    }

                        //将所有的上层图表位置-1
                        foreach($result as $key => $value) {
                        $update = Db::name('screenchart')->where('tid',$value['tid'])->update(['position' => $value['position']+1]);
                    }
                break;
            case 'up' ://上移
                    //查询当前大屏最大位置
                    $result = Db::name('screenchart')->where('screenid',$screenid)->field('position,tid')->order('position DESC')->find();
                    
                    if($result['position'] == $position) {
                        $movePosition = $position;
                    }else {
                        //要移动的位置
                        $movePosition = $position+1;
                    }
                    //修改比当前图层高一层的值-1
                    $up = Db::name('screenchart')->where('screenid',$screenid)
                                                ->where('position',$movePosition)
                                                ->update(['position'=>$position]);
                   
                break;
            case 'down' ://下移
                    //查询当前大屏最大位置
                    $result = Db::name('screenchart')->where('screenid',$screenid)->field('position,tid')->order('position ASC')->find();
                    if($result['position'] == $position) {
                        $movePosition = $position;
                    }else {
                        //要移动的位置
                        $movePosition = $position-1;
                    }
                    //修改比当前图层高一层的值-1
                    $up = Db::name('screenchart')->where('screenid',$screenid)
                                                ->where('position',$movePosition)
                                                ->update(['position'=>$position]);
                break;
            case 'all' ://下移
                    $screenid = '';
                    //确定最大位置
                    $max = count($input['layerlist']) - 1;
                    //遍历图表排序列表
                    foreach ( $input['layerlist'] as $key => $value) {
                        //通过图表名字修改图表信息
                        $chart = Db::name('screenchart')->where('tname',$value)->update(['position' => $max]);
                        //判断screenid是否等于空
                        if($screenid == '') {
                            $chartInfo = Db::name('screenchart')->where('tname',$value)->find();
                            $screenid  = $chartInfo['screenid'];
                        }
                        $max--;
                    }
                    $result = Db::name('screenchart')->where('screenid',$screenid)->order('position DESC')->column('tname');
                    return  get_status(0,$result);
                break;
        }
        //修改当前图表位置
        if(!($movePosition == $position)) {
            $update = Db::name('screenchart')->where('tid',$input['tid'])->update(['position' => $movePosition]);
        }else {
            $update = 1;
        }

        //判断是否修改成功
        if(!$update) {
            return get_status(1,'移动失败',2014);
        }else{
            $result = Db::name('screenchart')->where('screenid',$screenid)->order('position DESC')->column('tname');
            return  get_status(0,$result);
        }

        
    }

    /**
     * 查看数据数据源显示结果
     */
    public function getDataSource() 
    {
        //接收数据
        $input = input('get.');
        //判断数据是否接收成功
        if(!$input) {
            return get_status(1,'数据接收失败',2000);
        }
        //查询数据库(不知道库和的信息)
        $data = Db::name('screen')->where('id',1)->field('data')->find();
        //返回数据源
        if (!$data) {
            return get_status(1,'获取数据源失败',000);
        }else {
            return get_status(0,$data);
        }
    }

    public function Mapping($input = [] ,$filedsparam = [])
    {
        //判断是否dbconfig是否有值
        if(empty($input)) {
             //接收数据
            $input = input('post.');
            //判断数据是否接收成功
            if(!$input) {
                return get_status(1,'数据接收失败',2000);
            }
            if($input['type'] != 'STATIC'){
                if(isset($input['filedsparams'])){
                    //将筛选条件存入变量
                    $filedsparam = $input['filedsparams'];
                }
            }
        }
        //处理gis地图
        if(isset($input['chartType'])) {
            if(in_array($input['chartType'] , ['amapgis'])) {
                //判断是否需要返回data
                if(isset($input['returnData'] )) {
                    if($input['returnData'] == true) {
                        return $input['sdata'];
                    }
                }else {
                    return get_status(0 , [/*'data' => $data ,*/ 'success' =>[],'ismatch' => []]);
                }
            }
        }
        //取出需要映射的key
        $keys = $input['map'];
        
        //遍历keys
        foreach($keys as $key => $value) {
            //判断映射目标值是否为空
            if($value[1] == ''){
                //如果为空则用默认映射值替换
                $keys[$key][1] = $keys[$key][0];
            }
        }
        //处理不同类型的传值使其在下面运行时保持键一致
        $input = $this->getInput($input);
        //处理不同类型的数据
        $data = $this->getdata($input);
        if($input['type'] == 'STATIC'){
            unset($filedsparam);
        }
        // data就是拿出来的数据，要在这儿进行筛选
        if(!empty($filedsparam)){
            $key = [];
            if(!isset($data[$filedsparam['name']])){
                return get_status(1,'暂无对应数据',7012);
            }
            //筛选出对应的数据
            $data = $data[$filedsparam['name']];
            //将下钻图表需要的字段组成数组
            foreach($keys as $kk=>$vv){
                //区分可选字段和必填字段
                if(isset($vv[3])){
                $key[$vv[3]][] = $vv[1];
                }else{
                $key['must'][] = $vv[1];
                }
            }
            //将筛选出来的数据与需要匹配的字段进行替换
            foreach($data as $dk=>$dv){
                //将关联数组变为索引
                    $dv = array_values($dv);
                //将字段数组与数据数组合并为新数组，字段数组为键，数据数组为值
                    $data[$dk] = array_combine($key['must'],$dv);
            }
        }
        //判断data是否为空,或者是否为数组
        if(empty($data) || !is_array($data)) {
            if(isset($data['err'])) {
                //判断是否需要返回data
                if(isset($input['returnData'] )) {
                    if($input['returnData'] == true) {
                        return get_status(0,[]);
                    } else {
                        return get_status($data['err'] , $data['msg'] , $data['code'] );
                    }
                }else {
                    return get_status($data['err'] , $data['msg'] , $data['code'] );
                }
            }else {
                return get_status(0,['success' => [],'ismatch' => false]);
            }
        }

        //判断data中的value是否是关联数组用于返回值类型统一
        foreach($data as $key => $value) {
            $vali = $this->isAssocArray($value);
            if($vali) {
                //获取数组的长度
                $count = count($value);
                //将数组的第一个元素拿出
                $arr0 = $data[$key][0];
                //将数组的第一个元素销毁
                unset($data[$key][0]);
                //将数组的第一个元素保存至最后一个
                $data[$key][$count] = $arr0;
            }
        }

        

        //定义返回数组,便于存储
        $arr = [];
        $i = 0;
        //定义所有替换之后键的存储数组用户验证完整性
        $allKeys = [];
        //定义成功匹配的字段名
        $success = [];
        //将map中必须映射的字段取出
        $must = [];
        // dump($keys);
        // dump($keys);
        //遍历data
        foreach($data as $key => $value) {
            // //遍历键
            foreach($keys as $k => $val ) {
                //判断是否为必须匹配的字段
                if(!isset($val[3])) {
                    //将必须匹配的字段加入到must数组中
                    if( !in_array($val[0] , $must)) {
                        $must[] = $val[0];
                    }
                }
                //判断allKeys数组长度是否与原来键的数组长度
                if(count($allKeys) <  count($val)) {
                    $allKeys[] = $val[0];
                }
                //判断$val[1]是否是存在并且不是空值
                if(isset($val[1]) && $val[1] != ''){
                    //判断data数组中是否有关于keys数组中符合的值
                    if(isset($value[$val[1]])) {
                        //查询$val[0]是否保存入$success数组
                        if( !in_array($val[0] , $success)) {
                            $success[] = $val[0];
                        }
                        //将匹配成功的项加入新数组
                        $arr[$i][$val[0]] = $value[$val[1]];
                        //删除目标数组
                        unset($data[$key][$val[1]]);
                        //将匹配成功的项加入原数组
                        $data[$key][$val[0]] = $value[$val[1]]; 
                    }
                }
                
            }
            $i++;
        }
        // dump($arr);
        
        //判断$arr是否匹配成功
        if (!empty($arr)) {
            //是否验证完整性
            if(0) {
                //验证数组完整性
                foreach($arr as $key => $value) {
                   
                    //定义是否完整性标识
                    $j = 0;
                    foreach($must as $k => $val ) {
                                if(!isset($value[$val])) {
                                    $j = 1;
                                }
                            }   
                    //如果不完整则删除
                    if($j) {
                        unset($arr[$key]);
                    }
                }
            }
            
        }else {
            $i = 0;
            //如果匹配结果不存在则返回原来数组,在原来数组中查询默认字段
            foreach($data as $key => $value) {
                foreach ($keys as $k => $val) {
                    //判断data的value中是否有下标为默认字段的值
                    if(isset($value[$val[0]]) && $val[1] == '') {
                        $arr[$i][$val[0]] = $value[$val[0]];
                        // $arr[][$val[0]] = $value[$val[0]];
                        //查询$val[0]是否保存入$success数组
                        if( !in_array($val[0] , $success)) {
                            $success[] = $val[0];
                        }
                    }
                }
                $i++;
            }
        }
        
        //判断匹配成功的字段中是否全部包含必须映射的字段
        $ismatch = true;
        //遍历must的字段
        foreach ($must as $key => $value) {
            //判断must字段中每一个字段是否都在成功数组里面
            if(!in_array($value , $success)) {
                $ismatch = false;
            }
        }
        //判断是否需要返回data
        if(isset($input['returnData'] )) {
            if($input['returnData'] == true) {
                //实例化处理图像对象
                $chart = new Chart();
                //使用处理入口方法
                $arr = $chart->index($input['chartType'],$arr);
                
                return $arr;
            } 
        }else {
            if(isset($data['err'])) {
                return get_status($data['err'] , $data['msg'] , $data['code'] );
            }else {
                return get_status(0 , [/*'data' => $data ,*/ 'success' =>$success,'ismatch' => $ismatch]);
            }
        }
    }

    /**
     * 处理传值
     */
    protected function getInput($input)
    {
        //修改前端传值使其保持一致方便下面操作
        switch ($input['type']) {
            case "STATIC" :
                break;
            case "Excel/Csv" :
                    if(isset($input['selectDaid'])) {
                        $input['selectedId'] = $input['selectDaid'];
                        unset($input['selectDaid']);
                    }else {
                        $input['selectedId'] = '';
                    }
                break;
            case "API" :
                if(isset($input['apiURL'])) {
                    $input['url'] = $input['apiURL'];
                    unset($input['apiURL']);
                }else {
                    $input['url'] = '';
                }
                if(isset($input['selectDaid'])) {
                    $input['selectedId'] = $input['selectDaid'];
                    unset($input['selectDaid']);
                }else {
                    $input['selectedId'] = '';
                }
                break;
            case "SQL" :
                if(isset($input['selectDaid'])) {
                    $input['selectedId'] = $input['selectDaid'];
                    unset($input['selectDaid']);
                }else {
                    $input['selectedId'] = '';
                }
                break;
            case "WebSocket" :
                if(isset($input['socketURL'])) {
                    $input['url'] = $input['socketURL'];
                    unset($input['socketURL']);
                }else {
                    $input['url'] = '';
                }
                if(isset($input['selectDaid'])) {
                    $input['selectedId'] = $input['selectDaid'];
                    unset($input['selectDaid']);
                }else {
                    $input['selectedId'] = '';
                }
                break;
            case "自定义视图" :
                if(isset($input['selectDaid'])) {
                    $input['selectedId'] = $input['selectDaid'];
                    unset($input['selectDaid']);
                }else {
                    $input['selectedId'] = '';
                }
                break;
        }
        return $input;
    }

    /**
     * 判断数组是否是关联数组
     */
    protected function isAssocArray($arr)  
    {  
        if(!is_array($arr)){
            return false;
        }
        $i = 0;
        foreach($arr as $value) {
            if(!isset($arr[$i])) {
                return false;
            }
            $i++;
        }
        return true;
    }

    /**
     * 获取data
     */
    protected function getData($input)
    {
        $data = [];
        switch ($input['type']) {
            case "STATIC" :
                //取出需要匹配的data
                $data = $input['sdata'];
                //判断是否是前端请求有TID
                if(isset($input['tid'])) {
                    //将数据库中的tdata修改为data
                    if(!empty($data)) {
                        $update = Db::name('screenchart')->where('tid',$input['tid'])->update(['tdata' => json_encode($data)]);
                    }  
                    unset($input['tid']);
                }
                break;
            case "Excel/Csv" :
                //从数据库中取出响应的值
                $data = $this->getDatament($input['selectedId']);
                break;
            case "API" :
                //判断是否有URL
                if($input['url'] == "") {
                    //从数据库中取值
                    $data = $this->getDatament($input['selectedId']);
                }else {
                    try{
                        //直接从API里面取值
                        $results = file_get_contents($input['url'] , false , stream_context_create($this->arrContextOptions));
                        //如果返回值为XML则转成json
                        if(xml_parser($results)){
                            $xml =simplexml_load_string($results);
                            $xmljson= json_encode($xml);
                            $result=json_decode($xmljson,true);
                        }else{
                            $result=json_decode($results,true);
                        }
                        //判断$result是否是二维数组
                        if(is_array($result)) {
                            $data= $this->toArray($result);
                        }else {
                            $data = json_decode($result,1);
                            if($data) {
                                $data = $this->toArray($data);
                            }
                        }
                    } catch (\Exception $e) {
                        return $data = ['err' => 1 , 'msg' => 'API请求失败','code' => 7011];
                    }
                }
                break;
            case "SQL" :
                //判断是否有URL
                if($input['sqlstr'] == "") {
                    //从数据库中取值
                    $data = $this->getDatament($input['selectedId']);
                }else {
                    //从数据库中拿到数据库配置ID
                    $configID = Db::name('datament')->where('daid' , $input['selectedId'])->find();
                    //判断是否查询成功
                    if(!$configID) {
                        return $data = ['err' => 1 , 'msg' => '数据库ID请求失败','code' => 7012];
                    }
                    //通过数据库配置ID拿到数据库配置
                    $DbConfig = Db::name('databasesource')->where('baseid' , $configID['sid'])->find();
                    //判断是否查询成功
                    if(!$DbConfig) {
                        return $data = ['err' => 1 , 'msg' => '数据库配置查询失败','code' => 7012];
                    }else {
                        $DbConfig = json_decode($DbConfig['baseconfig'],1);
                        //将数据库密码解密
                        $DbConfig['password'] = decrypt($DbConfig['password'],$DbConfig['len']);
                    }
                    //连接数据库执行sql
                    try{

                        // if ($DbConfig['type'] == 'oracle') {
						// 	$result =  \OracleSource::connect($DbConfig)->execute("$input[sqlstr]");
						// } else {
						// 	$result = Db::connect($DbConfig)->query("$input[sqlstr]");
						// }
                        $result = \DataSource::connect($DbConfig)->query("$input[sqlstr]");
                        //判断$result 的类型
                        foreach ($result as $key => $value) {
                            foreach ($value as $k => $v) {
                                if(is_resource($v)) { //如果为资源类型则转化为字符串
                                    $result[$key][$k] = stream_get_contents($v);
                                }
                            }
                        }
                    } catch (\Exception $e) {
                        return $data = ['err' => 1 , 'msg' => '执行sql失败','code' => 7012];
                    }
                    //判断$result是否是二维数组
                    if(is_array($result)) {
                        $data= $this->toArray($result);
                    }else {
                        $data = json_decode($result,1);
                        if($data) {
                            $data = $this->toArray($data);
                        }
                    }

                }
                break;
            case "WebSocket" :
                //判断是否有URL
                if($input['url'] == "") {
                    //从数据库中取值
                    $data = $this->getDatament($input['selectedId']);
                }else {
                    try{
                        //直接从API里面取值
                        $results = file_get_contents($input['url'] , false , stream_context_create($this->arrContextOptions));
                        //如果返回值为XML则转成json
                        if(xml_parser($results)){
                            $xml =simplexml_load_string($results);
                            $xmljson= json_encode($xml);
                            $result=json_decode($xmljson,true);
                        }else{
                            $result=json_decode($results,true);
                        }
                        //判断$result是否是二维数组
                        if(is_array($result)) {
                            $data= $this->toArray($result);
                        }else {
                            $data = json_decode($result,1);
                            if($data) {
                                $data = $this->toArray($data);
                            }
                        }
                    } catch (\Exception $e) {
                        return $data = ['err' => 1 , 'msg' => 'websock请求失败','code' => 7012];
                    }
                }
                break;
            case "自定义视图" :
                //从数据库中取值
                $result = $this->getDatament($input['selectedId']);
                $data= $this->toArray($result);
                break;
        }
        return $data;            
    }

    /** 
     * 取出datament中的数据
     */
    public function getDatament($did)
    {
       
        //不是静态数据在数据库中取出数据
        $res = Db::name('datament')->where('daid',intval($did))->field('data,datatype,filepath')->find();
        
        //处理API类型
        if($res['datatype'] == 'api' || $res['datatype'] == 'websocket') {

            try{
                //直接从API里面取值
                $result = file_get_contents($res['filepath']);

                //判断$result是否是二维数组
                if(is_array($result)) {
                    $data= $this->toArray($result);
                }else {
                    $data = json_decode($result,1);
                    if($data) {
                        $data = $this->toArray($data);
                    }
                }
            } catch (\Exception $e) {
                $data = [];
            } 
        }else {
            //将data转换成数组
            $data = json_decode($res['data'],1);
        }
        return $data;
    }

    /**
     * 转换成为二维数组
     */
    protected function toArray($result)
    {
        if(!is_array($result)){
            return [];
        } 
        //遍历查询是否是二维数组
        foreach($result as $key => $value) {
            //不是二维数组销毁改值
            if(!is_array($value)) {
                unset($result[$key]);
            }
        }
        return $result;
    }



    /**
     * 添加发布信息
     * 
     */
    public function addRelease()
    {
        //查询系统是否设置为不发布
        $config = configJson();
        if(!$config['config']['system']['publish']){
            return get_status(1,'当前系统设置为不能发布',6002);
        }
        //接收数据
        $input = input('post.');
        //判断数据是否接收成功
        if(!$input) {
            return get_status(1,'数据接收失败',2000);
        }
        //验证屏幕是否发布或是否存在
        $screen = Db::name('screen')->where('id',$input['pid'])->find();
        if(!$screen) {
            return get_status(1,'大屏不存在',2020);
        }
        //判断大屏是否已经发布
        $vali  = Db::name('publish')->where('scid',$input['pid'])->find();
        if($vali) {
            return get_status(1,'不能重复发布',6001);
        }
        //判断大屏发布类型
        if($input['type'] == 'page'){
            $input['type'] = 1;
            $data = "";
            $input['shid'] = 0;
        }else{
            //如果为历史快照,则生成当前大屏配置及图表
            $input['type'] = 2;
            //将pid改为当前大屏ID
            $input['shid'] = $this->snapshot($input['pid']);
        }
        if(!isset($input['token'])) {
            $input['token'] = '';
        }
        if(!isset($input['localDeploy'])) {
            $input['localDeploy'] = false;
        }

        //将创建时间加入数组
        if($input['testTime'] == 0){
            $data= [
                'createtime' => time(),
                'scid' => $input['pid'],
                'sname' => $input['name'],
                'link' => $input['url'],
                'is_pwd' => $input['is_pwd'],
                'token' => $input['token'],
                'expiredate' =>0,
                'ptype' => $input['type'],
                'pdata' => $input['data'],
                'shid' => $input['shid'],
                'localdeploy' => intval($input['localDeploy']),
            ];
        }else{
            $data= [
                'createtime' => time(),
                'scid' => $input['pid'],
                'sname' => $input['name'],
                'link' => $input['url'],
                'is_pwd' => $input['is_pwd'],
                'token' => $input['token'],
                'expiredate' => time() + $input['testTime'],
                'ptype' => $input['type'],
                'pdata' => $input['data'],
                'shid' => $input['shid'],
                'localdeploy' => intval($input['localDeploy']),
            ];
        }

        
        //获取发布用户的用户名
        $publishuser = $this->getUserName();
        //将发布用户名加入到大屏
        // Db::name('screen')->where('id',$input['pid'])->update(['publishuser' => $publishuser]);
        //通过发布用户名获取用户ID
        $uid = Db::name('user')->where('username',$publishuser)->field('uid')->find();
        //将uid存入发布列表中
        $data['uid'] = $uid['uid'];
        //判断是否设置密码
        if(isset($input['password'])) {
            //判断密码是否为空
            if(!empty($input['password'])) {
                //解密加入发布列表中
                $data['password'] = decrypt($input['password'],$input['len']);
            }
        }
        //判断是否有发布封面
        if(isset($input['img'])) {
            //判断发布封面是否为空
            if(empty($input['img'])) {
                //默认发布封面
                $data['img'] = '/static/img/medical.png';
            }else {
                //将发布封面加入发布列表
                $data['img'] = $input['img'];
            }
        }else {
            //默认发布封面
            $data['img'] = '/static/img/medical.png';
        }
        
        //将所有信息插入数据库
        $insert = Db::name('publish')->insert($data);
        //将大屏信息修改成已发布
        $update = Db::name('screen')->where('id',$input['pid'])->update(['publish' =>1 ]);
        //判断是否成功
        if(!$insert) {
            return get_status(1,'发布失败',6002);
        }else {
            return get_status(0,'发布成功');
        }
    }

    /**
     * 处理历史快照
     * $pid 发布的大屏ID
     */
    protected function snapshot($pid)
    {
        //复制当前大屏配置信息
        $screenData = Db::name('screen')->where('id',$pid)->find();
        //销毁id
        unset($screenData['id']);
        //将大屏类型修改为发布信息
        $screenData['screentype'] = 1;
        //将大屏名字改为当前名字+ 历史快照
        $screenData['name'] = $screenData['name'] .'Snapshot' . mt_rand(0,9999);
        //插入数据库获取自增id
        $screenid = Db::name('screen')->insertGetId($screenData);
        //查找相关图表
        $screenchart = Db::name('screenchart')->where('screenid',$pid)->field('tid',true)->select();
        foreach($screenchart as $k=>&$v){
            $v['screenid'] = $screenid;
            $v['updatetime'] = time();
            $v['createtime'] = time();
        }
        //将图表信息加入新大屏
        $screeninster = Db::name('screenchart')->insertAll($screenchart);
        //查询相关tconfig
		$tconfig = Db::name('screencharttconfig')->field('id',true)->where('screenid',$pid)->select();
				
			if($tconfig){
				//新增图表id
				$tid = Db::name('screenchart')->field('tid')->where('screenid',$screenid)->select();
				foreach($tconfig as $k=>&$v){
					$v['screenid'] = $screenid;
					$v['tid'] = $tid[$k]['tid'];
				}
				$tconfiginsert =  Db::name('screencharttconfig')->insertAll($tconfig);
			}        
        
        return $screenid;
    }

    /**
     * 通过token 获取用户名
     */
    public function getUserName(){
        //获取当前的token
        $header = get_all_header();
        //判断token是否存在
        if(!isset($header['token'])){
            //没有token返回NoUser
            return 'NoUser';
        }else{
            //将token加入到变量
            $token = $header['token'];
        }
        //通过token查询用户id 
        $uid = Db::name('token')->where('token',$token)->field('uid')->find();
        //判断查询是否成功
        if(!$uid){
            //uid
            return 'NoUser';
        }
        //通过uid获取用户信息
        $user = Db::name('user')->where('uid' , $uid['uid'])->field('username')->find();
        //判断查询是否成功
        if(!$user){
            //user
            return 'NoUser';
        }
        //返回user
        return $user['username'];
    }


    /**
     * 发布列表
     */
    public function releaseList()
    {
        //接收get数据
		$get = input('get.');
		//判断是否有有搜索关键字
		if(!isset($get['searchword'])) {
			//设置默认搜索关键字
			$get['searchword'] = '';
		}else {
			//去掉首尾空格
			$get['searchword'] = rtrim($get['searchword']);
		}
		//判断是否排序
		if(!isset($get['order'])) {
			//设置默认排序规则
			$get['order'] = 'pid';
		}else{
			//去掉首尾空格
			$get['order'] = rtrim(strtolower($get['order']));
		}
		//判断是否有sid
		if(!isset($get['sid'])) {
			$get['sid'] = 0;
        }
        //分页 $currentPage第几页
        if(isset($get['currentPage'])){
            $currentPage = $get['currentPage'];
        }else {
            $currentPage = 1;
        }
        // return $get['pageSize'];
        //分页  $pageSize每页条数
        if(isset($get['pageSize'])){
            $pageSize = $get['pageSize'];
        }else {
            $pageSize = 10;
        }

        //获取发布用户的用户名
        $publishuser = $this->getUserName();
        //通过发布用户名获取用户ID
        $uid = Db::name('user')->where('username',$publishuser)->field('uid')->find();
        //通过uid获取用户权限
        $role = Db::name('user_role')->where('uid',$uid['uid'])->find();
        
        if($get['sid'] == 0 ){
            if($role['rid'] == 1 ) {
                $data = Db::name('publish')->where('sname' , 'like' , "%".$get['searchword']."%")
                                        ->page($currentPage.','.$pageSize)
                                        ->order($get['order'] .' DESC')
                                        ->field('pid,scid,sname,is_pwd,expiredate,link,createtime,ptype,pdata,viewsnum,password,extype,localdeploy')
                                        ->select();
                //查询总条数
                $total = Db::name('publish')->where('sname' , 'like' , "%".$get['searchword']."%")->count();
            }else {
                $data = Db::name('publish')->where('sname' , 'like' , "%".$get['searchword']."%")
                                        ->where('uid',$uid['uid'])
                                        ->page($currentPage.','.$pageSize)
                                        ->order($get['order'] .' DESC')
                                        ->field('pid,scid,sname,is_pwd,expiredate,link,createtime,ptype,pdata,viewsnum,password,extype,localdeploy')
                                        ->select();
                //查询总条数
                $total = Db::name('publish')->where('sname' , 'like' , "%".$get['searchword']."%")->where('uid',$uid['uid'])->count();
            }
            
        }else {
            if($role['rid'] == 1 ) {
                $data = Db::name('publish')->where('sname' , 'like' , "%".$get['searchword']."%")
                                            ->where('sid' , $get['sid'])
                                            ->page($currentPage.','.$pageSize)
                                            ->order($get['order'] .' DESC')
                                            ->field('pid,scid,sname,is_pwd,expiredate,link,createtime,ptype,pdata,viewsnum,password,extype,localdeploy')
                                            ->select();
                //查询总条数
                $total = Db::name('publish')->where('sid' , $get['sid'])->where('sname' , 'like' , "%".$get['searchword']."%")->count();
            }else {
                $data = Db::name('publish')->where('sname' , 'like' , "%".$get['searchword']."%")
                                            ->where('sid' , $get['sid'])
                                            ->where('uid',$uid['uid'])
                                            ->page($currentPage.','.$pageSize)
                                            ->order($get['order'] .' DESC')
                                            ->field('pid,scid,sname,is_pwd,expiredate,link,createtime,ptype,pdata,viewsnum,password,extype,localdeploy')
                                            ->select();
                //查询总条数
                $total = Db::name('publish')->where('sid' , $get['sid'])->where('sname' , 'like' , "%".$get['searchword']."%")->where('uid',$uid['uid'])->count();
            }
        }
        //遍历data
        foreach($data as $key => $value) {
            //将type改成字符串
            if($value['ptype'] == 1) {
                $data[$key]['ptype'] = '实时画面';
            }else {
                $data[$key]['ptype'] = '页面快照';
            }

            if($value['localdeploy'] == 1) {
                $data[$key]['localdeploy'] = true;
            }else {
                $data[$key]['localdeploy'] = false;
            }

            if(time() > $value['expiredate'] && $value['expiredate'] != 0 ){
                Db::name('publish')->where('pid',$value['pid'])->update(['extype'=>1]);
            }
            $data[$key]['createtime'] = date('Y-m-d H:i:s' , $value['createtime']);
            if($value['expiredate'] == 0){
                $data[$key]['expiredate'] = '永久';
            }else{
                $data[$key]['expiredate'] = date('Y-m-d H:i:s' , $value['expiredate']);
            }

        } 
        //返回
        return ['err' => 0,'data' => $data , 'total' => $total];
    }

    /**
     * 获取单个发布信息
     * pid 发布信息ID
     */
    public function getRelease()
    {
        //接收数据
        $input = input('get.');
        //判断数据是否接收成功
        if(!$input) {
            return get_status(1,'数据接收失败',2000);
        }
        $data = Db::name('publish')->where('pid',$input['pid'])->field('pid,scid,sname,is_pwd,expiredate,link,createtime,ptype,pdata,viewsnum')->select();
        return get_status(0,$data);

    }

    /**
     * 删除发布信息
     * pid 发布信息ID
     */
    public function deleteRelease()
    {
        //接收数据
        $input = input();
        //判断数据是否接收成功
        if(!isset($input['pid'])) {
            return get_status(1,'数据接收失败',2000);
        }
        $pid = explode(',',$input['pid']);
        //判断是否存在于轮播
        $pub = Db::name('publish')->where('scid','in',$pid)->field('link')->select();
        $pub = array_column($pub,'link');

        // $pub = implode(',',$pub);
        foreach($pub as $k=>$v){
        $delpub = Db::name('carouselrelease')->where('screens','like','%'.$v.'%')->select();
            if($delpub){
                return get_status(1,'大屏存在于轮播，取消失败',2035);
            }
        }
    
        $err = 0;
        //遍历pid
        foreach ($pid as $value) {
            //查询发布类型
            $type = Db::name('publish')->where('scid' , intval($value))->find();

            //判断是否为快照
            if($type['ptype'] == 1) {
                 //将大屏信息修改成未发布
                $update = Db::name('screen')->where('id',intval($value))->update(['publish' => 0]);
                if(!$update) {
//                    $err = 1;
                    return get_status(1,'更新大屏信息失败',2027);
                }
            } else {
                //将大屏信息修改成未发布
                $update = Db::name('screen')->where('id',intval($value))->update(['publish' => 0]);
                if(!$update) {
//                    $err = 1;
                    return get_status(1,'更新大屏信息失败',2027);
                }
                //查找快照大屏关联的大屏
                $shid = Db::name('publish')->where('scid',intval($value))->field('shid')->find();
                //如果是快照删除大屏
                $deleteScreen = Db::name('screen')->where('id' , intval($shid['shid']))->delete();
                if(!$deleteScreen) {
//                    $err = 1;
                    return get_status(1,'删除快照大屏失败',2028);
                }
                //删除大屏下图表
                $deleteChart = Db::name('screenchart')->where('screenid',intval($shid['shid']))->delete();
                if(!$deleteChart) {
//                    $err = 1;
                    return get_status(1,'图表删除失败',2005);
                }
                //删除图表的tconfig
                $deleteChartTconfig = Db::name('screencharttconfig')->where('screenid',intval($shid['shid']))->delete();
                if(!$deleteChartTconfig) {
//                    $err = 1;
                    return get_status(1,'图表删除失败',2005);
                }
            }  
            //删除发布信息
            $data = Db::name('publish')->where('scid' , intval($value))->delete();
            if(!$data) {
//                $err = 1;
                return get_status(1,'删除发布信息失败',6003);
            }
        }

        if($err) {
            return get_status(1,'取消发布失败',6003);
        }else {
            return get_status(0,'取消发布成功');
        }



    }

    /**
     * 修改发布信息
     */
    public function updateRelease()
    {
        //接收数据
        $input = input('put.');
        //判断数据是否接收成功
        if(!$input) {
            return get_status(1,'数据接收失败');
        }
        //判断密码是否为空
        if(isset($input['paswword'])) {
            if(!empty($input['paswword'])) {
                    $password = decrypt($input['password'],$input['len']);
                    unset($input['password']);
                    unset($input['len']);
                    $input['password'] = $password;
                }
        }
        //查询数据是否更改
        $vali = Db::name('publish')->where($input)->find();
        //判断是否更改
        if($vali) {
            return get_status(0,'修改成功');
        }
        //如果有更改修改数据库
        $update = Db::name('publish')->where('pid',$input['pid'])->update($input);

        if(!$update) {
            return get_status(1,'修改失败',6004);
        }else {
            return get_status(0,'修改成功');
        }
    }

    /**
     * 图表数值预警
     */
    public function numericalWarning()
    {
        //接收数据
        $input = input();
        //判断数据是否接收成功
        if(!$input) {
            return get_status(1,'数据接收失败',2000);
        }
        //取出需要匹配的key
        $keys = $input['key'];
        //去除需要匹配的data
        $data = $input['data'];
        //遍历data
        foreach($data as $key => $value) {
            //遍历最大值
            foreach($keys as $k => $val ) {    
               //判断data中是否有下边为val[0]键的数据
                if(isset($value[$val['key']])) {  
                    //$val[max]为最大值 $val[min]为最小值
                    if($value[$val['key']]  >= $val['max'] ) {
                        $data[$key][$val['key']] = '超出预算值';   
                    }
                    if($value[$val['key']]  <= $val['min'] ) {
                         $data[$key][$val['key']] = '低于预算值';
                    }
                 }
            }
        }
        //返回数据
        return get_status(0,$data);
    }

    /**
     * 收藏
     */
    public function collection()
    {
        $token = $this->Request->header('Access-token');
        // $token = $this->Request->header('token');
        // dump($token);die;
        //获取用户id
        $uid = Db::name('token')->where('token',$token)->field('uid')->find();

        //接收数据
        $input = input('post.');
        //判断数据是否接收成功
        if(!$input) {
            return get_status(1,'数据接收失败',2000);
        }
        
        if($input['status']) {
            //查询图表tconfig信息
            $tconfig = Db::name('screencharttconfig')->where('tid', $input['tid'])->field('id,selectDaid,tid',true)->find();
            //将状态该为已收藏
            $tconfig['collection'] = '1';
            //将图表tconfig信息加入到收藏数据库
            $insert = Db::name('collection')->insert(['tconfig' => json_encode($tconfig),'tid' => $input['tid'],'uid'=>$uid['uid']]);
            if(!$insert) {
                return get_status(1,'收藏失败',2016);
            }else {
                return get_status(0,'收藏成功');
            }
        }else {
            //将图表配置加入到收藏数据库
            $delete = Db::name('collection')->where('collectionid',$input['collectionid'])->where('uid',$uid['uid'])->delete();
            if(!$delete) {
                return get_status(1,'取消收藏失败',2017);
            }else {
                return get_status(0,'取消收藏成功');
            }
        }
    }

    /**
     * 查看收藏
     */
    public function getCollection()
    {
        //获取token
        $token = $this->Request->header('access-token');
        //通过token获取用户id
        $uid = Db::name('token')->where('token',$token)->field('uid')->find();
        //查处此用户的收藏信息
        $collection = Db::name('collection')->where('uid',$uid['uid'])->field('collectionid,tconfig,is_col')->select();

        foreach($collection as $k=>$v){
            //将tconfig转成数组
            $collection[$k] = json_decode($v['tconfig'],true);
            unset($collection[$k]['screenid']);
            unset($collection[$k]['key']);
            //将收藏id加入数组
            $collection[$k]['collectionid'] = $v['collectionid'];
            //将返回值改为bool
            $collection[$k]['collection'] = (bool)$collection[$k]['collection'];
            $collection[$k]['ishide'] = (bool)$collection[$k]['ishide'];
            $collection[$k]['islock'] = (bool)$collection[$k]['islock'];
            $collection[$k]['showBorder'] = (bool)$collection[$k]['showBorder'];
            $collection[$k]['resizable'] = (bool)$collection[$k]['resizable'];
            //将chardata转为数组
            $collection[$k]['chartData'] = json_decode($collection[$k]['chartData'],true);
            $collection[$k]['dataOpt'] = json_decode($collection[$k]['dataOpt'],true);
            unset($v['tconfig']);
        }
        return get_status(0,$collection);
    }

    /**
     * 查看数据类型映射
     */
    public function getDataType()
    {
        //查询数据类型
        $databasesList = Db::name('datamentname')->column('name');
        return get_status(0,$databasesList);
    }


    /**
     * 查看响应结果
     */
    public function responseResults()
    {
        //接收数据
        $input = input('post.');
        //判断数据是否接收成功
        if(!$input) {
            return get_status(1,'数据接收失败',2000);
        }
        //处理不同类型的传值使其在下面运行时保持键一致
        $input = $this->getInput($input);
        //处理不同类型的数据
        $data = $this->getData($input);
        if(empty($data)) {
            return get_status(0 , ['data' => [] , 'success' =>[]]);
        }
        //取出需要映射的key
        $keys = $input['map'];
        //判断data中的value是否是关联数组用于返回值类型统一
        foreach($data as $key => $value) {
            $vali = $this->isAssocArray($value);
            if($vali) {
                //获取数组的长度
                $count = count($value);
                //将数组的第一个元素拿出
                $arr0 = $data[$key][0];
                //将数组的第一个元素销毁
                unset($data[$key][0]);
                //将数组的第一个元素保存至最后一个
                $data[$key][$count] = $arr0;
            }
        }

        //定义返回数组,便于存储
        $arr = [];
        $i = 0;
        //遍历data
        foreach($data as $key => $value) {
            // //遍历键
            foreach($keys as $k => $val ) {
                //判断$val[1]是否是存在并且不是空值
                if(isset($val[1]) && $val[1] != ''){
                    //判断data数组中是否有关于keys数组中符合的值
                    if(isset($value[$val[1]])) {
                        //删除目标数组
                        unset($data[$key][$val[1]]);
                        //将匹配成功的项加入原数组
                        $data[$key][$val[0]] = $value[$val[1]];
                        
                    }
                }
                
            }
            $i++;
        }
        
        if(empty($data)) {
            return get_status(0 , ['data' => [] , 'success' =>[]]);
        }else {
            return get_status(0, $data);
        }
    }


    /**
     * 获取data
     */
    // protected function responseData($input)
    // {
        
    //     $data = [];
    //     switch ($input['type']) {
    //         case "STATIC" :
    //             //取出需要匹配的data
    //             $data = $input['sdata'];  
    //             break;
    //         case "Excel/Csv" :
    //             //从数据库中取出响应的值
    //             $data = $this->getDatament($input['selectedId']);
    //             break;
    //         case "API" :
    //             //判断是否有URL
    //             if($input['url'] == "") {
    //                 //从数据库中取值
    //                 $data = $this->getDatament($input['selectedId']);
    //             }else {
    //                 try{
    //                     //直接从API里面取值
    //                     $results = file_get_contents($input['url'] , false , stream_context_create($this->arrContextOptions));
    //                     //如果返回值为XML则转成json
    //                     if(xml_parser($results)){
    //                         $xml =simplexml_load_string($results);
    //                         $xmljson= json_encode($xml);
    //                         $result=json_decode($xmljson,true);
    //                     }else{
    //                         $result=json_decode($results,true);
    //                     }
    //                     //判断$result是否是二维数组
    //                     if(is_array($result)) {
    //                         $data= $this->toArray($result);
    //                     }else {
    //                         $data = json_decode($result,1);
    //                         if($data) {
    //                             $data = $this->toArray($data);
    //                         }
    //                     }
    //                 } catch (\Exception $e) {
    //                     return $data = ['err' => 1 , 'msg' => 'API请求失败','code' => 7012];
    //                 }
    //             }
    //             break;
    //         case "SQL" :
    //             //判断是否有URL
    //             if($input['sqlstr'] == "") {
    //                 //从数据库中取值
    //                 $data = $this->getDatament($input['selectedId']);
    //             }else {
    //                 //从数据库中拿到数据库配置ID
    //                 $configID = Db::name('datament')->where('daid' , $input['selectedId'])->find();
    //                 //判断是否查询成功
    //                 if(!$configID) {
    //                     return $data = ['err' => 1 , 'msg' => '数据库请求ID失败','code' => 7012];
    //                 }
    //                 //通过数据库配置ID拿到数据库配置
    //                 $DbConfig = Db::name('databasesource')->where('baseid' , $configID['sid'])->find();
    //                 //判断是否查询成功
    //                 if(!$DbConfig) {
    //                     return $data = ['err' => 1 , 'msg' => '数据库配置请求失败','code' => 7012];
    //                 }else {
    //                     $DbConfig = json_decode($DbConfig['baseconfig'],1);
    //                     $DbConfig['password'] = decrypt($DbConfig['password'],$DbConfig['len']);
    //                 }
    //                 //连接数据库执行sql
    //                 try{
    //                     if($DbConfig['type'] == 'oracle') {
    //                         $result = Db::connect($DbConfig)->query("$input[sqlstr]");
    //                     }else {
    //                         $result = Db::connect($DbConfig)->name($DbConfig['database'])->query("$input[sqlstr]");
    //                     }
    //                     //判断$result 的类型
    //                     foreach ($result as $key => $value) {
    //                         foreach ($value as $k => $v) {
    //                             if(is_resource($v)) { //如果为资源类型则转化为字符串
    //                                 $result[$key][$k] = stream_get_contents($v);
    //                             }
    //                         }
    //                     }
    //                 } catch (\Exception $e) {
    //                     return $data = ['err' => 1 , 'msg' => '执行sql失败','code' => 7012];
    //                 }
    //                 //判断$result是否是二维数组
    //                 if(is_array($result)) {
    //                     $data= $this->toArray($result);
    //                 }else {
    //                     $data = json_decode($result,1);
    //                     if($data) {
    //                         $data = $this->toArray($data);
    //                     }
    //                 }
    //             }
    //             break;
    //         case "WebSocket" :
    //             //判断是否有URL
    //             if($input['url'] == "") {
    //                 //从数据库中取值
    //                 $data = $this->getDatament($input['selectedId']);
    //             }else {
    //                 try{
    //                     //直接从API里面取值
    //                     $results = file_get_contents($input['url'] , false , stream_context_create($this->arrContextOptions));
    //                     //如果返回值为XML则转成json
    //                     if(xml_parser($results)){
    //                         $xml =simplexml_load_string($results);
    //                         $xmljson= json_encode($xml);
    //                         $result=json_decode($xmljson,true);
    //                     }else{
    //                         $result=json_decode($results,true);
    //                     }
    //                     //判断$result是否是二维数组
    //                     if(is_array($result)) {
    //                         $data= $this->toArray($result);
    //                     }else {
    //                         $data = json_decode($result,1);
    //                         if($data) {
    //                             $data = $this->toArray($data);
    //                         }
    //                     }
    //                 } catch (\Exception $e) {
    //                     return $data = ['err' => 1 , 'msg' => 'websock请求失败','code' => 7012];
    //                 }
    //             }
    //             break;
    //         case "自定义视图" :
    //             //从数据库中取值
    //             $data = $this->getDatament($input['selectedId']);
                
    //             break;
    //     }
    //     return $data;            
    // }

    /**
     * 获取大屏全部图表中的数据
     */
    public function getAllChart($input = [])
    {
        //判断是否dbconfig是否有值
        if(empty($input)) {
                //接收数据
            $input = input('get.');
            //判断数据是否接收成功
            if(!$input) {
                return get_status(1,'数据接收失败',2000);
            }
        }
        //判断是否是发布查询,用于将ID修改为历史快照
        if(isset($input['sharetype'])){
            $input['id'] = $this->getpublishId($input['id']);
        }
        // 如果开启了缓存
        if (config('addons.cachePlugin')['enabled']) {
            //判断是否是发布查询,用于将ID修改为历史快照
            $chartProcess = new \app\addons\cache\hook\ChartProcess;
            // 由条件查出数据并处理
            $data = $chartProcess->process($input);
            //返回数据
            return get_status(0,$data);
        }
        
        //判断传值是否为chartid判断查询大屏or图表
        if(isset($input['chartid'])) {
            //获取大屏的ID
            $chartid  = $input['chartid'];
            //查询大屏相关图表
            $result = Db::name('screenchart')->alias('screenchart')->join('screencharttconfig','screenchart.tid=screencharttconfig.tid')->where('screenchart.tid', $chartid)->select();
        }else {
            //获取大屏的ID
            $screenid  = $input['id'];
            $result = Db::name('screenchart')->alias('screenchart')->join('screencharttconfig','screenchart.tid=screencharttconfig.tid')->where('screenchart.screenid', $screenid)->order('screenchart.position DESC')->select();
            foreach($result as $k=>$v){
                if(!empty($v['parenttid'])){
                    unset($result[$k]);
                }
            }
        }
        //对图表进行数据的转换
        $data = $this->chartData($result);

        //返回数据
        return get_status(0,$data);

    }

    /**
     * 增对每个图表进行数据的处理
     */
    protected function chartData( $result )
    {
        // dump($result);die;
                //声明图表新数组
                $newArr = [];
                //声明数据新数组
                $chartData  = [];
                //遍历数据
                foreach ($result as $value) {
                    //以图表名字为下标作为键去除config中source
                    // dump($value);
                    $sour = json_decode($value['dataOpt'],true);
                    if(!isset($sour['source'])) {
                        continue;
                    } 
               
                    //取出图形中的映射信息
                    $source = $sour['source'];
                    // dump($source);die;
                    //将映射信息加入新数组
                    $newArr[$value['tname']] = $source;
                    //给定标识需要返回data
                    $newArr[$value['tname']]['returnData'] = true;
                    if(!isset($value['charttype'])) {
                        continue;
                    } 
                    //给图表类型加入新数组
                    $newArr[$value['tname']]['chartType'] = $value['charttype'];
                    
                    //判断图表数据是不是STATIC
                    if($newArr[$value['tname']]['type'] == 'STATIC'){
                        $newArr[$value['tname']]['sdata'] = json_decode($value['tdata'],1);
                    }        
                    $value['dataOpt'] = json_decode($value['dataOpt'],1);
                    // dump($value['dataOpt']);
                    //判断图表是否有map
                    if(!isset($value['dataOpt']['map'])) {
                        $newArr[$value['tname']]['map'] = [['name', ''],["value" , '']];    
                    }else {
                        $newArr[$value['tname']]['map'] = $value['dataOpt']['map'];
                    }
                    // $test = $this->testarr($newArr[$value['tname']]);
                    //将新数组使用mappin方法
                    $data = $this->Mapping($newArr[$value['tname']]);
            
                    if(isset($data['err']) && $data['err'] == 0) {
                        $chartData[$value['tname']] = [];
                    }else {
                        $chartData[$value['tname']] = $data;
                    }
                    
                    //判断数据是否为空,如果为空则返回图表的名字
                    if(empty($chartData[$value['tname']])) {
                        //通过图表名查询配置
                        $tconfig = Db::name('screencharttconfig')->where('key' , $value['tname'])->field('name')->find();
                        // //格式化配置
                        // $tconfig = '111';
                        //定义随机返回数据错误信息
                        $errorArr =['数据获取失败','数据映射失败','映射关系不正确','获取数据中存在错误']; 
                        //获取随机错误信息
                        $errorNo = mt_rand(0,3);
                        //将图表名字存储到空数组
                        $chartData[$value['tname']] =  '['.$tconfig['name'].']图表,' . $errorArr[$errorNo];
                    }
                    
                }
                
                //返回数据
                return $chartData;
    }
    // 测试数据映射
    // public function testarr($newArr)
    // {
    //     foreach($newArr['sdata'] as $key=>$val){
    //         foreach($newArr['map'] as $k=>$v){
                
    //         }
    //     }
    // }
    /**
	 * 获取发布大屏快照ID
	 */
	public function getpublishId($id)
	{
		//取出大屏的信息
		$data = Db::name("screen")->where('id',$id)->find();
		//判断是否发布
		if(!$data['publish']) {
			return get_status(1,'大屏未发布',2026);
		}
		//获取发布信息查看是否是历史快照发布
		$publish = Db::name('publish')->where('scid',$id)->find();
		//将发布浏览次数+1
		// Db::name('publish')->where('scid',$id)->update(['viewsnum' => $publish['viewsnum']+1]);
		//判断是否是历史快照发布
		if($publish['ptype'] == 2) {
			//如果是历史快照发布则使用历史快照ID
			return $publish['shid'];
		}else {
			return $id;
		}

	}


    /**
     * 获取数据源  发布待处理
     */
    public function getsource()
    {
        //接收数据
        $input = input('post.');
        //判断数据是否接收成功
        if(!$input) {
            return get_status(1,'数据接收失败',2000);
        }
        //获取用户token
        $arr = get_all_header();
        if(isset($arr['token'])){
            $this->token = $arr['token'];    
            //通过token获取用户的uid
            $uid = Db::name('token')->where('token',$this->token)->field('uid')->find();
            //通过uid获取用户的sid
            $category = Db::name('user')->where('uid',$uid['uid'])->field('sid')->find();	
            $cate = explode(',',$category['sid']);
            //分组 分类
            $datatype = Db::name('screengroup')->where('sid','in',$cate)->field('sid,screenname')->select();
        }else {
            $datatype = Db::name('screengroup')->field('sid,screenname')->select();
        }
        $screenname = [];
        for($i = 0;$i < count($datatype); $i++) {
            $screenname[] = $datatype[$i]['screenname'];
        }
        //查询相关数据源
        $source = Db::name('datament')->where('cid' , 'in' , $screenname)
                                      ->where('datatype',strtolower($input['datatype']))
                                      ->field('daid,dataname,filepath,returnsql')
                                      ->select();
        return get_status(0,$source);
    }


    /**
     * 查看关于sql及自定义视图的数据源
     */
    public function getSqlSource()
    {
        //接收数据
        $input = input('post.');
        //判断数据是否接收成功
        if(!$input) {
            return get_status(1,'数据接收失败',2000);
        }
        $keys = ["id","type"];
        //验证传值
        $vali = valiKeys($keys , $input);
        //判断传值是否满足
        if($vali['err'] != 0) {
            return get_status(1,$vali['data']);
        }

        if(strtolower($input['type']) == 'sql') {
            //查询关于数据库源的数据源
            $result = Db::name('datament')->where(['sid' => $input['id'],'datatype' => 'sql'])
                                        ->field('daid,sid,returnsql,dataname')
                                        ->select();
        }else {
            //查询关于数据库源的数据源
            $result = Db::name('datament')->where(['sid' => $input['id'],'datatype' => '自定义视图'])
                                        ->field('daid,sid,data,dataname')
                                        ->select();
                                        
            foreach ( $result as $key => $value) {
                $result[$key]['data'] = $this->toArray(json_decode($value['data'],1));
            }
        }
        //直接返回数据
        return get_status(0,$result);
    }

    /**
     * 记录自动刷新开始时间
     * $chartid
     */
    public function recordAutoRefresh()
    {
        //接收数据
        $input = input('get.');
        //判断数据是否接收成功
        if(!$input) {
            return get_status(1,'数据接收失败',2000);
        }
        //修改自动刷新开始时间
        $update = Db::name('screenchart')->where('tid' , $input['chartid'])->update(['autoupdatetime' => time()]);
    }

    /**
     * 获取静态数据
     */
    public function getStatusData($input = [])
    {
        //判断是否dbconfig是否有值
        if(empty($input)) {
            //接收数据
           $input = input('get.');
           // return $input;
           //判断数据是否接收成功
           if(!$input) {
               return get_status(1,'数据接收失败',2000);
           }
        }
      //获取图表的静态数据
      $statusData = Db::name('screenchart')->where("tid",$input['tid'])->field('tdata')->find();
      $config = Db::name('screencharttconfig')->field('chartSourceType,tid,id',true)->where('tid',$input['tid'])->find();

      //  判断是否有图表数据type
      if (isset($config['dataOpt'])) {
        $dataOpt = json_decode($config['dataOpt'],true);
        //将图表cinfig中的type改为static
        $dataOpt['source'] = ["type" => "STATIC"];
         //将图表configjson化
      $config['dataOpt'] = json_encode($dataOpt);
      }
     
      //将配置文件修改入库
      $updateConfig = Db::name('screencharttconfig')->where("tid",$input['tid'])->update($config);
      if($statusData) {
        $status = json_decode($statusData['tdata'],1);
      }else  {
        $status = [];
      }


      //获取处理完成之后的数据
      $resultData = $this->getAllChart(['chartid'=> $input['tid'],'sdata' => $status]);
      //获取图表具体的数据
      if(isset(array_values($resultData['data'])[0])) {
        $resultData = array_values($resultData['data'])[0];
      }else {
        $resultData = [];
      }
      //返回数据
      return get_status(0,['statusData' => $status,'resultData' => $resultData]);
    }

    /** 
     * 一键保存大屏数据
     */
    public function saveallchart()
    {
        $post = input('post.');
        if(empty($post)){
            return get_status(1,'数据有误');
        }
        //将大屏信息转为数组
        $screenOption = json_encode($post['screenOption']);
        // dump($screenOption);die;
        //查询大屏信息是否修改
        $DaScreen = Db::name('screen')->where(['id'=>$post['screenid'],'data'=>$screenOption])->find();
        //不一致则代表修改
        if(!$DaScreen){
            $screen = Db::name('screen')->where(['id'=>$post['screenid']])->update(['data'=>$screenOption]);
            if(!$screen){
                return get_status(1,'大屏数据保存失败',2031);
            }
        }
        foreach($post['layer'] as $kk=>$vv){
            //保存图表定位
            $position = Db::name('screenchart')->where(['tname'=>$vv])->update(['position'=>$kk]);
        }
        if(!empty($post['position'])){
        //更新图表数据
        foreach($post['position'] as $k=>$v){
            if(isset($v['chartData'])) {
                $v['chartData'] = json_encode($v['chartData']);
            }
            if(isset($v['dataOpt'])) {
                $v['dataOpt'] = json_encode($v['dataOpt']);
            }
            if(isset($v['drilling'])){
                $v['drilling'] = json_encode($v['drilling']);
            }
            $res = Db::name('screencharttconfig')->where(['chartData'=>$v['chartData']])->select();
            if(!$res){
                $update = Db::name('screencharttconfig')->where(['tid'=>$v['tid']])->update($v);
                if(!$update){
                    return get_status(1,'大屏数据保存失败',2031);
                }
            }
        }
    }
            return get_status(0,'大屏数据保存成功');
    }

    //导出大屏 TODO
    public function exportScreen()
    {
        //接收数据
        $input = input('get.');
        //验证数据
        $vali = valiKeys(['id'] , $input);
        if($vali['err']) {
            return get_status(1,"参数不完整" , 1050);
        }
        //压缩包名字前拼接英文，防止全中文无法解压
        $yname = 'k';
        $rooturl = ROOT_PATH . 'public';
        //读取json文件
        $inturl = self::jspath();
        //实例化model
        $screenModel = new ScreenModel();
        $screenChartModel = new Screenchart();
        //获取大屏的配置信息
        $screenInfo = $screenModel->getScreenInfo($input['id']);
        $exportScreenArr['screenOption'] = json_encode($screenInfo,JSON_UNESCAPED_SLASHES);
        //获取大屏所有的图表
        $screenChartList = $screenChartModel->getScreenList($input['id']);
        //获取大屏图表tconfig
        $scrtconfig = Db::name('screencharttconfig')->where('screenid',$input['id'])->field('id',true)->select();
        //将所有图表的数据类型改为静态数据,并吧静态数据加入到data
        $screenChartListSTATIC  = $this->chartDataSTATIC($screenChartList , $input['id'],$scrtconfig);
        // dump($screenChartListSTATIC);die;
       if(empty($screenChartListSTATIC)){
           return get_status(1,'暂无图表',7013);
       }
         //将图表tconfig存入数组
        foreach($screenChartListSTATIC as $key=>$val){
            foreach($scrtconfig as $kk=>$vv){
                if($vv['tid'] == $val['tid']){
                    unset($vv['tid']);
                    $screenChartListSTATIC[$key]['scrtconfig'] = json_encode($vv); 
                }
            }
        }
        //将图表数据加入导出数组
        $exportScreenArr['position'] = $screenChartListSTATIC;
          //查询大屏封面路径
          $fimg = Db::name('screen')->where("id" ,$input['id'])->value('imgdata');
          if(!empty($fimg)){
              $newUrl = $this->fizip($yname.$screenInfo['name']. '.' .'zip',$rooturl . $fimg,true);
              //    将封面图加入数组
              $exportScreenArr['imgdata'] = $newUrl;
          }
        //对图表图片路径进行处理将数据写入文件
        $screenJson = $this->exProcessing($exportScreenArr,$inturl,$screenInfo,$yname);
        // //将json写入文件指定文件地址
        $path = ROOT_PATH ."public" . DS ."alg" . DS ; //绝对地址
        if(!file_exists($path)) { //判断文件是否存在
            mkdir($path , 0777);
        }
        //将文件写入json
        $res = file_put_contents($path.$yname.$screenInfo['name'].'.json' , $screenJson);
        //生成压缩包返回新josn文件路径
        $jsonUrl = $this->fizip($yname.$screenInfo['name'].'.'.'zip',$screenJson,false);
        //判断写入是否成功
        if($res) {
            return get_status( 0 , $jsonUrl);
        }else{
            return get_status( 1 , "网络繁忙" , 100000);
        }
    }

    /**
     * 将图表变化为静态数据
     * @param $screenchart 图表数组
     * @param $id   大屏ID
     * @return array
     */
    public function chartDataSTATIC($screenchart ,$id,$scrtconfig)
    {
        //遍历所有图表信息
            foreach ($screenchart as $key => $value) {
                //销毁自增ID
                // unset($screenchart[$key]['tid']);
                //将大屏关联修改为当前快照大屏
                $screenchart[$key]['screenid'] = $id;
                //设置创建及修改时间
                $screenchart[$key]['createtime'] = time();
                $screenchart[$key]['updatetime'] = time();
                //将配置文件读取
                $tconfig = $scrtconfig[$key];
                //判断是否有dataOpt
                if(!isset($tconfig['dataOpt'])) {
                    continue;
                }
                $dataOpt = json_decode($tconfig['dataOpt'],true);
                //将数据源配置取出
                $source = $dataOpt['source'];
                // dump($source);die;

                //判断数据源是否为静态数据
                if($source['type'] == "STATIC") {
                    continue;
                }
                //取出map
                $map = $dataOpt['map'];
                //将映射关系,数据源配置及tid加入数组
                $source['map'] = $map;
                $source['tid'] = $value['tid'];

                //处理不同类型的传值使其在下面运行时保持键一致
                // $source = $this->getInput($source);
                //获取图表数据
                // $data = $this->getData($source);
                //删除配置项中的source
                unset($dataOpt['source']);
                //添加配置项中['dataOpt']['source']['type']为STATIC
                $dataOpt['source']['type'] = "STATIC";
                //将自动更新关闭
                $dataOpt['autoUpdate'] = false;
                $tconfig['dataOpt'] = json_encode($dataOpt);
                //将数据存入图表的data中
                // $screenchart[$key]['tdata'] =  json_encode($data,JSON_UNESCAPED_UNICODE);
                //将配置转为json格式并存入配置项
                $screenchart[$key]['scrtconfig'] = json_encode($tconfig,JSON_UNESCAPED_UNICODE);
            }
            $result = [];
            //遍历数组 将数组下标变为图表名字
            foreach ($screenchart as $key => $value) {
                $result[$value['tname']] = $value;
            }
            return $result;
    }

    /**
     * 处理大屏图片
     * @return false|mixed|string
     */
    protected function exportScreenImage($screenInfo)
    {
        //将大屏配置config转换为数组
        $screenConfig = json_decode($screenInfo['data'] , 1);
        //设置图片地址
        $exportPath = ROOT_PATH . "public" . DS  . "exportScreen"   ;
        //判断文件目录是否存在
        if ( !file_exists($exportPath)) {
            mkdir($exportPath, 0777);
        }
        $filepath = $screenConfig['background']['image'];
        //获取文件内容
        try {
            $file = file_get_contents($screenConfig['background']['image']);
        }catch (Exception $e) {
            return $screenInfo;
        }
        //获取文件在服务器的目录
        $fileServerPath = substr($filepath , strrpos( $filepath , '.com') + 1);
        //设置文件名称
        $filename = substr($filepath , strrpos( $filepath , '/') + 1);
        //将图片放入指定文件夹
//        $res = file_put_contents($exportPath,$file);
        dump($filepath);
    }

    //导入大屏
    public function importScreen()
    {
        Db::startTrans();
        try {
            //接收大屏ID
            $input = input('post.');
            //保存上传的文件的内容
            $fileInfo  = $this->getFilePath();
            //判断文件是否接受成功并且清空大屏数据
            self::delScreenData($fileInfo,$input);
            //读取json文件，或得服务器配置
            $inturl = self::jspath();
            //读取json文件
            $screenData = self::jsonData($fileInfo);
            //判断是否读取成功
            if(isset($screenData['err']) && $screenData['err'] = 1){
                return $screenData;
            }
            //定义背景图地址，默认为空
            $imgdata = '';
            //获取大屏背景图路径
            $scr = json_decode($screenData['screenOption'],1);
            if(isset($scr['background']['image'])){
                $scr['background']['image'] = $inturl  .$scr['background']['image'];
            }
            $screenData['screenOption'] = json_encode($scr,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
            //判断背景是否为图片
            if(isset($screenData['imgdata'])){
                $imgdata = $screenData['imgdata'];
            }
            //将大屏信息存入数据库
            $data = [
                'data'    => $screenData['screenOption'],
                'imgdata' => $imgdata
            ];
            $Scrselect = Db::name('screen')->where(['id'=>$input['sid'],'data'=>$screenData['screenOption'],'imgdata'=>$imgdata])->find();
            if(!$Scrselect){
                $insert = Db::name('screen')->where(['id'=>$input['sid']])->update($data);
                if(!$insert){
                    return get_status(1,"大屏导入有误",2032);
                }
            }
            //将图表数据存入数据库
            self::imInsertScreen($screenData['position'],$input);
            Db::commit();
            return get_status(0,'');
        }catch (\Exception $e) {
            Db::rollback();
            return get_status(1,"大屏导入有误,请确认文件是否正确",2032);
        }

    }

    /**
     * @return 变量结构|mixed 错误返回错误信息  成功返回json装换的数组
     */
    public function getFilePath()
    {
        //接收文件
        $file = request()->file('screenData');
        //判断文件是否获取成功
        if ($file) {
            //定义路径
            $path = ROOT_PATH . 'public' . DS . 'json_tmp';
            //移动文件
            $info = $file->move($path);
           
            //判断文件移动结果
            if ($info) {
                $data = [
                    'name'=>$file->getinfo('name'),
                    'url' =>$path . DS . $info->getSaveName()
                ];
                //返回图片路径
                return get_status( 0 ,$data );
            }else {
                // 上传失败获取错误信息
                return get_status(1, $file->getError(), 1080);
            }
        }else {
            return get_status(1, "文件不存在", 1080);
        }

    }

    public function fizip($zipName,$fileurl,$type)
    {
        //定义导出图片路径
        $zipUrl =  DS . 'image';
        $zipPath = ROOT_PATH ."public" . DS ."alg" . DS; //定义压缩包路径
          $zip = new \ZipArchive(); 
          if(!file_exists($zipPath)) { //判断文件是否存在
              mkdir($zipPath , 0777);
          }
          $filename = $zipPath . $zipName; // 压缩包所在的位置路径
          $zip->open($filename,\ZipArchive::CREATE);   //打开压缩包
          if($type){
            //根据路径截取文件名
            $Pname = substr($fileurl,strrpos($fileurl,'/'));
            $zip->addFromString('image/'.$Pname,file_get_contents($fileurl));
            //返回新文件路径
            $newUrl = $zipUrl . $Pname;
          }else{
              //读取json文件，或得服务器配置
              $inturl = self::jspath();
              //去除后缀或得文件名
              $Pname = substr($zipName,0,strpos($zipName, '.'));
              //将json数据生成json文件
              $zip->addFromString($Pname.'.json',$fileurl);
              //压缩包所在路径
              $newUrl = $inturl  . DS ."alg" . DS . $Pname . '.zip';
          }
          return $newUrl;
    }
    /**
     *导入时清空大屏数据
     * $fileInfo  上传文件内容
     * $input 大屏id
     */
    public static function delScreenData($fileInfo,$input)
    {
        //判断文件是否接收成功
        if ($fileInfo['err']) {
            return get_status(1 , $fileInfo['data']['url'] , $fileInfo['status']);
        }
        //查询该大屏下是否有图表
        $screenTu = Db::name('screenchart')->where(['screenid'=>$input['sid']])->select();
        
        if($screenTu){
      
        $delTconfig = Db::name('screencharttconfig')->where('screenid',$input['sid'])->delete();
            //清空当前表的图表数据
        $del = Db::name('screenchart')->where(['screenid'=>$input['sid']])->delete();
        
        if(!$del){
            return get_status(0,'图表删除失败',2005);
            }

        if(!$delTconfig){
            return get_status(0,'图表数据删除失败',2005);
            }
        }
    }
    /**
     * 将图表数据存入数据库
     * $position 图表数据
     * $input 大屏id
     */
    public static function imInsertScreen($position,$input)
    {
        foreach($position as &$screenV){
            $tconfig = $screenV['scrtconfig'];
            $tconfig['chartData'] = json_decode($tconfig['chartData'],1);
                  //判断小图标是否存在
        if(isset($tconfig['chartData']['iconObj']['url'])){
            //判断是否为空
            if(!empty($tconfig['chartData']['iconObj']['url'])){
                $image = substr($tconfig['chartData']['iconObj']['url'],0,7);
                if(strpos($tconfig['chartData']['iconObj']['url'],$image) !== false){
                    //将新路径放入json
                    $tconfig['chartData']['iconObj']['url'] = self::jspath() .$tconfig['chartData']['iconObj']['url'];
                    }
                }
          }

          //判断图片是否存在
          if(isset($tconfig['chartData']['url'])){
              //判断该字段不为空
              if(!empty($tconfig['chartData']['url'])){
                $image = substr($tconfig['chartData']['url'],0,7);
                if(strpos($tconfig['chartData']['url'],$image)!== false){
                    //将新路径放入json
                    $tconfig['chartData']['url'] = self::jspath() .$tconfig['chartData']['url'];
                }
              }
          }
          $screenchartTconfig = $screenV['scrtconfig'];
          //去除无用值
          unset($screenV['scrtconfig']);
          unset($screenV['tid']);
          //将图表数据插入并返回自增id
          $screenV['screenid'] = $input['sid'];
          $screenTid = Db::name('screenchart')->insertGetId($screenV);

          //将自增id加入数组
          $screenchartTconfig['tid'] = $screenTid;
          //替换大屏id
          $screenchartTconfig['screenid'] = $input['sid'];
          //添加tconfig数据
          $insertTconfig = Db::name('screencharttconfig')->insert($screenchartTconfig);
            if(!$insertTconfig){
              return get_status(1,"大屏导入有误",2032);
            }
        }
    }

    //读取json配置文件
    public static function jspath()
    {
        $jsPath = configJson()['config'];
        $inturl = $jsPath['setting']['server'];
        return $inturl;
    }
    /*
     *  读取大屏json文件     
     * $fileInfo  上传文件内容
     **/
    public static function jsonData($fileInfo)
    {
        $zip = new \ZipArchive();
        //打开压缩文件
        if($zip->open($fileInfo['data']['url']) === true){
            $zip->extractTo(ROOT_PATH . 'public');
            $zip->close();
        }else{
            return get_status(1,'解压文件错误',2033);
        }
        //获取文件名
        $length = strlen($fileInfo['data']['name']);
        //压缩包名字和json文件名字一致，截取zip后缀转成json文件
        $name = substr($fileInfo['data']['name'],0,$length-4) . '.json';
        //检测文件名是否一致
        if(!file_exists(ROOT_PATH . 'public' . DS . $name)){
            return get_status(1,'文件名错误',4013);
            // return false;
        }
        //获取
        $screenData = file_get_contents(ROOT_PATH . 'public' . DS . $name);
        @unlink($fileInfo['data']);
        //取出\n 和空格
        // $screenData = str_replace(" " , "" , $screenData);
        $screenData = str_replace("\n" , "" , $screenData);
        //转换成数组
        $screenData = json_decode($screenData,1);
        return $screenData;
        
    }

    /**
     * 导出时图表图片处理
     * $inturl  json配置文件
     * $screenInfo 获取大屏的配置信息
     * $yname   压缩包前缀
     */
    public  function exProcessing($exportScreenArr,$inturl,$screenInfo,$yname)
    {
         //获取字符串长度
        $weblen = strlen($inturl);
        $rooturl = ROOT_PATH . 'public';
        
        foreach($exportScreenArr['position'] as $k=>&$v){
            $tconfig = json_decode($v['scrtconfig'],1);
            $tconfig['chartData'] = json_decode($tconfig['chartData'],1);
            // dump($tconfig['chartData']);die;
              //判断小图标是否存在
        if(isset($tconfig['chartData']['iconObj']['url'])){
            //判断是否为空
            if(!empty($tconfig['chartData']['iconObj']['url'])){
                if(strpos($tconfig['chartData']['iconObj']['url'],$inturl)!== false){
                    $url = substr($tconfig['chartData']['iconObj']['url'],$weblen);
                    //放入压缩包后的图片路径    
                    $newUrl = $this->fizip($yname.$screenInfo['name']. '.' .'zip',$rooturl . $url,true);
                    //将新路径放入json
                    $tconfig['chartData']['iconObj']['url'] = $newUrl;
                }
            }
          }

          //判断图片是否存在
          if(isset($tconfig['chartData']['url'])){
              //判断该字段不为空
              if(!empty($tconfig['chartData']['url'])){
                //   dump($tconfig['chartData']['url']);
                if(strpos($tconfig['chartData']['url'],$inturl)!== false){
                    $url = substr($tconfig['chartData']['url'],$weblen);
                    // dump($url);
                    //放入压缩包后的图片路径    
                    $newUrl = $this->fizip($yname.$screenInfo['name']. '.' .'zip',$rooturl . $url,true);
                    // dump($newUrl);
                    //将新路径放入json
                    $tconfig['chartData']['url'] = $newUrl;
                }
              }
          }
          $tconfig['chartData'] = json_encode($tconfig['chartData'],JSON_UNESCAPED_SLASHES);
          $v['scrtconfig'] = $tconfig;
        }
        //检测背景图是否存在
        $jsondata = json_decode($exportScreenArr['screenOption'],1);
         $data    = json_decode($jsondata['data'],1);
        if(isset($data['background']['image'])){
            if(!empty($data['background']['image'])){
                if(strpos($data['background']['image'],$inturl)!== false){
                $url = substr($data['background']['image'],$weblen);
                //放入压缩包后的图片路径    
                $newUrl = $this->fizip($yname.$screenInfo['name']. '.' .'zip',$rooturl . $url,true);
                //将新路径放入json
                $data['background']['image'] = $newUrl;
                }
            }
        }
        //将data数组转为json
        $jsondata = json_encode($data,JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);
        //将背景图数组转为json
        // $exportScreenArr['screenOption'] = json_encode($jsondata,JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);
        $exportScreenArr['screenOption'] = $jsondata;
        //将数组JSON序列化
        $screenJson = json_encode($exportScreenArr,JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);
        return $screenJson;
    }

    //离线部署 (多出同时导出离线文件是会出错)
    public function exportLarge()
    {
        $post = input('post.');
        if(empty($post['data'])){
            return get_status(1,'参数不能为空',5002);
        }
        //压缩包文件路径
        $Yurl = ROOT_PATH.'public/dist.zip';
        //离线部署前端包路径
        $Burl = ROOT_PATH .'public/offline';
        //检测dist压缩包是否存在
        if(is_file($Yurl)){
            unlink($Yurl);
        }
        //检测是否有api文件
        if(is_file(ROOT_PATH.'public/offline/static/api.json')){
            unlink(ROOT_PATH.'public/offline/static/api.json');
        }
        //检测是否有视频资源
        if(file_exists($Burl.'/video')){
            $this->delDir($Burl.'/video');
        }
        //判断数据源是否符合标准
        foreach($post['data']['data']['position'] as $k=>&$v){
            if(isset($v['dataOpt'])){
                if($v['dataOpt']['source']['type'] != 'API' && $v['dataOpt']['source']['type'] != 'STATIC'){
                    return get_status(1,'数据源不符合标准',4012);
                }
            }
            //对图片资源进行替换
            if(isset($v['chartData']['url']) && !empty($v['chartData']['url'])){
                $image = substr($v['chartData']['url'],0,6);
                //路径有两种，正常上传的和导出的大屏再导入大屏
                if($image == '/image'){
                    $url = $this->localization(ROOT_PATH.'/public'.$v['chartData']['url']);
                    $v['chartData']['url'] = $url;
                }else{
                    $url = $this->localization($v['chartData']['url']);
                    $v['chartData']['url'] = $url;
                }
            }
            //对轮播图资源进行替换
            if(isset($v['chartData']['dataUrl']) && !empty($v['chartData']['dataUrl'])){
                $url = [];
                foreach($v['chartData']['dataUrl'] as $key=>$val){
                    //下载图片到本地并放入压缩包
                    $url[] = $this->localization($val);
                    //修改chartData数组里轮播图图片路径
                    $v['chartData']['dataUrl'] = $url;
                }
            }
            //对富文本资源进行替换
            if($v['charttype'] == 'fuwenben'){
            if(isset($v['chartData']['data']) && !empty($v['chartData']['data'])){
                //过滤img标签正则
                $pregRule = "/<[img|IMG].*?src=[\'|\"](.*?(?:[\.jpg|\.jpeg|\.png|\.gif|\.bmp]))[\'|\"].*?[\/]?>/";
                //匹配符合的数据并存入$array数组
                preg_match_all($pregRule,$v['chartData']['data'],$array,PREG_PATTERN_ORDER);
                //声明一个新数组
                $replaceArr = array();
                foreach($array[1] as $value) {
                    //检测是否为本地文件
                    if(strstr($value ,'/KindEditor/attached/image/')){
                        // dump($value);die;
                        //对本地文件进行下载
                        array_push($replaceArr,$this->localization($this->jspath().$value));
                    }
                }
                //替换图片路径
                $replaceStr = str_replace($array[1], $replaceArr, $v['chartData']['data']);
                $v['chartData']['data'] = $replaceStr; 
                $v['dataOpt']['tdata'][0]['richtext'] = $replaceStr;
            }
        }
            //对视频资源进行替换
            if(isset($v['chartData']['playerOptions']['sources'][0]['src']) && !empty($v['chartData']['playerOptions']['sources'][0]['src'])){
                $url = $this->dlfile($v['chartData']['playerOptions']['sources'][0]['src'],$Burl);
                $v['chartData']['playerOptions']['sources'][0]['src'] = $url;
            }
            
            //将api地址与图表名组成新数组
            if(isset($v['dataOpt']['source']['type'])){
            if($v['dataOpt']['source']['type'] == 'API'){
                $api[$k]['name'] =$v['name']; 
                $api[$k]['apiURL'] = $v['dataOpt']['source']['apiURL'];
                $api[$k]['qtime'] =$v['dataOpt']['source']['qtime'];
            }
            }
        
        }
        //对大屏背景图进行替换
        if($post['data']['data']['screenOption']['bgtype']=='image' && !empty($post['data']['data']['screenOption']['background']['image'])){
                $bgimg = $this->localization($post['data']['data']['screenOption']['background']['image']);
            $post['data']['data']['screenOption']['background']['image'] = $bgimg;
        }

        //将json数据写入json文件
        $data = file_put_contents('offline/static/screenoption.json',json_encode($post['data'],JSON_UNESCAPED_UNICODE));
        //将图表对应api接口写入json文件
        if(isset($api)){
            $apifile = file_put_contents('offline/static/api.json',json_encode($api,JSON_UNESCAPED_UNICODE));
        }
        if(!$data){
            return get_status(1,'导出文件有误',5010);
        }

        $zip = new \ZipArchive();  
        //参数1:zip保存路径，参数2：ZIPARCHIVE::CREATE没有即是创建  
        if(!$zip->open("dist.zip",\ZIPARCHIVE::CREATE))  
        {  
            return get_status(1,'文件压缩失败',1080);
        }  
        //echo "创建[$exportPath.zip]成功<br/>";  
        $this->createZip(opendir($Burl),$zip,$Burl);  
        $zip->close();
        //压缩包文件路径
        $file = self::jspath() .'/dist.zip';
        return get_status(0,$file);
    }

    //导出本地化对图片进行处理
    public function localization($fileurl)
    {
        //定义导出图片路径
        $zipUrl =  DS . 'image';
        $fileurl = trim($fileurl, '/');
        $zip = new \ZipArchive(); 
        $zip->open('dist.zip',\ZipArchive::CREATE);   //打开压缩包
        //根据路径截取文件名
        $Pname = substr($fileurl,strrpos($fileurl,'/'));
        $zip->addFromString('image/'.$Pname,file_get_contents($fileurl));
        //返回新文件路径
        $newUrl = $zipUrl . $Pname;
        return $newUrl;
    }

    //压缩多级文件夹
    function createZip($openFile,$zipObj,$sourceAbso,$newRelat = '')  
    {  
        while(($file = readdir($openFile)) != false)  
        {  
            if($file=="." || $file=="..")  
                continue;  
            
            /*源目录路径(绝对路径)*/  
            $sourceTemp = $sourceAbso.'/'.$file;  
            /*目标目录路径(相对路径)*/  
            $newTemp = $newRelat==''?$file:$newRelat.'/'.$file;  
            if(is_dir($sourceTemp))  
            {  
                //echo '创建'.$newTemp.'文件夹<br/>';  
                $zipObj->addEmptyDir($newTemp);/*这里注意：php只需传递一个文件夹名称路径即可*/  
                $this->createZip(opendir($sourceTemp),$zipObj,$sourceTemp,$newTemp);  
            }  
            if(is_file($sourceTemp))  
            {  
                //echo '创建'.$newTemp.'文件<br/>';  
                $zipObj->addFile($sourceTemp,$newTemp);  
            }  
        }  
    }
    
    //存储大屏
    public function storagescreen()
    {
        $post = input('post.');
        if(empty($post['screenid'])){
            return get_status(1,'参数不能为空',5002);
        }
        $screen = Db::name('restorescreen')->where('screenid',$post['screenid'])->find();
        if($screen){
            Db::name('restorescreen')->where('screenid',$post['screenid'])->delete();
        }
        //根据大屏id查处相关大屏配置
        $screen = Db::name('screen')->where('id',$post['screenid'])->find();
        //字段有变，注销
        unset($screen['id']);
        $screen['screenid'] = $post['screenid'];
        //将大屏配置贮存
        $insert = Db::name('restorescreen')->insert($screen);
        //删除上一次的大屏相关的图表存储记录
        Db::name('restorechart')->where('screenid',$post['screenid'])->delete();
        //查询相关图表
        $chart = Db::name('screencharttconfig')->alias('st')->join('screenchart','st.tid=screenchart.tid')->where('st.screenid',$post['screenid'])->select();
        if(empty($chart)){
            return get_status(0,'存储成功');
        }
        foreach($chart as $k=>$v){
            $data[$k]['screencharttconfig'] = json_encode(array_slice($v,0,23));
            $data[$k]['screenchart'] = json_encode(array_slice($v,23,30));
            $data[$k]['tid'] = $v['tid'];
            $data[$k]['screenid'] = $v['screenid'];
        }
        $insertall = DB::name('restorechart')->insertAll($data);
        if(!$insertall){
            return get_status(1,'存入数据库失败',1045);
        }else{
            return get_status(0,'存储成功');
        }
    }

    //复原大屏
    public function restorescreen()
    {
        $post = input('post.');
        if(empty($post)){
            return get_status(1,'参数不能为空',5002);
        }
        //查询存储的大屏数据
        $screen = DB::name('restorescreen')->where('screenid',$post['screenid'])->field('id,screenid',true)->find();
        
        //将大屏id存入数组
        $screen['id'] = $post['screenid'];
        //更新大屏配置
        $update = Db::name('screen')->where('id',$post['screenid'])->update($screen);
        //删除相关图表
        Db::name('screenchart')->where('screenid',$post['screenid'])->delete();
        //删除相关图表的配置
        Db::name('screencharttconfig')->where('screenid',$post['screenid'])->delete();
        //查询存储的相关图表数据
        $chart = Db::name('restorechart')->where('screenid',$post['screenid'])->select();
        if(empty($chart)){
            return get_status(0,'恢复成功');
        }
        foreach($chart as $k=>$v){
            //将图表信息存入数组
            $screenchart[$k] = json_decode($v['screenchart'],1);
            $screenchart[$k]['tid'] = $v['tid'];
            $screenchart[$k]['screenid'] = $v['screenid'];
            //将图表配置信息存入数组
            $screencharttconfig[$k] = json_decode($v['screencharttconfig'],true);
        }
        $insertchart = Db::name('screenchart')->insertAll($screenchart);
       
        if(!$insertchart){
            return get_status(1,'图表更新失败',2034);
        }
        $inserttconfig = Db::name('screencharttconfig')->insertAll($screencharttconfig);
        if(!$inserttconfig){
            return get_status(1,'图表更新失败',2034);
        }
        return get_status(0,'恢复成功');
    }

    //下载视频到本地
  public function dlfile($fileUrl,$Burl)
  {
    if(!file_exists($Burl.'/video')){
        mkdir($Burl.'/video',0777,true);
    }
    //获取文件后缀
    $suffix = substr($fileUrl,strrpos($fileUrl,'.'));
    //读取图片信息
    $content = file_get_contents($fileUrl);
    //生成文件名
    $filename = time() . rand(1000,10000) . $suffix;
    //将图片写入文件夹
    file_put_contents(ROOT_PATH.'public/offline/video/'.$filename,$content);
    //获取文件路径
    return 'video/'.$filename;
  }

    //删除文件及文件夹
    function delDir($path, $del = false)
    {
    //打开所选文件
    $handle = opendir($path);
    if ($handle) {
        //函数返回目录中下一个文件的文件名
        while (false !== ($item = readdir($handle))) {
        //排除. ..文件
        if (($item != ".") && ($item != "..")) {
            //删除文件
            is_dir("$path/$item") ? delDir("$path/$item", $del) : unlink("$path/$item");
        }
        }
        closedir($handle);
        if ($del) {
        //删除空目录
        return rmdir($path);
        }
        //检测文件或目录是否存在
        }elseif (file_exists($path)) {
        return unlink($path);
        }else {
            return false;
        }
    }
    //查看下钻图表映射后的数据
    public function getdrillchart()
    {
        $get = input('post.');
        //下钻图表数据的筛选条件
        $filedsparams = $get['filedsparams'];
        //判断数据是否接收成功
        if(!$get) {
        return get_status(1,'数据接收失败',2000);
        }
        $chartid = $get['chartid'];
        $result = Db::name('screenchart')->alias('screenchart')->join('screencharttconfig','screenchart.tid=screencharttconfig.tid')->where('screenchart.tid', $chartid)->select();
        $data = $this->getdrill($result,$filedsparams);
        return get_status(0,$data);
    }
    //处理下钻图表数据
    public function getdrill($result,$filedsparams)
    {
                //声明图表新数组
                $newArr = [];
                //声明数据新数组
                $chartData  = [];
                //遍历数据
                foreach ($result as $value) {
                    //去除config中source
                    $sour = json_decode($value['dataOpt'],true);
                    if(!isset($sour['source'])) {
                        continue;
                    } 
               
                    //取出图形中的映射信息
                    $source = $sour['source'];
                    // dump($source);die;
                    //将映射信息加入新数组
                    $newArr = $source;
                    //给定标识需要返回data
                    $newArr['returnData'] = true;
                    if(!isset($value['charttype'])) {
                        continue;
                    } 
                    //给图表类型加入新数组
                    $newArr['chartType'] = $value['charttype'];
                    
                    //判断图表数据是不是STATIC
                    if($newArr['type'] == 'STATIC'){
                        $newArr['sdata'] = json_decode($value['tdata'],1);
                    }       
                    $value['dataOpt'] = json_decode($value['dataOpt'],1);
                    // dump($value['dataOpt']);
                    //判断图表是否有map
                    if(!isset($value['dataOpt']['map'])) {
                        $newArr['map'] = [['name', ''],["value" , '']];    
                    }else {
                        $newArr['map'] = $value['dataOpt']['map'];
                    }
                    // $test = $this->testarr($newArr[$value['tname']]);
                    //将新数组使用mappin方法
                    $data = $this->Mapping($newArr,$filedsparams);
                    if(isset($data['err']) && $data['err'] == 0) {
                        $chartData = [];
                    }else {
                        $chartData = $data;
                    }

                    //判断数据是否为空,如果为空则返回图表的名字
                    if(empty($chartData)) {
                        //通过图表名查询配置
                        $tconfig = Db::name('screencharttconfig')->where('tid' , $value['tid'])->field('name')->find();
                        //定义随机返回数据错误信息
                        $errorArr =['数据获取失败','数据映射失败','映射关系不正确','获取数据中存在错误']; 
                        //获取随机错误信息
                        $errorNo = mt_rand(0,3);
                        //将图表名字存储到空数组
                        $chartData =  '['.$tconfig['name'].']图表,' . $errorArr[$errorNo];
                    }
                    
                }
                
                //返回数据
                return $chartData;
    }

}
