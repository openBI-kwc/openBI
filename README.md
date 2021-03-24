## 官方地址

[http://www.openbi.com.cn/](http://www.openbi.com.cn/)

## 安装方式一

- 环境要求PHP7.0以上。（建议PHP7.2）

- 建议环境 lnmp

- 从 [releases](https://github.com/openBI-kwc/openBI/releases/) 下载部署包openbi.zip

- 将openbi.zip 上传到服务器并解压到指定位置

- 将解压的目录授权到web服务启动用户（如： chown -R www:www /home/wwwroot/openbi）

- 配置 nginx server模块新增webserver的ip或域名与openbi的路径绑定

- 部署不支持二级目录，请直接将域名或ip定位到项目目录/public下

- 添加重写规则

  ```bash
  if (!-e $request_filename) {
        rewrite ^(.*)$ /index.php?s=$1 last;
        break;
  }
  ```

- 进入到openbi解压目录 找到.example_env 并复制为 .env 

- 导入openbi.sql

- 修改 .env 相应配置（配置数据库信息）

- 默认账号密码 admin / admin

- 请取消禁用函数 putenv (php.ini中disable_function)

## 安装方式二

- 使用宝塔面板一键部署安装

## 数据源支持

- excel/csv
- API
- websocket
- mysql
- pgsql
- SQLServer
- Oracle
- es
- redis
- MongoDB
## 系统支持

稳定支持Linux CentOS系统 

## 示例

- ![http://www.kwcnet.com/assets/img/huabei1.png](http://www.kwcnet.com/assets/img/huabei1.png)
- ![http://www.openbi.com.cn/upload/20191118/c36bbd258b7ee694eb987221b2b197b0/d0c06fd3b4b6642248a20814924b9c79.jpg](http://www.openbi.com.cn/upload/20191118/c36bbd258b7ee694eb987221b2b197b0/d0c06fd3b4b6642248a20814924b9c79.jpg)
- ![http://www.openbi.com.cn/upload/20191118/bff139fa05ac583f685a523ab3d110a0/b522c00ca66c5f633770bf2836e8e460.png](http://www.openbi.com.cn/upload/20191118/bff139fa05ac583f685a523ab3d110a0/b522c00ca66c5f633770bf2836e8e460.png)
- ![http://www.kwcnet.com/assets/img/lvyou.png](http://www.kwcnet.com/assets/img/lvyou.png)
- ![http://www.kwcnet.com/Images/e03eed0554c8b59707288bd4983f9518.jpg](http://www.kwcnet.com/Images/e03eed0554c8b59707288bd4983f9518.jpg)
- ![http://www.kwcnet.com/Images/1fb4de3967a2992258e05edd7b5f0127.png](http://www.kwcnet.com/Images/1fb4de3967a2992258e05edd7b5f0127.png)

## License

遵循nc开源协议发布，并提供个人免费使用。

## 官方QQ技术交流群

328601229 （QQ群号）


## 商务垂询
黄成：13370182900（非购买商业授权勿扰）
