<?php 
namespace app\index\Controller;
//header("content-type:text/html;charset=utf-8");


use think\Db;
use think\Request;
use think\Session;
use app\base\controller\Base;
use PhpOffice\PhpSpreadsheet\IOFactory;

/**
* 数据管理
*/

class Datasource extends Base
{
	public $host;
	public $name;
	public $pwd;
	public $dbname;

	public function aaa(){
		//echo '222';
	    return $this->fetch('/setsystem/index');
	}

	//数据管理
	public function dataList()
	{
		$post = input('get.');
        
		$type = $post['type'];
		$group = $post['group'];
		//查询uid用户权限

		$uid = Db::name('token')->where('token',$_SERVER['HTTP_TOKEN'])->field('uid')->find();
		$category = Db::name('user')->where('uid',$uid['uid'])->field('sid')->find();
		$cate = explode(',',$category['sid']);
		//查询数据类型
		$datatype = Db::name('datatype')->select();
		//查询分类
		//Db::name('screengroup')->where('sid','in',)->field('sid,screenname')->select();
		$groupdata = Db::name('screengroup')->where('sid','in',$cate)->field('sid,screenname')->select();
		//dump($groupdata);
		if(empty($datatype)){
			return get_status(0,[]);
		}
		if(empty($groupdata)){
		    return get_status(0,[]);
		}
		//连表条件
		$join = [
			['screengroup g','d.cid=g.sid'],
			['datatype t','d.dtid=t.did' ],
		];
		//如果参数都为0，全部显示
		if (empty($post['pages'])) {
			if ( $type == 0 && $group == 0) {
			   $data = Db::name('datasource')->alias('d')->join($join)->select();
	           $total = count($data);
	        }elseif($type == 0){
	           $data = Db::name('datasource')->alias('d')->join($join)->where('g.sid',$group)->select();
	           $total = count($data);
	        }elseif($group == 0){
	           $data = Db::name('datasource')->alias('d')->join($join)->where('t.did',$type)->select();
	           $total = count($data);
	        }else{
	           $data = Db::name('datasource')->alias('d')->join($join)->where('t.did',$type)->where('g.sid',$group)->select();
	           $total = count($data);
	        }
			
		}else{
			if ( $type == 0 && $group == 0) {
			   $result = Db::name('datasource')->alias('d')->join($join)->select();
	           $data = Db::name('datasource')->alias('d')->join($join)->page($post['pages'],$post['num'])->select();
	           $total = count($result);
	        }elseif($type == 0){
	           $result = Db::name('datasource')->alias('d')->join($join)->where('g.sid',$group)->select();
	           $data = Db::name('datasource')->alias('d')->join($join)->where('g.sid',$group)->page($post['pages'],$post['num'])->select(); 
	           $total = count($result);
	        }elseif($group == 0){
	           $result = Db::name('datasource')->alias('d')->join($join)->where('t.did',$type)->select();
	           $data = Db::name('datasource')->alias('d')->join($join)->where('t.did',$type)->page($post['pages'],$post['num'])->select();
	           $total = count($result);
	        }else{
	           $result = Db::name('datasource')->alias('d')->join($join)->where('t.did',$type)->where('g.sid',$group)->select();
	           $data = Db::name('datasource')->alias('d')->join($join)->where('t.did',$type)->page($post['pages'],$post['num'])->where('g.sid',$group)->select();
	           $total = count($result);
	        }

		}
		
		//dump($data);
		if (empty($data)) {
         	return ['err'=>0,'datatype'=>$datatype,'groupdata'=>$groupdata,'total'=>$total];
	    }else{
	      	return ['err'=>0,'data'=>$data,'datatype'=>$datatype,'groupdata'=>$groupdata,'total'=>$total];
	    }		
	}
	//调整分类
	public function grouping()
	{
	    $put = file_get_contents('php://input');
        //$put = '{"daid":1,"cid":1}';
		$post = json_decode($put,1);
        //查询分类
		$group = Db::name('screengroup')->select();
		//修改分类
		$result = Db::name('datasource')->where('daid',$post['daid'])->update(['cid'=>$post['cid']]);
		if(empty($group)){
           return get_status(1,'没有相应的分类');
		}
		if (empty($result)) {
		   return get_status(1,'没有相应的数据');
		}else{
		   return get_status(0,'修改成功');
		}
	}
	//删除数据
	public function deldata()
	{
       
		$put = file_get_contents('php://input');
       if(empty($put)){
         return get_status(1,NULL);
       }
   	   //$put = '{"daid":2}';
       $post = json_decode($put,1);
       //删除数据
       $result = Db::name('datasource')->where('daid',$post['daid'])->delete();
       if (empty($result)) {
         	return get_status(1,NULL);
	    }else{
	      	return get_status(0,NULL);
	    }
   	}
   	//修改
   	public function updata()
   	{
   	   $put = file_get_contents('php://input');
       $post = json_decode($put,1);
       //修改数据
       $result = Db::name('datasource')->where('daid',$post['daid'])->update($post['data']);
       if (empty($result)) {
         	return get_status(1,'修改失败');
	    }else{
	      	return get_status(0,'修改成功');
	    }
   	}
   	//文件上传
   	public function uploadFile()
   	{
		//获取参数num
		$input = input('post.');

        if(isset($input['num'])) {
            //用于存储路径
            $newpaths = [];
            for($i = 0 ; $i < intval($input['num']) ; $i ++ ) {
                //设置上传键
                $fileKey = 'file'.$i;
                //执行上传操作
                $file = request()->file($fileKey);

                if(empty($file)){
                    return get_status(1,NULL);
                }
                //把图片移动到/public/uploads/img/文件下
                $info = $file->validate(['size'=>5242880,'ext'=>'csv,xlsx,xls'])->move(ROOT_PATH . 'public' . DS . 'updirs');
                if($info){
                    //获取图片的路径
                    $newpath =  '/updirs/' .$info->getSaveName();
                    //定义文件上传路径
                    $path = ROOT_PATH . 'public' . DS . $newpath;
                    chmod($path , 0777);
                    $newpaths[] = str_replace('\\','/',$newpath);
                }else{
                    // 上传失败获取错误信息
                    $error = $file->getError();
                }
            }
        }else {
            //执行上传操作
            $file = request()->file('file');

            if(empty($file)){
                return get_status(1,NULL);
            }
            //把图片移动到/public/uploads/img/文件下
            $info = $file->validate(['size'=>5242880,'ext'=>'csv,xlsx,xls'])->move(ROOT_PATH . 'public' . DS . 'updirs');
            if($info){
                //获取图片的路径
                $newpath =  '/updirs/' .$info->getSaveName();
                //定义文件上传路径
                $path = ROOT_PATH . 'public' . DS . $newpath;
                chmod($path , 0777);
                $newpaths[] = str_replace('\\','/',$newpath);
            }else{
                // 上传失败获取错误信息
                $error = $file->getError();
            }
        }

		//返回的数据
		if (empty($info)) {
			   return get_status(1,$error);
		}else{
			   return get_status(0,$newpaths);
		}
	}
   	//添加excel
   	public function addexcel()
   	{
      
       $put = file_get_contents('php://input');
       //$put = '{"dataname":"xiaoni","cid":"1","uploadpath":"/upload/"}';
       //判断前端传的数据是否为空
       if(empty($put)){
          return get_status(1,'传入数据为空');
       }
       //转换成数组
       $post = json_decode($put,1);
       //执行插入操作
       $result = Db::name('datasource')->insert($post);
       //返回值
       if (empty($result)) {
           return get_status(1,'添加失败');
       }else{
           return get_status(0,'添加成功');
       }
   	}	
	//添加API
	public function addapi()
	{
       $put = file_get_contents('php://input');
       //$put = '{"dataname":"xiaoni","cid":1,"dtid":1,"uploadpath":"www.baidu.com","structure":"sss","updatetime":5,"request":0,"cookie":0}';
       //转换成数组
       if(empty($put)){
         return get_status(1,'请添加相应的数据');
       }
       $post = json_decode($put,1);
       //执行插入操作
       $result = Db::name('datasource')->insert($post);
       //返回值
       if (empty($result)) {
           return get_status(1,'添加失败');
       }else{
           return get_status(0,'添加成功');
       }
	}
	//测试数据库链接
	public function testlink()
	{
	   $put = file_get_contents('php://input');
       //$put = '{"dataname":"xiaoni","cid":1,"dtid":1,"host":"localhost","username":"root","password":"R9Smg/VifC67GXJf0mczWg==","port":3306,"len":6}';
       //转换成数组
       $post = json_decode($put,1);
       $key = 'kwc.net';
       $str = openssl_decrypt($post['password'], 'aes-128-cbc',$key,2);
       $pwd = substr($str, 0,$post['len']);
       
       //数据库连接
       $link = @mysqli_connect($post['host'],$post['username'],$pwd,'',$post['port']);
       //dump($link);
       //判断是否连接成功
       if(!$link){
       	   //不成功，返回报错信息
       	   return  get_status(1,mysqli_connect_error());
       }else{
       	    //成功，执行查询所有库操作
	       	$sql = 'show databases';
	        $result = mysqli_query($link,$sql);
			while($row = mysqli_fetch_array($result,MYSQLI_ASSOC)){
	                $arr[] = $row['Database'];
		    }       
	       	return  get_status(0,$arr);
       }
   	}
	//添加数据库连接
	public function dbconnect()
	{
	   $put = file_get_contents('php://input');
       //$put = '{"dataname":"xiaoni","cid":1,"dtid":1,"host":"localhost","username":"root","password":"root","port":3306,"dbname":"sas", "len":6}';
       //转换成数组
       if(empty($put)){
          return  get_status(1,'请填写相应数据');
       }
       $post = json_decode($put,1);  
       
       //执行插入操作
       $result = Db::name('datasource')->insert($post);
       //dump($post);
       // //返回值
       if(empty($result)){
       	 return  get_status(1,mysqli_connect_error());
       }else{
       	 return  get_status(0,'添加成功');
       }
	}
	//数据源列表

