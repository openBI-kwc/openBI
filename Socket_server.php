<?php
define("WEB_PATH" , "http://127.0.0.1:9507");
//实例化swoole对象
$ws = new swoole_websocket_server("0.0.0.0", 9507);


$ws->on('open' , function ($ws , $request){
    var_dump("客户端-{$request->fd}连接");
});


$ws->on('message', function( $ws , $request ) {
	if($request->data == "Are you still alive") {
             $ws->push($request->fd, "I am still alive");
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
