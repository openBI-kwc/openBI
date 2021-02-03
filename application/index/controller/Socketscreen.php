<?php
namespace app\index\controller;

/**
 * swoole websocket  可以回调  未测试版本
 */
use think\Cache;
use Swoole\Server;
use think\Log;

class Socketscreen
{
    protected $ws;

    //初始化socket
    public function __construct(){
        if(!$this->ws) {
            //实例化swoole对象
            $this->ws = new \swoole_websocket_server("0.0.0.0", 9577);
            //初始化缓存
            Cache::rm('screen');
        }
    }
    
    //socket主程序
    public function socket()
    {
        //open时处理
        $this->socketOpen();
      
        //send时处理
        $this->socketSend();
       
        //关闭连接时处理
        $this->socketClose();
       
        //回调处理
        $this->socketRequest();

       //开始服务
        $this->ws->start();

    }

    //send时处理
    protected function socketOpen()
    {
        $this->ws->on('open' , function ( \swoole_websocket_server $ws, $request){
            var_dump("客户端-{$request->fd}连接");
        });
    }

    //send时处理
    protected function socketSend()
    {
        $this->ws->on('message', function( \swoole_websocket_server $ws , $request ) {
            //接收数据
            $json = $request->data;
            if($json == "heartbeat") {
                $ws->push($request->fd, "I am still alive");
            }else {
                $json = json_decode($json,1);
                //存入缓存
                $this->saveCache($request->fd, $json['sid']);
            }
        });
    }
    
    //关闭是处理
    protected function socketClose()
    {
        $this->ws->on('close',function(\swoole_websocket_server $ws,$request){
            //删除缓存
            $this->deleteCache($request);
            var_dump("客户端-{$request}断开连接");
        });
    }

    //回调处理
    protected function socketRequest()
    {
        $this->ws->on('Request', function ($req, $respone) {
            //接收数据
            $data = $req->rawContent();
            //写入日志
            Log::write($data,'notice');
            //反序列化
            $data = json_decode($data,1);
            dump($data);
            // //获取当前在线用户
            $fileArr = json_decode(Cache::get('screen'),1);
            //写入日志
            Log::write(json_encode($fileArr),'notice');
            dump($fileArr);
            // //判断$fileArr是否为空
            if(!empty($fileArr)) {
                foreach ($fileArr as  $value) {
                    $this->ws->push($value['fd'], $data[$value['sid']]);
                    // $this->ws->push(1, "大屏");
                }
            }
            $respone->end("success");

        });
    }
 
    //存入缓存
    protected function saveCache($fd ,  $sid)
    {
        //查看缓存
        $cache = Cache::get('screen');
        if($cache != '') {
            //将json转换为数组
            $cacheArr = json_decode($cache,1);
            $data = ['fd' => $fd , 'sid' => $sid];
            //将本次连接记录
            $cacheArr[$fd] = $data;
            //数组转换为json方便存储
            $json = json_encode($cacheArr);
        }else {
            $cacheArr[$fd] = ['fd' => $fd , 'sid' => $sid];
            $json = json_encode($cacheArr);
        }
        //将json存储缓存
        Cache::set('screen', $json);
    }

    //删除缓存
    protected function deleteCache($fd)
    {
        //查看缓存
        $cache = Cache::get('screen');
        if($cache) {
            //将json转换为数组
            $cacheArr = json_decode($cache,1);
            //删除对应数据
            unset($cacheArr[$fd]);
            //判断是否还有连接
            if(empty($cacheArr)) {
                //没有连接初始化缓存
                Cache::rm('screen');
            }else {
                //数组转换为json方便存储
                $json = json_encode($cacheArr);
                //将剩余json化存储缓存
                Cache::set('screen', $json);
            }
        }else {
            return;
        }
    }

    
}