	public function sourcelist()
	{
		$put = file_get_contents('php://input');
		//$put = '{"daid":1,"dataname":"测试-show"}';
		if(empty($put)){
		   $result = Db::name('datasource')->where('dtid',1)->field('daid,dataname')->select();
		}else{
           $post = json_decode($put,1);
           $select = Db::name('datasource')->where('daid',$post['daid'])->field('daid,dataname,host,username,password,port,dbname,len')->select();
           $this->host = $select[0]['host'];
           $this->name = $select[0]['username'];
          // $this->pwd = $select[0]['password'];
           $this->dbname = $select[0]['dbname'];
           $port = $select[0]['port'];
           $key = 'kwc.net';
	       $str = openssl_decrypt($select[0]['password'], 'aes-128-cbc',$key,2);
	       $pwd = substr($str, 0,$select[0]['len']);
          
           
           //连接数据库
	       $link = mysqli_connect($this->host,$this->name,$pwd,$this->dbname,$port);
	       if(!$link){
		       	//不成功，返回报错信息
		        return mysqli_connect_error();
		    }else{
                if(empty($post['tablename'])){
                   $sql = 'show tables';
                   $tables = mysqli_query($link,$sql);
                   //dump($tables);
                   while($row = mysqli_fetch_array($tables,MYSQLI_ASSOC)){
                   	
	                  $result[] = $row['Tables_in_'.$this->dbname];
		            } 
                }else{
                   $sql = "select COLUMN_NAME from INFORMATION_SCHEMA.Columns where table_name='".$post['tablename']."' and table_schema='".$this->dbname."'";
                   $fields = mysqli_query($link,$sql);
                   while($row = mysqli_fetch_array($fields,MYSQLI_ASSOC)){
	                  $result[] = $row['COLUMN_NAME'];
		            } 
                }
		    }         
           
		}
		if(empty($result)){
       	  return  get_status(1,NULL);
        }else{
       	  return  get_status(0,$result);
        }
	}
	//sql语句执行接口
	public function query_sql()
	{
		$put = file_get_contents('php://input');
		//$put = '{"daid":1,"sql":"select * from pie limit 0,50"}';
		
		if(empty($put)){
           return  get_status(1,NULL);
		}
		$post = json_decode($put,1);
	    $select = Db::name('datasource')->where('daid',$post['daid'])->field('daid,dataname,host,username,password,port,dbname,len')->select();
	    $this->host = $select[0]['host'];
	    $this->name = $select[0]['username'];
	    //$this->pwd = $select[0]['password'];
	    $this->dbname = $select[0]['dbname'];
	    $key = 'kwc.net';
	    $str = openssl_decrypt($select[0]['password'], 'aes-128-cbc',$key,2);
	    $pwd = substr($str, 0,$select[0]['len']);
	    $port = $select[0]['port'];
		$link = @mysqli_connect($this->host,$this->name,$pwd,$this->dbname,$port);
       if(!$link){
	       	//不成功，返回报错信息
	        return mysqli_connect_error();
	    }else{
	    	$sqls = mysqli_query($link,$post['sql']);
	    	while($row = mysqli_fetch_array($sqls,MYSQLI_ASSOC)){
                  $result[] = $row;
	            } 
	        
	    }
	    if(empty($result)){
       	  return  get_status(1,NULL);
        }else{
       	  return  get_status(0,$result);
        }
	}
	//添加SQL
	public function addsql()
	{
       $put = file_get_contents('php://input');
	   //$put = '{"dataname":"lalalsasas","cid":1,"sourcename":"app_bbs","tablename":"bbs_link","field":"","returnsql":"select * from bbs_link","daid":14}';
	   if(empty($put)){
           return  get_status(1,NULL);
		}
	   $post = json_decode($put,1);
	   $result = Db::name('datasource')->insert($post);

	   if(empty($result)){
       	  return  get_status(1,'添加失败');
        }else{
       	  return  get_status(0,'添加成功');
        }
      
	}
	//生成表格数据
	public function formdata()
	{
       $put = file_get_contents('php://input'); 

       //$put = '{"daid":"9","table":"sdata_user"}';
       if(empty($put)){
           return  get_status(1,NULL);
		}
       $post =json_decode($put,1);
       
       $select = Db::name('datasource')->where('daid',$post['daid'])->field('daid,dataname,host,username,password,port,dbname,len')->select();
       $this->host = $select[0]['host'];
       $this->name = $select[0]['username'];
       //$this->pwd = $select[0]['password'];
       $this->dbname = $select[0]['dbname'];
       $key = 'kwc.net';
	   $str = openssl_decrypt($select[0]['password'], 'aes-128-cbc',$key,2);
	   $pwd = substr($str, 0,$select[0]['len']);
       $port = $select[0]['port'];
       //连接数据库
       $link = @mysqli_connect($this->host,$this->name,$pwd,$this->dbname,$port);
       if(!$link){
	       	//不成功，返回报错信息
	        return mysqli_connect_error();
	    }else{
	    	if (empty($post['filed'])) {
	          $sql = 'select * from '.$post['table'];
	        }else{
	          $sql  = 'select '.$post['filed'].' from '.$post['table'];
	        }
	        //echo $sql;
	        $data = mysqli_query($link,$sql);
	        $result = [];
	        while($row = mysqli_fetch_array($data,MYSQLI_ASSOC)){
                  $result[] = $row;
	            }
	        

	        if(empty($result)){
	         	return ['err'=>0,'data'=>[],'colHeaders'=>[]];
	        }
			$keys=array_keys($result[0]);
	        for($i = 0;$i <count($result);$i++){
               $arr[] = array_values($result[$i]);
            }
	    }  
       if(empty($arr)){
         return get_status(1,NULL);
       }else{
       	 return ['err'=>0,'data'=>$arr,'colHeaders'=>$keys];
       }
	}
	//添加自定义视图
    public function customview()
    {
       $put = file_get_contents('php://input');
	   //$put = '{"dataname":"xiaoni","cid":1,"dtid":1,"sourcename":"xiao","tablename":"user","field":"name,age","returnsql":"select * from user","returnjson":"{a:b}"}';
	   $post = json_decode($put,1);
	   $result = Db::name('datasource')->insert($post);

	   if(empty($result)){
       	  return  get_status(1,'添加失败');
        }else{
       	  return  get_status(0,'添加成功');
        }
    }
    
      
}
