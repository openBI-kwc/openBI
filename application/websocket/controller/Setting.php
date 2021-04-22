<?php
namespace app\websocket\controller;

use think\Db;

class Setting
{
    public function index()
    {
        //查询数据
        $path = config('static_config_path');
        //获取配置文件的内容
        $file = file_get_contents($path);
        $arr = json_decode($file, 1);
        $result = Db::name('systemset')->where('type', 'socket')->find();
        $config = json_decode($result['config'], true);
        $data['url'] = $arr['setting']['realData']['url'];
        $data['status'] = $config['status'] ?? 0;
        $data['php_path'] = $config['php_path'] ?? '';
        if (empty($file)) {
            return get_status(1, null);
        } else {
            return get_status(0, $data);
        }
    }

    public function set()
    {
        //获取数据
        $put = file_get_contents('php://input');
        if (empty($put)) {
            return get_status(1, null);
        }
        $post = json_decode($put, 1);
        $path = config('static_config_path');
        $file = file_get_contents($path);
        //转成数组
        $arr = json_decode($file, 1);
        $arr['setting']['realData']['url'] = $post['url'];
        $jsondata = json_encode($arr, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
        $data = file_put_contents($path, $jsondata, FILE_USE_INCLUDE_PATH);
        $result = Db::name('systemset')->where('type', 'socket')->find();
        $config = json_decode($result['config'], true);
        $inputConfig = ['status' => $post['status'], 'php_path' => $post['php_path']];
        if (!$result) {
            Db::name('systemset')->where('type', 'socket')->insert(['type' => 'socket', 'config' => json_encode($inputConfig)]);
        } else {
            Db::name('systemset')->where('type', 'socket')->update(['config' => json_encode($inputConfig)]);
        }
        $status = $config['status'] ?? 0;
        $msg = '修改成功';
        if ($status != $post['status']) {
            $returnData = $this->runSocket($post['status'], $post['php_path']);
            return get_status($returnData[0], $returnData[1]);
        }
        if (empty($data)) {
            return get_status(1, $msg);
        } else {
            return get_status(0, $msg);
        }
    }

    public function runSocket($status, $phpPath)
    {
        if (!$status) {
            return $this->stopSocket($phpPath);
        }
        if (!$phpPath) {
            $phpPath = 'php';
        }
        try {
            $commandStr = 'cd ' . ROOT_PATH . ' & nohup ' . $phpPath . ' ' . ROOT_PATH . 'Socket_server.php >' . ROOT_PATH . 'socket.log 2>&1& echo $! >' . ROOT_PATH . 'socket.pid';
            exec($commandStr);
            return [0, '开启socket成功'];
        } catch (\Exception $e) {
            return [1, '启动失败，请检查php路径并放开exce函数（此功能不支持winserver）'];
        }
    }

    public function stopSocket()
    {
        if (!file_exists(ROOT_PATH . 'socket.pid')) {
            return [0, '修改成功'];
        }
        try {
            $commandStr = 'kill `cat ' . ROOT_PATH . 'socket.pid`';
            exec($commandStr);
            @unlink(ROOT_PATH . 'socket.pid');
            return [0, '关闭socket成功'];
        } catch (\Exception $e) {
            return [1, '关闭socket失败'];
        }
    }
}
