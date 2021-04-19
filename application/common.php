<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件


function get_status($err, $da, $status = null)
{
    $data['err'] = $err;
    $data['data'] = $da;
    if ($data['err']) {
        $data['status'] = $status;
    }
    return $data;
}


function get_Log($username, $operation, $state)
{
    Db::name('log')->insert(['username'=>$username,'operation'=>$operation,'state'=>$state]);
}


function get_types($filename)
{
    $type = substr($filename, strrpos($filename, ".")+1);
    return $type;
}

function ajaxReturn($data)
{
    exit(json_encode($data)) ;
}


//获取header头中的值（token）
function get_all_header()
{
    // 忽略获取的header数据。这个函数后面会用到。主要是起过滤作用
    $ignore = array('host','accept','content-length','content-type');

    $headers = array();
    //这里大家有兴趣的话，可以打印一下。会出来很多的header头信息。咱们想要的部分，都是‘http_'开头的。所以下面会进行过滤输出。
    /*    var_dump($_SERVER);
     exit;*/

    foreach ($_SERVER as $key=>$value) {
        if (substr($key, 0, 5)==='HTTP_') {
            //这里取到的都是'http_'开头的数据。
            //前去开头的前5位
            $key = substr($key, 5);
            //把$key中的'_'下划线都替换为空字符串
            $key = str_replace('_', ' ', $key);
            //再把$key中的空字符串替换成‘-’
            $key = str_replace(' ', '-', $key);
            //把$key中的所有字符转换为小写
            $key = strtolower($key);

            //这里主要是过滤上面写的$ignore数组中的数据
            if (!in_array($key, $ignore)) {
                if ($key == "access-token") {
                    $headers['token'] = $value;
                } else {
                    $headers[$key] = $value;
                }
            }
        }
    }
    //输出获取到的header
    return $headers;
}


/**
* 判断传过来的键是否存在
*
*  */
function valiKeys($keys, $input)
{
    //遍历键
    foreach ($keys as $k => $v) {
        if (!isset($input[$v])) {
            return get_status(1, $v.'未设置');
        }
    }
    return get_status(0, []);
}


/**
* 默认返回为html是返回json格式
*/
function jsonRetuen($err, $data, $status = null)
{
    if ($status) {
        return json(['err' => $err , 'data' => $data,'status'=>$status]);
    } else {
        return json(['err' => $err , 'data' => $data]);
    }
}


  /**
   * @Notes : 解密
   * @access : $password : 解密内容
   * @author : wwk
   * @Time: 2018/08/13 11:23
   */
function decrypt($password, $len)
{
    $key = 'kwc.net';
    $str = openssl_decrypt($password, 'aes-128-cbc', $key, 2);
    $pwd = substr($str, 0, $len);
    return $pwd;
}

//获取配置文件信息
function configJson()
{
    //获取配置文件信息
    $file = file_get_contents(config('static_config_path'));
    //将配置文件转换数组
    $arr = json_decode($file, 1);
    //返回配置文件信息及路径
    return ['config' => $arr , 'path' => config('static_config_path')];
}


//处理base64图片
function base64Image($data)
{
    if (!is_string($data) || $data == '') {
        return false;
    }
    $base64 = $data;
    $arr = explode(',', $base64);
    //取出后缀
    $ext = base64Ext($arr[0]);
    //取出base64图片字符串
    $base64Str = $arr[1];
    $file = base64_decode($base64Str);
    //定义图片名称
    $fileName = md5(mt_rand(100, 999).time()).'.'.$ext;
    //定义图片路径
    $filePath = ROOT_PATH . 'public' . DS . 'uploads' . DS . date('Ymd', time());
    if (!file_exists($filePath)) {
        mkdir($filePath, 0755);
    }
    //保存图片
    $r = file_put_contents($filePath.DS.$fileName, $file);

    if ($r) {
        //获取图片相对路径
        $imgPath = str_replace(ROOT_PATH, '', $filePath.DS.$fileName);
        return $imgPath;
    } else {
        return false;
    }
}


// 传入base64前部分带有照片后缀的字符串
function base64Ext($str)
{
    $step1 = explode('/', $str);
    $step2 = explode(';', $step1[1]);
    return $step2[0];
}


//模型统一判断返回格式
function agreeReturn($result)
{
    if ($result) {
        return $result;
    }
    return false;
}

//判断键是否存在,不存在给默认值
function issetKey($input, $key, $value)
{
    if (!isset($input[$key])) {
        $input[$key] = $value;
    } else {
        $input[$key] = rtrim(strtolower($input[$key]));
    }
    return $input[$key];
}

//判断xml格式
//自定义xml验证函数xml_parser()
function xml_parser($str)
{
    $xml_parser = xml_parser_create();
    if (!xml_parse($xml_parser, $str, true)) {
        xml_parser_free($xml_parser);
        return false;
    } else {
        return (json_decode(json_encode(simplexml_load_string($str)), true));
    }
}


function curl_request($url, $opt = [])
{
    $sec = $opt['sec'] ?? 3;
    $method = $opt['method'] ?? 'GET';
    $headers = $opt['headers'] ?? [];
    $params = $opt['params'] ?? [];
    if (is_array($params)) {
        $requestString = http_build_query($params);
    } else {
        $requestString = $params ? : '';
    }
    if (empty($headers)) {
        $headers = array('Content-type: text/json');
    } elseif (!is_array($headers)) {
        parse_str($headers, $headers);
    }
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_VERBOSE, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, $sec);
    curl_setopt($ch, CURLOPT_POST, 1);
    switch (strtoupper($method)) {
          case "GET": curl_setopt($ch, CURLOPT_HTTPGET, 1);break;
          case "POST": curl_setopt($ch, CURLOPT_POST, 1);
                      curl_setopt($ch, CURLOPT_POSTFIELDS, $requestString);break;
          case "PUT": curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
                      curl_setopt($ch, CURLOPT_POSTFIELDS, $requestString);break;
          case "DELETE":  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
                          curl_setopt($ch, CURLOPT_POSTFIELDS, $requestString);break;
      }
    $response = curl_exec($ch);
    curl_close($ch);
    if (stristr($response, 'HTTP 404') || $response == '') {
        return array('Error' => '请求错误');
    }
    return $response;
}
