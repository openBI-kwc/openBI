<?php
/**
 * Created by PhpStorm.
 * User: 王伟康
 * Date: 2019/08/02
 * Time: 10:44
 */
namespace app\index\Controller;
use think\Loader;
use think\Request;
use think\Db;
use app\base\controller\Addons;

/**
 * 轮播发布页面表
 * Class Chart
 * @package app\index\Controller
 */
class Carousel extends Addons
{
    //轮播发布页面表model
    protected $carousel;

    //发布列表model
    protected $publish;

    public function __construct()
    {
        if (strtolower($_SERVER['REQUEST_METHOD']) == 'options') {
            exit();
        }
        parent::__construct();
        //初始化轮播发布页面表model
        $this->carousel = Loader::model("Carousel");
        //初始化发布列表model
        $this->publish = Loader::model("Publish");

    }

    public function index()
    {
        return  1;
    }

    /**
     * 获取一个ID
     * @return mixed
     */
    public function getCarouselId()
    {
        //将创建时间加入data
        $data = [
            'createtime' => time()
        ];
        //增加一条记录获取ID
        $id = $this->carousel->getInsertID($data);
        if($id) {
            return get_status(0,intval($id));
        }else {
            return get_status(1, "网络繁忙" , 9001);
        }
    }

    /**
     * 添加轮播列表
     * @return mixed
     */
    public function addCarouselScreen()
    {
        //获取input数据
        $input = input('post.');
        
        //判断shareIds是否是数组
        if(is_array($input['screens'])) {
            $input['screens'] = implode(',' , $input['screens']);
        }
        $cname = Db::name('carouselrelease')->where('cname',$input['cname'])->find();
        if($cname){
            return get_status(1,'文件名重复',4006);
        }
        //组合data
        $data = [
            'animation' =>$input['animation'],
            'remarks'   =>$input['remarks'],
            'controlPos' => $input['controlPos'],
            'cname' => $input['cname'],
            'screens' => $input['screens'],
            'crIdent' => $input['crIdent'],
            'crLink' => $input['crLink'],
            'time' => intval($input['time']),
            'createtime' =>time(),
            'updatetime' =>time()
        ];
        $insert = $this->carousel->getInsertID($data);

        if($insert) {
             return get_status(0,"添加成功");
        }else {
            return get_status(1, "网络繁忙" , 9001);
        }

    }

    //获取轮播图表
    public function getCarousel()
    {
        //获取input数据
        $input = input('get.');
        //搜索条件
        $where = isset($input['searchword']) ? $input['searchword'] : '';
        //当前页
        $currentPage = isset($input['currentPage']) ? $input['currentPage'] : '1';
        //每页显示数
        $pageSize = isset($input['pageSize']) ? $input['pageSize'] : '10';
        //排序条件
        $order = isset($input['order']) ? $input['order'] : 'cname';
        //查询列表
        $result['list'] = $this->carousel->getCarouselListAll($where,$currentPage,$pageSize,$order);
        //数据总数
        $result['total'] = count(Db::name('carouselrelease')->where('cname','like','%'.$where.'%')->select());
        //将图表地址改为数组
        foreach($result['list'] as $k=>&$v){
            $v['screens'] = explode(',',$v['screens']);
        }
        //直接返回数组
        return get_status(0,$result);
    }

    /**
     * 由ID装换成发布链接
     * @param $data
     */
    protected function CarouselListURL($data)
    {
        //设置发布列表查询ID集合
        $publishIDs = [];
        //将查询出来的轮播列表中的发布ID提取出来
        foreach ($data as $key => $value) {
            //判断cpid是否有值
            if(!empty($value['cpid'])) {
                //将cpid切割为数组
                $tmpArr= explode(',' , $value['cpid']);
                foreach ($tmpArr as $v) {
                    //提取cpid
                    $publishIDs[] = $v;
                }
                //初始化$tmpArr
                $tmpArr = [];
            }
        }
        //定义where条件
        $where['scid'] =['in' , array_unique($publishIDs)];
        //查询发布列表
        $selectList = $this->publish->getWhereList($where,"scid,link");
        $publishList = [];
        //将查询结果以pid为键保存
        foreach ($selectList as $value) {
            $publishList[$value['scid']] = $value['link'];
        }
        //将$data中的cpid换成具体的link
        foreach ($data as $key => $value) {
            //判断cpid是否有值
            if(!empty($value['cpid'])) {
                //将cpid切割为数组
                $tmpArr= explode(',' , $value['cpid']);
                //将源cpid初始化
                $data[$key]['cpid'] = [];
                foreach ($tmpArr as $v) {
                    if(isset($publishList[$v]) ){
                        //给cpid重新赋值
                        $data[$key]['cpid'][] = $publishList[$v];
                    }
                }
                //初始化$tmpArr
                $tmpArr = [];
            }
        }
        //返回数组
        return $data;
    }

    //删除轮播列表
    public function delCarousel()
    {
        //获取input数据
        $input = input('get.');
        //验证参数是否正确
        //判断shareIds是否是数组
        if(is_array($input['crid'])) {
            $input['crid'] = implode(',' , $input['crid']);
        }
        //设置where条件
        $where['crid'] = ["in" , $input['crid']];
        //执行删除
        $delete = $this->carousel->deleteCarousel($where);
        //判断是否删除成功
        if($delete) {
            return get_status(0 , "删除成功");
        }else {
            return get_status(1 , "网路繁忙" , 9001);
        }
    }
    //修改轮播图信息
    public function updateCarouselScreen()
    {
        $put = input('put.');
        $cname = Db::name('carouselrelease')->where('crid','<>',$put['crid'])->where('cname',$put['cname'])->find();
        if($cname){
            return get_status(1,'文件名重复',4006);
        }
        $put['screens'] = implode(',',$put['screens']);
        $put['updatetime'] = time();
        $crid = ['crid'=>$put['crid']];
        //删除无用字段 
        unset($put['crid']);
        unset($put['checked']);
        $update = $this->carousel->updateCarousel($put,$crid);
        if($update){
            return get_status(0,'修改成功');
        }else{
            return get_status(1,'修改失败',6004);
        }
    }
    //展示一个轮播详情
    public function getCarouselDetail()
    {
        $input = input('get.');
        // dump($input);die;
        $get = ['crIdent'=>$input['crIdent']];
        $data = $this->carousel->getCarouselList($get);
        
        $data['screens'] = explode(',',$data['screens']);
        return get_status(0,$data);
    }

}