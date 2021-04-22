<?php

Env::loadFile(__DIR__ . '/.env');
$staticPath =  __DIR__ . '/' . Env::get('config.static_config_path');
$staticConfig = file_get_contents($staticPath);
$configArr = json_decode($staticConfig, true);
$port = explode(':', $configArr['setting']['realData']['url'])[2] ?? 9507;
define("WEB_PATH" , $configArr['setting']['server'] ?? 'http://127.0.0.1');
//实例化swoole对象
$ws = new swoole_websocket_server("0.0.0.0", $port);


$ws->on('open' , function ($ws , $request){
    var_dump("客户端-{$request->fd}连接");
});


$ws->on('message', function( $ws , $request ) {
	if($request->data == "ping") {
             $ws->push($request->fd, "pong");
        }else {
			go(function () use ($ws , $request){
				$GLOBALS[$request->fd] = swoole_timer_tick(1000, function ($timer_id) use ($ws , $request){
					try{
						//var_dump($request->data);
						$result = isJson($request->data , true);
						//var_dump($result);
						$id  =  $result["sid"];
						$curlData = file_get_contents(WEB_PATH."/index/Websocket/index?id=".$id);
						$user_message = $curlData;
						if(!empty(json_decode($user_message, 1)["data"])){
							$ws->push($request->fd, $user_message);	
						}
					} catch (\Exception $e) {
	
					}
				//var_dump($user_message);
				});	   
			});
		}
});

function isJson($data = '', $assoc = false) {
    $data = json_decode($data, $assoc);
    if ($data && (is_object($data)) || (is_array($data) && !empty(current($data)))) {
        return $data;
    }
    return false;
}



$ws->on('close',function($ws,$request){
	if(isset($GLOBALS[$request])) {
		$result = swoole_timer_clear($GLOBALS[$request]);
		 var_dump($result);
	}
	var_dump("客户端-{$request}断开连接");
});

$ws->start();

class Env
{
    const ENV_PREFIX = 'PHP_';

          /**
     * 加载配置文件
     * @access public
     * @param string $filePath 配置文件路径
     * @return void
     */
    public static function loadFile(string $filePath):void
    {
        if (!file_exists($filePath)) throw new \Exception('配置文件' . $filePath . '不存在');
        //返回二位数组
        $env = parse_ini_file($filePath, true);
        foreach ($env as $key => $val) {
            $prefix = static::ENV_PREFIX . strtoupper($key);
            if (is_array($val)) {
                foreach ($val as $k => $v) {
                    $item = $prefix . '_' . strtoupper($k);
                    putenv("$item=$v");
                }
            } else {
                putenv("$prefix=$val");
            }
        }
    }

    /**
     * 获取环境变量值
     * @access public
     * @param string $name 环境变量名（支持二级 . 号分割）
     * @param string $default 默认值
     * @return mixed
     */
    public static function get(string $name, $default = null)
    {
        $result = getenv(static::ENV_PREFIX . strtoupper(str_replace('.', '_', $name)));

        if (false !== $result) {
            if ('false' === $result) {
                $result = false;
            } elseif ('true' === $result) {
                $result = true;
            }
            return $result;
        }
        return $default;
    }
}