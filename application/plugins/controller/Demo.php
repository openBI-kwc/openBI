<?php
namespace app\plugins\controller;

class Demo
{
    /**
     * 时序数据库插件使用测试
     *
     * @return void
     */
    public function influx()
    {
        include ROOT_PATH . 'plugins' . DS . 'influxDB/InfluxDb.php';
		$config['hostname'] = '192.168.30.128';
		$config['hostport']  = 8086;
		$config['dbname']  = 'real_time';
		$config['username'] = 'guest';
		$config['password'] = 'guest';
		$result = \InfluxDb::getInstance($config)->query('show measurements');
		dump($result);
    }

    public function demo()
    {
        $file = ROOT_PATH . 'plugins' . DS . 'demo2.zip';
        // $zip = new \ZipArchive;
        // $open = $zip->open($file);
        $zip = zip_open($file);
        $zip = zip_read($file);
        dump($zip);
        // dump($zip);die;
        // if ($zip->open($file) === TRUE) {
        //     for ($idx=0 ; $s = $zip->statIndex($idx) ; $idx++) {
        //         dump($zip->getExternalAttributesIndex($idx));
        //         // if ($zip->extractTo('.', $s['name'])) {
        //         //     if ($zip->getExternalAttributesIndex($idx, $opsys, $attr) 
        //         //         && $opsys==ZipArchive::OPSYS_UNIX) {
        //         //        chmod($s['name'], ($attr & 07777));
        //         //     }
        //         // }
        //     }
        //     // $zip->extractTo($this->pluginDir);
        //     $zip->close();
        //     return true;
        // } else {
        //     return false;
        // }
    }
}