<?php
namespace app\index\Controller;

use \tp5er\Backup;

/**
  系统设置
* 
*/
class Backdown
{
  
	public function backupdown()
  {
    $post = input('get.');
    //$put = '{"dataname":"20180802-113738-1.sql"}';
    //$post = json_decode($put,1);
    if (empty($post)) {
      return get_status(1,"参数不完整" , 1050);
    }
    $config=array(
      'path' =>ROOT_PATH.'data/',//数据库备份路径
    );
    $db= new Backup($config);//实例化数据库备份类进行条用里面的方法。

    $str = trim($post['dataname'],'-1.sql');
    $str = explode('-',$str);
    $str = join($str,'');  
    //dump($str);
    $result = $db->downloadFile(strtotime($str));
    if (!empty($result)) {
      return get_status(1,$result);
    }
  }
}