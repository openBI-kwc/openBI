## 常见问题

- **安装部署后无法登录，提示500错误**

  答：取消禁用php函数 putenv

- **安装部署后无法登录，提示404错误**

  答：web服务器新增伪静态

  ```bash
  ## nginx
  location / {
  	if (!-e $request_filename) {
  		rewrite ^(.*)$ /index.php?s=/$1 last; 			
  		break; 
  	} 
  }
  ```

  ```bash
  ## apache
  <IfModule mod_rewrite.c> 
      Options +FollowSymlinks -Multiviews 		  
      RewriteEngine on 
      RewriteCond %{REQUEST_FILENAME} !-d 
      RewriteCond %{REQUEST_FILENAME} !-f RewriteRule ^(.*)$ index.php?/$1 [QSA,PT,L] 
  </IfModule>
  ```

  

- **若遇到乱码问题**

  答：1.检查数据库字符集是否异常

  ​		2.检查数据库版本（建议采用mysql5.6、mysql5.7）

   	   3.暂时不建议使用maridb

- **样式异常问题**

  答：推荐以下浏览器

  		1. chrome（谷歌浏览器）
    		2. firefox（火狐浏览器）
    		3.  edge（新版edge）

- 其他问题

  提交issue

  或者加qq群：**328601229**