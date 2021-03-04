<?php

/** 
 * Elasticsearch数据源
 */

namespace datasource;
use think\Db;
use DataSource;
use Elasticsearch\ClientBuilder;

class Elasticsearch extends DataSource
{
    protected $conn = null;
    protected $config = [];
    protected static $instance = null;

    protected function __construct($config)
    {
        $this->config = $config;
        $username  = $config['username']? $config['username'] .':' : "";
        $password  = $config['password']? $config['password'] .'@' : "";
        $this->params = array(
            'http://'.$username.$password.$config['hostname'].':'.$config['hostport']
        );
        $this->conn = ClientBuilder::create()->setHosts($this->params)->build();
    }

    public static function getInstance($config)
    {
        if (null === self::$instance) {
            self::$instance = new self($config);
        }
        return self::$instance;
    }

    // 发送sql语句
    public function query($sql)
    {
        try {
            $result = $this->conn->search($sql);
            $hits = $result['hits'] ?? [];
            $aggregations = $result['aggregations'] ?? [];
            if ($aggregations) {
                $buckets = array_column($aggregations, 'buckets');
                $aggregation = array_shift($buckets);
                return $aggregation;
            }
            return array_column($hits['hits'], '_source');
        } catch (\Exception $e) {
            return false;
        }
    }

     // // 测试连接
    public function testConnection()
    {
        //获取ES索引列表  (不需要判断异常)
        $result = file_get_contents($this->params[0].'/_cat/indices?v');
        //判断获取到的字符串是否有双空格
        $vali = strpos( $result, '  ');
        while ($vali) {
            //将双空格改为单空格
            $result = str_replace('  ' , ' ' , $result);
            $vali = strpos( $result, '  ');
        }
        //以空格分割成字符串
        $arr = explode(' ' , $result);
        //找规律获取es索引  第一个为11 以后为11+9+9+9
        $i =  11;
        //定义索引存储数组
        $indexList = [];
        //判断是否存在该键
        while(isset($arr[$i])) {
            //判断$arr[$i]是否已.开头
            if($arr[$i][0]  != '.') {
                $indexList[] = $arr[$i];
            }
            $i += 9;
        }
        //返回索引数组  不许判断是否为空
        return $indexList;
    }


    // 获取字段
    public function getFields($tableName){}

    // 获取表
    public function getTables()
    {
        //查询语句
        $params = [
            'index' => $this->config['database'],
            'body' => [
                'size' => 0,
                '_source'=>['_type'],
                'aggs' => [
                    'all_interests' =>[
                        'terms' => [
                            'field' => '_type'
                        ]
                    ]
                ]

            ]
        ];
        //执行查询
        $data = $this->conn->search($params);
        //给聚合赋值
        $buckets = $data['aggregations']['all_interests']['buckets'] ?? false;
        if (!$buckets) return false;
        return array_column($buckets, 'key');
    }

    // 获取表所有数据
    public function selectTables($tableName)
    {
        return [];
    }

    // 性能语句分析
    public function getExplain($sql){}

}
