<?php 
namespace app\index\Controller;
use think\Db;
use think\Request;
use think\Session;
use PhpOffice\PhpSpreadsheet\IOFactory;


/*
  数据映射
*/
class Datamap
{
	//查询数据源类型
	public function datatype()
	{
		
		$result = Db::name('datatype')->select();
		if(empty($result)){
       	  return  get_status(1,'没有相应数据类型');
        }else{
       	  return  get_status(0,$result);
        }

	}
	//根据数据类型查出来的已有数据
	public function mapdata()
	{
		$put = file_get_contents('php://input');	
		//$put = '{"dtid":1}';   
	    $post = json_decode($put,1);
		$result = Db::name('datasource')->where('dtid',$post['dtid'])->field('daid,dataname')->select();
		if(empty($result)){
       	  return  get_status(1,'没有相应数据');
        }else{
       	  return  get_status(0,$result);
        }

	}
	//返回的数据
	public function returndata()
	{
        $put = file_get_contents('php://input');	
		//$put = '{"daid":85,"dtid":4}';   
		if(empty($put)){
            return  get_status(1,NULL);
		}
	    $post = json_decode($put,1);

	    if($post['dtid'] == 2){
          $select = Db::name('datasource')->where('daid',$post['daid'])->field('uploadpath')->select();
          $fileSrc = ROOT_PATH . 'public'.$select[0]['uploadpath'];
          //$fileSrc = ROOT_PATH . 'public/updirs/国电泰州#3至#4机组对比报表(1月).xlsx';
          //使用phpexcel
		  Vendor('Classes.PHPExcel');
	      $objPHPExcel = IOFactory::load($fileSrc);
	      //生成返回数据
	      $result = $objPHPExcel->getSheet(0)->toArray();
	      //dump($result);
	    }elseif($post['dtid'] == 3){
          $result = Db::name('datasource')->where('daid',$post['daid'])->field('uploadpath,autoupdate,request,cookie')->select();
	    }else{
	      $find = Db::name('datasource')->where('daid',$post['daid'])->field('source,returnsql')->find();
	      $select = Db::name('datasource')->where('daid',$find['source'])->field('daid,dataname,host,username,password,port,dbname,len')->select();
		  $this->host = $select[0]['host'];
		  $this->name = $select[0]['username'];
         //$this->pwd = $select[0]['password'];
		  $this->dbname = $select[0]['dbname'];
		  $port = $select[0]['port'];
		  $key = 'kwc.net';
		  $str = openssl_decrypt($select[0]['password'], 'aes-128-cbc',$key,2);
		  $pwd = substr($str, 0,$select[0]['len']);
	      $link = @mysqli_connect($this->host,$this->name,$pwd,$this->dbname,$port);
	      if(!$link){
		       	//不成功，返回报错信息
		        return mysqli_connect_error();
		   }else{
		    	$sqls = mysqli_query($link,$find['returnsql']);
		    	//dump($sqls);
		    	while($row = mysqli_fetch_array($sqls,MYSQLI_ASSOC)){
	                  $data[] = $row;
		            } 

		        
		    }
		    foreach ($data as $k => $v) {
	      	 $keys = array_keys($v);
	      	 $result[] = array_values($v);

	      }
	        array_unshift($result, $keys);
	      //dump($result);
	      // $data = json_decode($select[0]['returnjson'],1);
	      // if (empty($data)) {
	      // 	return  get_status(1,'没有相应的数据');
	      // }
	      // foreach ($data as $k => $v) {
	      // 	 $keys = array_keys($v);
	      // 	 $result[] = array_values($v);

	      // }

	      // array_unshift($result, $keys);
          //dump($result);

	      
	    }
	    if(empty($result)){
       	  return  get_status(1,'没有相应的数据');
        }else{
       	  return  get_status(0,$result);
        }
	    
	}
	//存入发布数据
	public function publish()
	{
		$put = file_get_contents('php://input');	

		if(empty($put)){
			return  get_status(1,NULL);
		}    
	    $post = json_decode($put,1);
	    $result = Db::name('publish')->insertGetId($post);
        
	    if(empty($result)){
       	  return  get_status(1,NULL);
        }else{
       	  return  get_status(0,$result);
        }
	}
	
	//发布内容修改
	public function pupdate()
	{
		$put = file_get_contents('php://input');	
		if(empty($put)){
			return  get_status(1,NULL);
		}    
	    $post = json_decode($put,1);

	    $result = Db::name('publish')->where('pid',$post['pid'])->update($post);
	    //dump($result);
	    if(empty($result)){
       	  return  get_status(1,NULL);
        }else{
       	  return  get_status(0,$result);
        }

	}
	//发布
	public function release()
	{
		$put = file_get_contents('php://input');	
		//$put = '{"is_pwd":0,"scid":12,"password":12345}'; 
		if(empty($put)){
			return  get_status(1,NULL);
		}  
	    $post = json_decode($put,1);
        //$insert = Db::name('publish')->insert($post);
		$select = Db::name('publish')->where('pid',$post['pid'])->select();
		//dump($select);
		if($select[0]['is_pwd'] == 1){
			// if ($select[0]['password'] == $post['password']) {
			//    $data = Db::name('screen')->where('id',$post['scid'])->field('data')->select();
			// }else{
			//     return  get_status(1,'数据库密码错误');
			// }
			return  get_status(2,NULL);
		}else{
			$data = Db::name('screen')->where('id',$select[0]['scid'])->field('data')->find();
		}
		if(empty($data)){
       	  return  get_status(1,NULL);
        }else{
       	  return  get_status(0,$data['data']);
        }

	}
	//发布密码
	public function pwd()
	{
		$put = file_get_contents('php://input');	
		//$put = '{"is_pwd":0,"scid":12,"password":12345}'; 
		if(empty($put)){
			return  get_status(1,NULL);
		}  
	    $post = json_decode($put,1);
	    $select = Db::name('publish')->where('pid',$post['pid'])->find();
	    if($select['password'] == $post['password']){
	    	$data = Db::name('screen')->where('id',$select['scid'])->field('data')->find();
	    }else{
	        return  get_status(2,'数据库密码错误');
	    }
	    if(empty($data)){
       	  return  get_status(1,NULL);
        }else{
       	  return  get_status(0,$data['data']);
        }
   
	}

}