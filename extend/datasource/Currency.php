<?php

/** 
 * 通用数据源
 * 自带orm支持友好
 * mysql, pgsql, sqlite, sqlsrv
 */

namespace datasource;
use think\Db;
use DataSource;

class Currency extends DataSource
{
    protected $conn = null;
    protected $config = [];
    protected static $instance = null;
    protected $testSqls = [
        'mysql' => 'show databases',
        'pgsql' => "SELECT datname FROM pg_database",
        'sqlite' => 'show status',
        'sqlsrv' => "SELECT NAME FROM MASTER.DBO.SYSDATABASES ORDER BY NAME",
    ];
    protected $tableSqls = [
        'mysql' => "show tables",
        'pgsql' => "SELECT tablename FROM pg_tables WHERE schemaname='public'",
        'sqlite' => "SELECT name as tablename  from sqlite_master where type='table' order by name",
        'sqlsrv' => "SELECT Name as tablename FROM SysObjects WHERE XType='U' ORDER BY Name",
    ];

    protected function __construct($config)
    {
        if ($config['type'] == 'pgsql') $config['database'] = 'postgres';
        $this->config = $config;
        $this->conn = Db::connect($config);
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
            return $this->conn->query($sql);
        } catch (\Exception $e) {
            return false;
        }
    }

     // // 测试连接
    public function testConnection()
    {
        $type = $this->config['type'] ?? 'mysql';
        $sql = $this->testSqls[$type];
        $result = $this->query($sql);
        if (!$result) return false;
        $databases = [];
        foreach ($result as &$value) {
            $value = array_values($value);
            $databases[] = $value[0];
        }
        return $databases;
    }


    // 获取字段
    public function getFields($tableName){}

    // 获取表
    public function getTables()
    {
        $type = $this->config['type'] ?? 'mysql';
        $sql = $this->tableSqls[$type];
        // return $this->query($sql);
        $result =  $this->query($sql);
        if (!$result) return false;
        // dump($result);die;
        foreach ($result as &$value) {
            $value = array_values($value)[0];
        }
        return $result;
    }

    // // 获取表所有数据
    public function selectTables($tableName)
    {
        return $this->query("select * from " . $tableName);
    }

    // 性能语句分析
    public function getExplain($sql){}

}
