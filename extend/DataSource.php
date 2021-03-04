<?php

abstract class DataSource
{
    // 数据源类别
    protected static $types = [
        'mysql' => 'Currency',
        'sqlsrv' => 'Currency',
        'pgsql' => 'Currency',
        'oracle' => 'Oracle',
        'mongo' => 'Mongo',
        'es' => 'Elasticsearch'
    ];

    protected static $link = [];


    public static function connect($config = [])
    {
        $className = self::$types[$config['type']];
        $class = '\\datasource\\'.$className;
        return $class::getInstance($config);
    }


    // // 测试连接
    abstract function testConnection();

    // 数据实例
    abstract public function query($sql);

    // 获取字段
    abstract public function getFields($tableName);

    // 获取表
    abstract public function getTables();

    // // 获取表所有数据
    abstract public function selectTables($tableName);

    // 性能语句分析
    abstract protected function getExplain($sql);

}
     