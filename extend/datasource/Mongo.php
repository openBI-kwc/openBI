<?php

/** 
 * mongo数据源
 */

namespace datasource;
use MongoDB\Driver\Manager;
use DataSource;
use think\Exception;

class Mongo extends DataSource
{
    protected $conn = null;
    protected $config = [];
    protected static $instance = null;
    protected $testSqls = [
        'mysql' => 'show status',
        'pgsql' => 'show status',
        'sqlite' => 'show status',
        'sqlsrv' => 'show status',
    ];
    protected $tableSql = "SELECT Name as tablename FROM SysObjects WHERE XType='U' ORDER BY Name";

    protected function __construct($config)
    {
        $this->config = $config;
        $host = 'mongodb://' . ($config['username'] ? "{$config['username']}" : '') . ($config['password'] ? ":{$config['password']}@" : '') . $config['hostname'] . ($config['hostport'] ? ":{$config['hostport']}" : '') . '/' . ($config['database'] ? "{$config['database']}" : '');
        $host = 'mongodb://' . ($config['username'] ? "{$config['username']}" : '') . ($config['password'] ? ":{$config['password']}@" : '') . $config['hostname'] . ($config['hostport'] ? ":{$config['hostport']}" : '');
        // 创建数据库连接对象
        $this->conn = new \MongoDB\Driver\Manager($host);
    }

    public static function getInstance($config)
    {
        if (null === self::$instance) {
            self::$instance = new self($config);
        }
        return self::$instance;
    }

    // 发送sql语句
    public function query($sql )
    {
        return "请使用mongoQuery()";
    }

    public function mongoQuery($table ,$filter ,$options)
    {
//        try{
            //此处如果密码错误会报错
            $query = new \MongoDB\Driver\Query($filter , $options);
            // 执行查询 成功即连接成功  失败则失败
            $datas = $this->conn->executeQuery($table,$query)->toArray();
            //返回数据
            return $datas;
//        }catch (\Exception  $e) {
//            return '查询失败';
//        }

    }

     // // 测试连接
    public function testConnection()
    {
       return $this->conn;
    }


    // 获取字段
    public function getFields($tableName)
    {
        dump($tableName);

    }

    // 获取表
    public function getTables()
    {
        // 待定
        return [];
    }

    // 性能语句分析
    public function getExplain($sql){}

}
