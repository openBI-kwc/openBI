<?php

/** 
 * oracle数据源
 */

namespace datasource;
use think\Db;
use DataSource;

class Oracle extends DataSource
{
    protected $conn = null;
    protected $config = [];
    protected static $instance = null;
    protected $tableSql = "SELECL t.table_name FROM user_tables t";

    protected function __construct($config)
    {
        $this->config = $config;
        $hostname = $config['hostname'].'/'.$config['dbname'];
        $this->conn = oci_connect($config['username'], $config['password'], $hostname, 'utf8');
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
            $ora = oci_parse($this->conn, $sql);
            oci_execute($ora, OCI_DEFAULT);
            $data = [];
            while($r = oci_fetch_assoc($ora)) {
                foreach($r as &$v) {
                    if(is_resource($v)) {
                        $v = stream_get_contents($v);
                    }
                }
                $data[] = $r;
            }
            return $data;	
        } catch (\Exception $e) {
            return false;
        }
    }

     // // 测试连接
    public function testConnection()
    {
        if (!$this->conn) return false;
        return [$this->config['dbname']];
    }


    // 获取字段
    public function getFields($tableName){}

    // 获取表
    public function getTables()
    {
        $sql = $this->tableSql;
        $result =  $this->query($sql);
        if (!$result) return false;
        foreach ($result as &$value) {
            $value = array_values($value);
        }   
        return $result;
    }

    public function __destruct()
    {
        return oci_close($this->conn);
    }

    // // 获取表所有数据
    public function selectTables($tableName)
    {
        return $this->query("select * from " . $tableName);
    }

    // 性能语句分析
    public function getExplain($sql){}

}
