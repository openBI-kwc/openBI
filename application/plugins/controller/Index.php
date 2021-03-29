<?php
namespace app\plugins\controller;
use app\plugins\model\Plugins;
use think\Db;

class Index
{
    // 插件存放目录
    protected static $pluginDir = ROOT_PATH . 'public/plugins' . DS;
    //相对路径
    protected static $pluginRelativePath = '/plugins/';
    // 数据转换类型
    protected static $xmlDataType = ['boolean', 'int', 'float'];
    // pluginJson
    protected static $pluginJson;
    // 只更新一个
    protected static $upSingleDir = '';
    
    /**
     * 插件列表
     *
     * @return void
     */
    public function lists(Plugins $plugin)
    {
        $page = input('get.page',0);
        $limit = input('get.limit',5);
        $type = input('get.type','chart');
        $name = input('get.name','');
        $sortField = input('get.sortField','create_time');
        $where['type'] = $type;
        $where['name'] = ['like',"%$name%"];
        $where = array_filter($where);
        $sum_count = $plugin->where($where)->count();
        $result = $plugin->where($where)->order("$sortField desc")->page($page)->limit($limit)->select();
        $return_data['sum_count'] = $sum_count;
        $return_data['result'] = $result;
        return get_status(0 , $return_data); 
    }

    /**
     * 批量删除插件
     *
     * @param Plugins $plugin
     * @return void
     */
    public function del(Plugins $plugin)
    {
        $ids = input('post.pluginIds/a');
        $info = $plugin->where('id', 'in', $ids)->select();
        if (!$info) return get_status(1 , '找不到插件信息'); 
        $result = $plugin->where('id', 'in', $ids)->delete();
        if (!$result) {
            return get_status(1 , '卸载失败'); 
        }
        foreach ($info as $value) {
            removeDir(self::$pluginDir . $value['dir']);
        }
        return get_status(0 , '已卸载'); 
    }

    public static function upSinglePlugin($path)
    {
        $dir = basename($path);
        self::$upSingleDir = $dir;
        return self::update();
    }
    
    /**
     * 扫描插件目录
     *
     * @return void
     */
    public static function update()
    {   
        $dirs = self::scan();
        $plugins = [];
        foreach ($dirs as $dir) {
            if ($dir == '.' || $dir == '..' || $dir == '.gitignore') continue;
            $pluginPath = self::$pluginDir . $dir;
            if (is_file($pluginPath)) continue;
            if (self::$upSingleDir && $dir != self::$upSingleDir) continue;
            $data['pluginPath'] = self::$pluginRelativePath . $dir;
            $data['xmlPath'] = $pluginPath . DS . 'plugin.xml';
            $data['info'] = self::getXml($data['xmlPath']);
            $data['name'] = $data['info']['moduleId'];
            $data['dir'] = $dir;
            $data['version'] = $data['info']['moduleVersion'];
            $data['type'] = $data['info']['moduleType'] ?? 'chart';
            
            $result = self::savePlugin($data);
            //静态数据
            $TestData = $pluginPath . DS . $data['info']['moduleTestData'];
            $TestData = file_get_contents($TestData);
            self::saveChartdata($TestData,$data['info']['moduleId']);

            $plugins[$dir]['result'] = true;
            $plugins[$dir]['msg'] = 'ok';
            if (!$result) {
                $plugins[$dir]['result'] = false;
                $plugins[$dir]['msg'] = '写入失败';
            }
            if ($result === -1) {
                $plugins[$dir]['result'] = false;
                $plugins[$dir]['msg'] = '插件版本已存在';
            }
            $plugins[$dir]['name'] = $data['name'];
        }
        return get_status(0 , $plugins); 
    }

    /**
     * 保存入库
     *
     * @param [type] $plugin
     * @return void
     */
    public static function savePlugin($plugin)
    {
        $time = time();
        $plugin['info'] = json_encode($plugin['info'], JSON_UNESCAPED_UNICODE);
        $plugin['update_time'] = date('Y-m-d H:i:s', $time);
        $data = new Plugins();
        $findPlugin = $data->where(['name' => $plugin['name']])->find();
        if ($findPlugin) {
            // if ($findPlugin['version'] == $plugin['version']) return -1;
            return $data->allowField(true)->save($plugin, ['id' => $findPlugin['id']]);
        }
        $plugin['create_time'] = $plugin['update_time'];
        return $data->allowField(true)->save($plugin);
    }
    public static function saveChartdata($data,$charttype)
    {
        $result = Db::table('up_chartdata')->where('charttype',$charttype)->find();
        if(empty($result)){
            //添加操作
            $data = ['data' => $data, 'charttype' => $charttype];
            Db::table('up_chartdata')->insert($data);
        }else{
            //修改操作
            Db::table('up_chartdata')->where('charttype', $charttype)->update(['data' =>$data]);

        }
    }

    public static function scan()
    {
        //扫描文件夹
        return scandir(self::$pluginDir);
    }

    /**
     * 由路径解析xml
     *
     * @param [type] $xmlPath
     * @return void
     */
    public static function getXml($xmlPath)
    {
        $json = json_encode((array) simplexml_load_file($xmlPath), JSON_UNESCAPED_UNICODE);
        $info = str_replace('{}', '""', $json);
        $info = str_replace(array('\n\t'), "", $info);
        // dump($info);die;
        $result = json_decode($info, true);
        return $result;
        // dump($result);die;
        // $modifyData =  self::modifyDataType($result);
        // dump($modifyData);die;
    }

    // protected static function modifyDataType( $data)
    // {
    //     foreach ($data as $key => $value) {
    //         if (is_array($value)) {
    //             $data[$key] = self::modifyDataType($value);
    //         } else {
    //             if (in_array($key, self::$xmlDataType)) {
    //                 $data[$key] = self::getType($key, $value);
    //             }
    //         }
    //     }
    //     return $data;
    // }

    // protected static function getType($type, $value)
    // {
    //     switch ($type) {
    //         case 'boolean':
    //             return ($value == 'true') ? true : false;
    //         case 'int':
    //             return (int) $value;
    //         case 'float':
    //             return (float) $value;
    //         default:
    //             return '';
    //     }
    // }
}