/*
Navicat MySQL Data Transfer

Source Server         : 127.0.0.1
Source Server Version : 50553
Source Host           : localhost:3306
Source Database       : test

Target Server Type    : MYSQL
Target Server Version : 50553
File Encoding         : 65001

Date: 2019-10-21 18:31:32
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for up_animation
-- ----------------------------
DROP TABLE IF EXISTS `up_animation`;
CREATE TABLE `up_animation` (
  `aid` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT '文件名',
  `animationname` varchar(255) NOT NULL COMMENT '动画名称',
  `label` varchar(255) NOT NULL COMMENT '动画标签',
  `filename` varchar(255) NOT NULL COMMENT '文件全名',
  `path` varchar(255) NOT NULL COMMENT '文件路径',
  `version` int(11) NOT NULL COMMENT '文件版本',
  `createtime` int(11) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`aid`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='上传的3D动画表';

-- ----------------------------
-- Records of up_animation
-- ----------------------------
INSERT INTO `up_animation` VALUES ('1', '11', '77;00;99', '6666', '11-1.xlsx', '/unity/11-1.xlsx', '1', '1559102633');
INSERT INTO `up_animation` VALUES ('2', 'im', '11;22;33', '666;66666;666', 'im-2.html', '/unity/im-2.html', '2', '1559109373');

-- ----------------------------
-- Table structure for up_attachment
-- ----------------------------
DROP TABLE IF EXISTS `up_attachment`;
CREATE TABLE `up_attachment` (
  `attid` int(11) NOT NULL AUTO_INCREMENT COMMENT '附件id',
  `type` varchar(100) NOT NULL COMMENT '附件类型',
  `width` int(4) NOT NULL COMMENT '附件宽度',
  `height` int(4) NOT NULL COMMENT '附件高度',
  `is_water` tinyint(4) NOT NULL COMMENT '是否启用水印（1：启用，2禁用）',
  `transparency` int(4) NOT NULL COMMENT '水印透明度',
  `position` varchar(100) NOT NULL COMMENT '水印位置',
  `waterpath` varchar(100) NOT NULL COMMENT '水印路径',
  PRIMARY KEY (`attid`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='附件表';

-- ----------------------------
-- Records of up_attachment
-- ----------------------------
INSERT INTO `up_attachment` VALUES ('1', 'JPEG,PNG,JPG,SVG,BMP,GIF', '9999', '8000', '1', '33', 'RightBottom', '/uploads/logo/20191016/f4167f56d79fc2008401cea690d652aa.png');

-- ----------------------------
-- Table structure for up_backgroundimg
-- ----------------------------
DROP TABLE IF EXISTS `up_backgroundimg`;
CREATE TABLE `up_backgroundimg` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `src` text NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Records of up_backgroundimg
-- ----------------------------
INSERT INTO `up_backgroundimg` VALUES ('1', '23112');
INSERT INTO `up_backgroundimg` VALUES ('2', '23113');
INSERT INTO `up_backgroundimg` VALUES ('3', '23114');
INSERT INTO `up_backgroundimg` VALUES ('4', '23115');
INSERT INTO `up_backgroundimg` VALUES ('5', '23116');

-- ----------------------------
-- Table structure for up_backup
-- ----------------------------
DROP TABLE IF EXISTS `up_backup`;
CREATE TABLE `up_backup` (
  `bid` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `dataname` varchar(255) NOT NULL COMMENT '备份名称',
  `backtime` int(11) NOT NULL COMMENT '备份时间',
  `link` varchar(100) NOT NULL COMMENT '链接池',
  PRIMARY KEY (`bid`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='SQL备份';

-- ----------------------------
-- Records of up_backup
-- ----------------------------

-- ----------------------------
-- Table structure for up_bindscenes
-- ----------------------------
DROP TABLE IF EXISTS `up_bindscenes`;
CREATE TABLE `up_bindscenes` (
  `scenes` int(11) NOT NULL COMMENT '场景ID',
  `scenes_name` varchar(255) DEFAULT '' COMMENT '场景的名字',
  `scenes_config` varchar(255) NOT NULL COMMENT '场景的配置',
  `scenes_createtime` int(11) NOT NULL COMMENT '场景创建时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='场景绑定设备表';

-- ----------------------------
-- Records of up_bindscenes
-- ----------------------------

-- ----------------------------
-- Table structure for up_carouselrelease
-- ----------------------------
DROP TABLE IF EXISTS `up_carouselrelease`;
CREATE TABLE `up_carouselrelease` (
  `crid` int(11) NOT NULL AUTO_INCREMENT COMMENT '轮播发布页面主键ID',
  `remarks` varchar(255) DEFAULT '' COMMENT '轮播备注',
  `screens` text COMMENT '轮播图URL',
  `cname` varchar(255) DEFAULT '' COMMENT '轮播页面名字',
  `time` int(11) NOT NULL DEFAULT '5' COMMENT '轮播间隔时间',
  `createtime` int(11) DEFAULT NULL COMMENT '创建时间',
  `animation` char(50) NOT NULL COMMENT '轮播动效',
  `controlPos` char(50) NOT NULL COMMENT '控制器位置',
  `crIdent` varchar(255) NOT NULL COMMENT '标识',
  `crLink` varchar(255) NOT NULL COMMENT '轮播链接',
  `updatetime` int(11) DEFAULT NULL COMMENT '修改时间',
  PRIMARY KEY (`crid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='轮播发布页面信息表';

-- ----------------------------
-- Records of up_carouselrelease
-- ----------------------------

-- ----------------------------
-- Table structure for up_chartdata
-- ----------------------------
DROP TABLE IF EXISTS `up_chartdata`;
CREATE TABLE `up_chartdata` (
  `typeid` int(11) NOT NULL AUTO_INCREMENT COMMENT '图表的类型ID',
  `data` text NOT NULL,
  `charttype` varchar(255) NOT NULL COMMENT '图表名字',
  PRIMARY KEY (`typeid`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=88 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='图表静态数据存储表';

-- ----------------------------
-- Records of up_chartdata
-- ----------------------------
INSERT INTO `up_chartdata` VALUES ('1', '[{\"name\":\"北京\",\"value\":\"25\",\"series\":\"系列一\"},{\"name\":\"上海\",\"value\":\"75\",\"series\":\"系列一\"}]', 'biaozhuduibibingtu');
INSERT INTO `up_chartdata` VALUES ('2', '[{\"value\":\"25\",\"name\":\"北京\",\"series\":\"系列一\"},{\"value\":\"25\",\"name\":\"上海\",\"series\":\"系列一\"},{\"value\":\"50\",\"name\":\"广州\",\"series\":\"系列一\"}]', 'daitubingtu');
INSERT INTO `up_chartdata` VALUES ('3', '[{\"value\":20,\"total\":20},{\"value\":40,\"total\":100}]', 'danzhibaifenbibingtu');
INSERT INTO `up_chartdata` VALUES ('4', '[{\"value\":30,\"name\":\"男生\",\"series\":\"系列一\"},{\"value\":70,\"name\":\"女生\",\"series\":\"系列一\"},{\"value\":50,\"name\":\"英语\",\"series\":\"系列二\"},{\"value\":50,\"name\":\"日语\",\"series\":\"系列二\"},{\"value\":20,\"name\":\"上海\",\"series\":\"系列三\"},{\"value\":80,\"name\":\"武汉\",\"series\":\"系列三\"}]', 'duoweidubingtu');
INSERT INTO `up_chartdata` VALUES ('5', '[{\"value\":\"25\",\"name\":\"北京\",\"series\":\"系列一\"},{\"value\":\"25\",\"name\":\"上海\",\"series\":\"系列一\"},{\"value\":\"50\",\"name\":\"广州\",\"series\":\"系列一\"}]', 'jibenbingtu');
INSERT INTO `up_chartdata` VALUES ('6', '[{\"value\":35,\"name\":\"圆环1\",\"series\":\"系列一\"},{\"value\":15,\"name\":\"圆环2\",\"series\":\"系列一\"},{\"value\":26,\"name\":\"圆环3\",\"series\":\"系列一\"},{\"value\":24,\"name\":\"圆环4\",\"series\":\"系列一\"},{\"value\":35,\"name\":\"圆环5\",\"series\":\"系列一\"},{\"value\":15,\"name\":\"圆环6\",\"series\":\"系列一\"}]', 'huanshanbingtu');
INSERT INTO `up_chartdata` VALUES ('7', '[{\"value\":25,\"name\":\"北京\",\"series\":\"系列一\"},{\"value\":75,\"name\":\"广州\",\"series\":\"系列一\"},{\"value\":25,\"name\":\"上海\",\"series\":\"系列一\"}]', 'lunbobingtu');
INSERT INTO `up_chartdata` VALUES ('8', '[{\"value\":20,\"total\":20},{\"value\":40,\"total\":100}]', 'mubiaozhanbibingtu');
INSERT INTO `up_chartdata` VALUES ('9', '[{\"value\":300,\"name\":\"北京\"},{\"value\":100,\"name\":\"上海\"}]', 'zhibiaoduibibingtu');
INSERT INTO `up_chartdata` VALUES ('10', '[{\"value\":20,\"total\":20},{\"value\":40,\"total\":100}]', 'zhibiaozhanbibingtu');
INSERT INTO `up_chartdata` VALUES ('11', '[{\"name\":\"A\",\"value\":15,\"series\":\"系列一\"},{\"name\":\"B\",\"value\":50,\"series\":\"系列一\"},{\"name\":\"C\",\"value\":30,\"series\":\"系列一\"},{\"name\":\"D\",\"value\":20,\"series\":\"系列一\"},{\"name\":\"E\",\"value\":45,\"series\":\"系列一\"},{\"name\":\"F\",\"value\":15,\"series\":\"系列一\"},{\"name\":\"G\",\"value\":40,\"series\":\"系列一\"},{\"name\":\"H\",\"value\":30,\"series\":\"系列一\"},{\"name\":\"I\",\"value\":50,\"series\":\"系列一\"}]', 'jibenzhexiantu');
INSERT INTO `up_chartdata` VALUES ('12', '[{\"name\":\"A\",\"value\":60,\"series\":\"系列一\"},{\"name\":\"B\",\"value\":65,\"series\":\"系列一\"},{\"name\":\"C\",\"value\":40,\"series\":\"系列一\"},{\"name\":\"D\",\"value\":30,\"series\":\"系列一\"},{\"name\":\"E\",\"value\":50,\"series\":\"系列一\"},{\"name\":\"F\",\"value\":20,\"series\":\"系列一\"},{\"name\":\"G\",\"value\":80,\"series\":\"系列一\"},{\"name\":\"H\",\"value\":60,\"series\":\"系列一\"},{\"name\":\"I\",\"value\":90,\"series\":\"系列一\"}]', 'quyutu');
INSERT INTO `up_chartdata` VALUES ('13', '[{\"name\":\"A\",\"value\":15,\"series\":\"系列一\"},{\"name\":\"B\",\"value\":50,\"series\":\"系列一\"},{\"name\":\"C\",\"value\":30,\"series\":\"系列一\"},{\"name\":\"D\",\"value\":20,\"series\":\"系列一\"},{\"name\":\"E\",\"value\":45,\"series\":\"系列一\"},{\"name\":\"F\",\"value\":15,\"series\":\"系列一\"},{\"name\":\"G\",\"value\":40,\"series\":\"系列一\"},{\"name\":\"H\",\"value\":30,\"series\":\"系列一\"},{\"name\":\"I\",\"value\":50,\"series\":\"系列一\"},{\"name\":\"A\",\"value\":60,\"series\":\"系列二\"},{\"name\":\"B\",\"value\":65,\"series\":\"系列二\"},{\"name\":\"C\",\"value\":40,\"series\":\"系列二\"},{\"name\":\"D\",\"value\":30,\"series\":\"系列二\"},{\"name\":\"E\",\"value\":50,\"series\":\"系列二\"},{\"name\":\"F\",\"value\":20,\"series\":\"系列二\"},{\"name\":\"G\",\"value\":80,\"series\":\"系列二\"},{\"name\":\"H\",\"value\":60,\"series\":\"系列二\"},{\"name\":\"I\",\"value\":90,\"series\":\"系列二\"}]', 'quyufanpaiqi');
INSERT INTO `up_chartdata` VALUES ('14', '[{\"name\":\"A\",\"value\":15,\"value2\":80},{\"name\":\"B\",\"value\":50,\"value2\":30},{\"name\":\"C\",\"value\":30,\"value2\":50},{\"name\":\"D\",\"value\":20,\"value2\":70},{\"name\":\"E\",\"value\":45,\"value2\":90},{\"name\":\"F\",\"value\":15,\"value2\":20}]', 'shuangzhouzhexiantu');
INSERT INTO `up_chartdata` VALUES ('15', '[{\"name\":\"A\",\"value\":51,\"series\":\"系列一\"},{\"name\":\"B\",\"value\":71,\"series\":\"系列一\"},{\"name\":\"C\",\"value\":61,\"series\":\"系列一\"},{\"name\":\"D\",\"value\":76,\"series\":\"系列一\"},{\"name\":\"E\",\"value\":58,\"series\":\"系列一\"},{\"name\":\"F\",\"value\":25,\"series\":\"系列一\"},{\"name\":\"G\",\"value\":43,\"series\":\"系列一\"},{\"name\":\"H\",\"value\":20,\"series\":\"系列一\"},{\"name\":\"I\",\"value\":58,\"series\":\"系列一\"},{\"name\":\"A\",\"value\":100,\"series\":\"系列二\"},{\"name\":\"B\",\"value\":200,\"series\":\"系列二\"},{\"name\":\"C\",\"value\":300,\"series\":\"系列二\"},{\"name\":\"D\",\"value\":400,\"series\":\"系列二\"},{\"name\":\"E\",\"value\":500,\"series\":\"系列二\"},{\"name\":\"F\",\"value\":600,\"series\":\"系列二\"},{\"name\":\"G\",\"value\":700,\"series\":\"系列二\"},{\"name\":\"H\",\"value\":800,\"series\":\"系列二\"},{\"name\":\"I\",\"value\":900,\"series\":\"系列二\"}]', 'banmazhutu');
INSERT INTO `up_chartdata` VALUES ('16', '[{\"name\":\"A\",\"value\":51,\"series\":\"系列一\"},{\"name\":\"B\",\"value\":71,\"series\":\"系列一\"},{\"name\":\"C\",\"value\":61,\"series\":\"系列一\"},{\"name\":\"D\",\"value\":76,\"series\":\"系列一\"},{\"name\":\"E\",\"value\":58,\"series\":\"系列一\"},{\"name\":\"F\",\"value\":25,\"series\":\"系列一\"},{\"name\":\"G\",\"value\":43,\"series\":\"系列一\"},{\"name\":\"H\",\"value\":20,\"series\":\"系列一\"},{\"name\":\"I\",\"value\":58,\"series\":\"系列一\"},{\"name\":\"A\",\"value\":100,\"series\":\"系列二\"},{\"name\":\"B\",\"value\":200,\"series\":\"系列二\"},{\"name\":\"C\",\"value\":300,\"series\":\"系列二\"},{\"name\":\"D\",\"value\":400,\"series\":\"系列二\"},{\"name\":\"E\",\"value\":500,\"series\":\"系列二\"},{\"name\":\"F\",\"value\":600,\"series\":\"系列二\"},{\"name\":\"G\",\"value\":700,\"series\":\"系列二\"},{\"name\":\"H\",\"value\":800,\"series\":\"系列二\"},{\"name\":\"I\",\"value\":900,\"series\":\"系列二\"}]', 'chuizhijibenzhutu');
INSERT INTO `up_chartdata` VALUES ('17', '[{\"name\":\"A\",\"value\":20,\"series\":\"系列一\"},{\"name\":\"B\",\"value\":20,\"series\":\"系列一\"},{\"name\":\"C\",\"value\":20,\"series\":\"系列一\"},{\"name\":\"D\",\"value\":20,\"series\":\"系列一\"},{\"name\":\"E\",\"value\":20,\"series\":\"系列一\"},{\"name\":\"F\",\"value\":20,\"series\":\"系列一\"},{\"name\":\"G\",\"value\":20,\"series\":\"系列一\"},{\"name\":\"H\",\"value\":20,\"series\":\"系列一\"},{\"name\":\"I\",\"value\":10,\"series\":\"系列一\"},{\"name\":\"A\",\"value\":40,\"series\":\"系列二\"},{\"name\":\"B\",\"value\":40,\"series\":\"系列二\"},{\"name\":\"C\",\"value\":40,\"series\":\"系列二\"},{\"name\":\"D\",\"value\":40,\"series\":\"系列二\"},{\"name\":\"E\",\"value\":40,\"series\":\"系列二\"},{\"name\":\"F\",\"value\":40,\"series\":\"系列二\"},{\"name\":\"G\",\"value\":40,\"series\":\"系列二\"},{\"name\":\"H\",\"value\":40,\"series\":\"系列二\"},{\"name\":\"I\",\"value\":30,\"series\":\"系列二\"},{\"name\":\"A\",\"value\":60,\"series\":\"系列三\"},{\"name\":\"B\",\"value\":60,\"series\":\"系列三\"},{\"name\":\"C\",\"value\":60,\"series\":\"系列三\"},{\"name\":\"D\",\"value\":60,\"series\":\"系列三\"},{\"name\":\"E\",\"value\":60,\"series\":\"系列三\"},{\"name\":\"F\",\"value\":60,\"series\":\"系列三\"},{\"name\":\"G\",\"value\":60,\"series\":\"系列三\"},{\"name\":\"H\",\"value\":60,\"series\":\"系列三\"},{\"name\":\"I\",\"value\":70,\"series\":\"系列三\"},{\"name\":\"A\",\"value\":76,\"series\":\"系列四\"},{\"name\":\"B\",\"value\":76,\"series\":\"系列四\"},{\"name\":\"C\",\"value\":76,\"series\":\"系列四\"},{\"name\":\"D\",\"value\":76,\"series\":\"系列四\"},{\"name\":\"E\",\"value\":76,\"series\":\"系列四\"},{\"name\":\"F\",\"value\":76,\"series\":\"系列四\"},{\"name\":\"G\",\"value\":76,\"series\":\"系列四\"},{\"name\":\"H\",\"value\":76,\"series\":\"系列四\"},{\"name\":\"I\",\"value\":80,\"series\":\"系列四\"}]', 'chuizhijiaonanzhutu');
INSERT INTO `up_chartdata` VALUES ('18', '[{\"name\":\"D\",\"value\":212,\"series\":\"系列一\"},{\"name\":\"C\",\"value\":98,\"series\":\"系列一\"},{\"name\":\"B\",\"value\":225,\"series\":\"系列一\"},{\"name\":\"A\",\"value\":123,\"series\":\"系列一\"},{\"name\":\"D\",\"value\":154,\"series\":\"系列二\"},{\"name\":\"C\",\"value\":162,\"series\":\"系列二\"},{\"name\":\"B\",\"value\":190,\"series\":\"系列二\"},{\"name\":\"A\",\"value\":77,\"series\":\"系列二\"}]', 'fenzuzhutu');
INSERT INTO `up_chartdata` VALUES ('19', '[{\"name\":\"张三\",\"value\":188,\"series\":\"系列一\"},{\"name\":\"李四\",\"value\":250,\"series\":\"系列一\"},{\"name\":\"王五\",\"value\":438,\"series\":\"系列一\"},{\"name\":\"赵六\",\"value\":8848,\"series\":\"系列一\"},{\"name\":\"陈七\",\"value\":9527,\"series\":\"系列一\"},{\"name\":\"朱八\",\"value\":10086,\"series\":\"系列一\"}]', 'huxingzhutu');
INSERT INTO `up_chartdata` VALUES ('20', '[{\"name\":\"A\",\"value\":15,\"series\":\"系列一\"},{\"name\":\"B\",\"value\":50,\"series\":\"系列一\"},{\"name\":\"C\",\"value\":30,\"series\":\"系列一\"},{\"name\":\"D\",\"value\":20,\"series\":\"系列一\"},{\"name\":\"E\",\"value\":45,\"series\":\"系列一\"},{\"name\":\"F\",\"value\":15,\"series\":\"系列一\"},{\"name\":\"G\",\"value\":40,\"series\":\"系列一\"},{\"name\":\"H\",\"value\":30,\"series\":\"系列一\"},{\"name\":\"I\",\"value\":50,\"series\":\"系列一\"}]', 'jibenzhutu');
INSERT INTO `up_chartdata` VALUES ('21', '[{\"name\":\"D\",\"value\":151,\"series\":\"系列一\"},{\"name\":\"C\",\"value\":71,\"series\":\"系列一\"},{\"name\":\"B\",\"value\":141,\"series\":\"系列一\"},{\"name\":\"A\",\"value\":76,\"series\":\"系列一\"},{\"name\":\"D\",\"value\":100,\"series\":\"系列二\"},{\"name\":\"C\",\"value\":120,\"series\":\"系列二\"},{\"name\":\"B\",\"value\":140,\"series\":\"系列二\"},{\"name\":\"A\",\"value\":100,\"series\":\"系列二\"}]', 'shuipingjibenzhutu');
INSERT INTO `up_chartdata` VALUES ('22', '[{\"name\":\"D\",\"value\":100,\"series\":\"系列一\"},{\"name\":\"C\",\"value\":100,\"series\":\"系列一\"},{\"name\":\"B\",\"value\":100,\"series\":\"系列一\"},{\"name\":\"A\",\"value\":100,\"series\":\"系列一\"},{\"name\":\"D\",\"value\":200,\"series\":\"系列二\"},{\"name\":\"C\",\"value\":200,\"series\":\"系列二\"},{\"name\":\"B\",\"value\":200,\"series\":\"系列二\"},{\"name\":\"A\",\"value\":200,\"series\":\"系列二\"},{\"name\":\"D\",\"value\":220,\"series\":\"系列三\"},{\"name\":\"C\",\"value\":220,\"series\":\"系列三\"},{\"name\":\"B\",\"value\":220,\"series\":\"系列三\"},{\"name\":\"A\",\"value\":46,\"series\":\"系列三\"},{\"name\":\"D\",\"value\":240,\"series\":\"系列四\"},{\"name\":\"C\",\"value\":240,\"series\":\"系列四\"},{\"name\":\"B\",\"value\":240,\"series\":\"系列四\"},{\"name\":\"A\",\"value\":41,\"series\":\"系列四\"}]', 'shuipingjiaonanzhutu');
INSERT INTO `up_chartdata` VALUES ('23', '[{\"name\":\"A\",\"value\":80,\"series\":\"系列一\"},{\"name\":\"B\",\"value\":60,\"series\":\"系列一\"},{\"name\":\"C\",\"value\":61,\"series\":\"系列一\"},{\"name\":\"D\",\"value\":70,\"series\":\"系列一\"},{\"name\":\"E\",\"value\":58,\"series\":\"系列一\"},{\"name\":\"F\",\"value\":40,\"series\":\"系列一\"},{\"name\":\"G\",\"value\":70,\"series\":\"系列一\"},{\"name\":\"H\",\"value\":50,\"series\":\"系列一\"},{\"name\":\"I\",\"value\":40,\"series\":\"系列一\"},{\"name\":\"A\",\"value\":60,\"series\":\"系列二\"},{\"name\":\"B\",\"value\":40,\"series\":\"系列二\"},{\"name\":\"C\",\"value\":50,\"series\":\"系列二\"},{\"name\":\"D\",\"value\":40,\"series\":\"系列二\"},{\"name\":\"E\",\"value\":20,\"series\":\"系列二\"},{\"name\":\"F\",\"value\":30,\"series\":\"系列二\"},{\"name\":\"G\",\"value\":60,\"series\":\"系列二\"},{\"name\":\"H\",\"value\":40,\"series\":\"系列二\"},{\"name\":\"I\",\"value\":20,\"series\":\"系列二\"}]', 'tixingzhutu');
INSERT INTO `up_chartdata` VALUES ('24', '[{\"name\":\"A\",\"barval\":24,\"lineval\":30},{\"name\":\"B\",\"barval\":14,\"lineval\":13},{\"name\":\"C\",\"barval\":26,\"lineval\":23},{\"name\":\"D\",\"barval\":34,\"lineval\":33}]', 'zhexianzhutu');
INSERT INTO `up_chartdata` VALUES ('25', '[{\"x\":12,\"y\":50,\"r\":6},{\"x\":10,\"y\":30,\"r\":15},{\"x\":15,\"y\":70,\"r\":10},{\"x\":16,\"y\":24,\"r\":58},{\"x\":39,\"y\":50,\"r\":6},{\"x\":46,\"y\":90,\"r\":2},{\"x\":45,\"y\":60,\"r\":1},{\"x\":65,\"y\":90,\"r\":5},{\"x\":25,\"y\":17,\"r\":15},{\"x\":15,\"y\":40,\"r\":16},{\"x\":35,\"y\":70,\"r\":23},{\"x\":22,\"y\":12,\"r\":14},{\"x\":45,\"y\":34,\"r\":10},{\"x\":22,\"y\":33,\"r\":15}]', 'qipaotu');
INSERT INTO `up_chartdata` VALUES ('26', '[{\"x\":12,\"y\":50,\"r\":6},{\"x\":10,\"y\":30,\"r\":15},{\"x\":15,\"y\":70,\"r\":10},{\"x\":16,\"y\":24,\"r\":58},{\"x\":39,\"y\":50,\"r\":6},{\"x\":46,\"y\":90,\"r\":2},{\"x\":45,\"y\":60,\"r\":1},{\"x\":65,\"y\":90,\"r\":5},{\"x\":25,\"y\":17,\"r\":15},{\"x\":15,\"y\":40,\"r\":16},{\"x\":35,\"y\":70,\"r\":23},{\"x\":22,\"y\":12,\"r\":14},{\"x\":45,\"y\":34,\"r\":10},{\"x\":22,\"y\":33,\"r\":15}]', 'sandiantu');
INSERT INTO `up_chartdata` VALUES ('27', '[{\"name\":\"节点1\",\"value\":22.2,\"source\":\"节点1\",\"target\":\"节点2\",\"category\":\"index1\"},{\"name\":\"节点2\",\"value\":33.2,\"source\":\"节点2\",\"target\":\"节点31\",\"category\":\"index2\"},{\"name\":\"节点3\",\"value\":26.2,\"source\":\"节点1\",\"target\":\"节点3\",\"category\":\"index3\"},{\"name\":\"节点4\",\"value\":18.2,\"source\":\"节点1\",\"target\":\"节点4\",\"category\":\"index4\"},{\"name\":\"节点5\",\"value\":43.2,\"source\":\"节点1\",\"target\":\"节点5\",\"category\":\"index5\"},{\"name\":\"节点11\",\"value\":22.2,\"source\":\"节点1\",\"target\":\"节点11\",\"category\":\"index11\"},{\"name\":\"节点21\",\"value\":33.2,\"source\":\"节点1\",\"target\":\"节点21\",\"category\":\"index21\"},{\"name\":\"节点31\",\"value\":26.2,\"source\":\"节点31\",\"target\":\"节点24\",\"category\":\"index31\"},{\"name\":\"节点41\",\"value\":18.2,\"source\":\"节点1\",\"target\":\"节点41\",\"category\":\"index41\"},{\"name\":\"节点51\",\"value\":43.2,\"source\":\"节点1\",\"target\":\"节点51\",\"category\":\"index51\"},{\"name\":\"节点12\",\"value\":22.2,\"source\":\"节点1\",\"target\":\"节点12\",\"category\":\"index12\"},{\"name\":\"节点22\",\"value\":33.2,\"source\":\"节点22\",\"target\":\"节点11\",\"category\":\"index22\"},{\"name\":\"节点24\",\"value\":33.2,\"source\":\"节点14\",\"target\":\"节点24\",\"category\":\"index24\"},{\"name\":\"节点34\",\"value\":26.2,\"source\":\"节点1\",\"target\":\"节点34\",\"category\":\"index34\"},{\"name\":\"节点44\",\"value\":18.2,\"source\":\"节点44\",\"target\":\"节点34\",\"category\":\"index44\"},{\"name\":\"节点54\",\"value\":43.2,\"source\":\"节点1\",\"target\":\"节点54\",\"category\":\"index54\"},{\"name\":\"节点64\",\"value\":43.2,\"source\":\"节点64\",\"target\":\"节点54\",\"category\":\"index54\"}]', 'guanxiwangluo');
INSERT INTO `up_chartdata` VALUES ('28', '[{\"name\":\"a\",\"value\":3,\"target\":\"指标一\"},{\"name\":\"b\",\"value\":2,\"target\":\"指标一\"},{\"name\":\"c\",\"value\":4,\"target\":\"指标一\"},{\"name\":\"a\",\"value\":3,\"target\":\"指标二\"},{\"name\":\"b\",\"value\":3.3,\"target\":\"指标二\"},{\"name\":\"c\",\"value\":2,\"target\":\"指标二\"},{\"name\":\"a\",\"value\":2,\"target\":\"指标三\"},{\"name\":\"b\",\"value\":3,\"target\":\"指标三\"},{\"name\":\"c\",\"value\":5,\"target\":\"指标三\"},{\"name\":\"a\",\"value\":5,\"target\":\"指标四\"},{\"name\":\"b\",\"value\":3,\"target\":\"指标四\"},{\"name\":\"c\",\"value\":4,\"target\":\"指标四\"},{\"name\":\"a\",\"value\":3.5,\"target\":\"指标五\"},{\"name\":\"b\",\"value\":4,\"target\":\"指标五\"},{\"name\":\"c\",\"value\":5.5,\"target\":\"指标五\"}]', 'leidatu');
INSERT INTO `up_chartdata` VALUES ('29', '[{\"name\":\"完成率\",\"value\":60}]', 'yibiaopan');
INSERT INTO `up_chartdata` VALUES ('30', '[{\"name\":\"visualMap\",\"value\":22199,\"url\":\"www.kwcnet.com\"},{\"name\":\"continuous\",\"value\":10288,\"url\":\"www.kwcnet.com\"},{\"name\":\"contoller\",\"value\":620,\"url\":\"www.kwcnet.com\"},{\"name\":\"series\",\"value\":274470,\"url\":\"www.kwcnet.com\"},{\"name\":\"gauge\",\"value\":12311,\"url\":\"www.kwcnet.com\"},{\"name\":\"detail\",\"value\":1206,\"url\":\"www.kwcnet.com\"},{\"name\":\"piecewise\",\"value\":4885,\"url\":\"www.kwcnet.com\"},{\"name\":\"textStyle\",\"value\":32294,\"url\":\"www.kwcnet.com\"},{\"name\":\"markPoint\",\"value\":18574,\"url\":\"www.kwcnet.com\"},{\"name\":\"pie\",\"value\":38929,\"url\":\"www.kwcnet.com\"},{\"name\":\"roseType\",\"value\":969,\"url\":\"www.kwcnet.com\"},{\"name\":\"label\",\"value\":37517,\"url\":\"www.kwcnet.com\"},{\"name\":\"emphasis\",\"value\":12053,\"url\":\"www.kwcnet.com\"},{\"name\":\"yAxis\",\"value\":57299,\"url\":\"www.kwcnet.com\"},{\"name\":\"name\",\"value\":15418,\"url\":\"www.kwcnet.com\"},{\"name\":\"type\",\"value\":22905,\"url\":\"www.kwcnet.com\"},{\"name\":\"gridIndex\",\"value\":5146,\"url\":\"www.kwcnet.com\"},{\"name\":\"normal\",\"value\":49487,\"url\":\"www.kwcnet.com\"},{\"name\":\"itemStyle\",\"value\":33837,\"url\":\"www.kwcnet.com\"},{\"name\":\"min\",\"value\":4500,\"url\":\"www.kwcnet.com\"},{\"name\":\"silent\",\"value\":5744,\"url\":\"www.kwcnet.com\"},{\"name\":\"animation\",\"value\":4840,\"url\":\"www.kwcnet.com\"},{\"name\":\"offsetCenter\",\"value\":232,\"url\":\"www.kwcnet.com\"},{\"name\":\"inverse\",\"value\":3706,\"url\":\"www.kwcnet.com\"},{\"name\":\"borderColor\",\"value\":4812,\"url\":\"www.kwcnet.com\"},{\"name\":\"markLine\",\"value\":16578,\"url\":\"www.kwcnet.com\"},{\"name\":\"line\",\"value\":76970,\"url\":\"www.kwcnet.com\"},{\"name\":\"radiusAxis\",\"value\":6704,\"url\":\"www.kwcnet.com\"},{\"name\":\"radar\",\"value\":15964,\"url\":\"www.kwcnet.com\"},{\"name\":\"data\",\"value\":60679,\"url\":\"www.kwcnet.com\"},{\"name\":\"dataZoom\",\"value\":24347,\"url\":\"www.kwcnet.com\"},{\"name\":\"tooltip\",\"value\":43420,\"url\":\"www.kwcnet.com\"},{\"name\":\"toolbox\",\"value\":25222,\"url\":\"www.kwcnet.com\"},{\"name\":\"geo\",\"value\":16904,\"url\":\"www.kwcnet.com\"},{\"name\":\"parallelAxis\",\"value\":4029,\"url\":\"www.kwcnet.com\"},{\"name\":\"parallel\",\"value\":5319,\"url\":\"www.kwcnet.com\"},{\"name\":\"max\",\"value\":3393,\"url\":\"www.kwcnet.com\"},{\"name\":\"bar\",\"value\":43066,\"url\":\"www.kwcnet.com\"},{\"name\":\"heatmap\",\"value\":3110,\"url\":\"www.kwcnet.com\"},{\"name\":\"map\",\"value\":20285,\"url\":\"www.kwcnet.com\"},{\"name\":\"animationDuration\",\"value\":3425,\"url\":\"www.kwcnet.com\"},{\"name\":\"animationDelay\",\"value\":2431,\"url\":\"www.kwcnet.com\"},{\"name\":\"splitNumber\",\"value\":5175,\"url\":\"www.kwcnet.com\"},{\"name\":\"axisLine\",\"value\":12738,\"url\":\"www.kwcnet.com\"},{\"name\":\"lineStyle\",\"value\":19601,\"url\":\"www.kwcnet.com\"},{\"name\":\"splitLine\",\"value\":7133,\"url\":\"www.kwcnet.com\"},{\"name\":\"axisTick\",\"value\":8831,\"url\":\"www.kwcnet.com\"},{\"name\":\"axisLabel\",\"value\":17516,\"url\":\"www.kwcnet.com\"},{\"name\":\"pointer\",\"value\":590,\"url\":\"www.kwcnet.com\"},{\"name\":\"color\",\"value\":23426,\"url\":\"www.kwcnet.com\"},{\"name\":\"title\",\"value\":38497,\"url\":\"www.kwcnet.com\"},{\"name\":\"formatter\",\"value\":15214,\"url\":\"www.kwcnet.com\"},{\"name\":\"slider\",\"value\":7236,\"url\":\"www.kwcnet.com\"},{\"name\":\"legend\",\"value\":66514,\"url\":\"www.kwcnet.com\"},{\"name\":\"grid\",\"value\":28516,\"url\":\"www.kwcnet.com\"},{\"name\":\"smooth\",\"value\":1295,\"url\":\"www.kwcnet.com\"},{\"name\":\"smoothMonotone\",\"value\":696,\"url\":\"www.kwcnet.com\"},{\"name\":\"sampling\",\"value\":757,\"url\":\"www.kwcnet.com\"},{\"name\":\"feature\",\"value\":12815,\"url\":\"www.kwcnet.com\"},{\"name\":\"saveAsImage\",\"value\":2616,\"url\":\"www.kwcnet.com\"},{\"name\":\"polar\",\"value\":6279,\"url\":\"www.kwcnet.com\"},{\"name\":\"calculable\",\"value\":879,\"url\":\"www.kwcnet.com\"},{\"name\":\"backgroundColor\",\"value\":9419,\"url\":\"www.kwcnet.com\"},{\"name\":\"excludeComponents\",\"value\":130,\"url\":\"www.kwcnet.com\"},{\"name\":\"show\",\"value\":20620,\"url\":\"www.kwcnet.com\"},{\"name\":\"text\",\"value\":2592,\"url\":\"www.kwcnet.com\"},{\"name\":\"icon\",\"value\":2782,\"url\":\"www.kwcnet.com\"},{\"name\":\"dimension\",\"value\":478,\"url\":\"www.kwcnet.com\"},{\"name\":\"inRange\",\"value\":1060,\"url\":\"www.kwcnet.com\"},{\"name\":\"animationEasing\",\"value\":2983,\"url\":\"www.kwcnet.com\"},{\"name\":\"animationDurationUpdate\",\"value\":2259,\"url\":\"www.kwcnet.com\"},{\"name\":\"animationDelayUpdate\",\"value\":2236,\"url\":\"www.kwcnet.com\"},{\"name\":\"animationEasingUpdate\",\"value\":2213,\"url\":\"www.kwcnet.com\"},{\"name\":\"xAxis\",\"value\":89459,\"url\":\"www.kwcnet.com\"},{\"name\":\"angleAxis\",\"value\":5469,\"url\":\"www.kwcnet.com\"},{\"name\":\"showTitle\",\"value\":484,\"url\":\"www.kwcnet.com\"},{\"name\":\"dataView\",\"value\":2754,\"url\":\"www.kwcnet.com\"},{\"name\":\"restore\",\"value\":932,\"url\":\"www.kwcnet.com\"},{\"name\":\"timeline\",\"value\":10104,\"url\":\"www.kwcnet.com\"},{\"name\":\"range\",\"value\":477,\"url\":\"www.kwcnet.com\"},{\"name\":\"value\",\"value\":5732,\"url\":\"www.kwcnet.com\"},{\"name\":\"precision\",\"value\":878,\"url\":\"www.kwcnet.com\"},{\"name\":\"target\",\"value\":1433,\"url\":\"www.kwcnet.com\"},{\"name\":\"zlevel\",\"value\":5361,\"url\":\"www.kwcnet.com\"},{\"name\":\"symbol\",\"value\":8718,\"url\":\"www.kwcnet.com\"},{\"name\":\"interval\",\"value\":7964,\"url\":\"www.kwcnet.com\"},{\"name\":\"symbolSize\",\"value\":5300,\"url\":\"www.kwcnet.com\"},{\"name\":\"showSymbol\",\"value\":1247,\"url\":\"www.kwcnet.com\"},{\"name\":\"inside\",\"value\":8913,\"url\":\"www.kwcnet.com\"},{\"name\":\"xAxisIndex\",\"value\":3843,\"url\":\"www.kwcnet.com\"},{\"name\":\"orient\",\"value\":4205,\"url\":\"www.kwcnet.com\"},{\"name\":\"boundaryGap\",\"value\":5073,\"url\":\"www.kwcnet.com\"},{\"name\":\"nameGap\",\"value\":4896,\"url\":\"www.kwcnet.com\"},{\"name\":\"zoomLock\",\"value\":571,\"url\":\"www.kwcnet.com\"},{\"name\":\"hoverAnimation\",\"value\":2307,\"url\":\"www.kwcnet.com\"},{\"name\":\"legendHoverLink\",\"value\":3553,\"url\":\"www.kwcnet.com\"},{\"name\":\"stack\",\"value\":2907,\"url\":\"www.kwcnet.com\"},{\"name\":\"throttle\",\"value\":466,\"url\":\"www.kwcnet.com\"},{\"name\":\"connectNulls\",\"value\":897,\"url\":\"www.kwcnet.com\"},{\"name\":\"clipOverflow\",\"value\":826,\"url\":\"www.kwcnet.com\"},{\"name\":\"startValue\",\"value\":551,\"url\":\"www.kwcnet.com\"},{\"name\":\"minInterval\",\"value\":3292,\"url\":\"www.kwcnet.com\"},{\"name\":\"opacity\",\"value\":3097,\"url\":\"www.kwcnet.com\"},{\"name\":\"splitArea\",\"value\":4775,\"url\":\"www.kwcnet.com\"},{\"name\":\"filterMode\",\"value\":635,\"url\":\"www.kwcnet.com\"},{\"name\":\"end\",\"value\":409,\"url\":\"www.kwcnet.com\"},{\"name\":\"left\",\"value\":6475,\"url\":\"www.kwcnet.com\"},{\"name\":\"funnel\",\"value\":2238,\"url\":\"www.kwcnet.com\"},{\"name\":\"lines\",\"value\":6403,\"url\":\"www.kwcnet.com\"},{\"name\":\"baseline\",\"value\":431,\"url\":\"www.kwcnet.com\"},{\"name\":\"align\",\"value\":2608,\"url\":\"www.kwcnet.com\"},{\"name\":\"coord\",\"value\":897,\"url\":\"www.kwcnet.com\"},{\"name\":\"nameTextStyle\",\"value\":7477,\"url\":\"www.kwcnet.com\"},{\"name\":\"width\",\"value\":4338,\"url\":\"www.kwcnet.com\"},{\"name\":\"shadowBlur\",\"value\":4493,\"url\":\"www.kwcnet.com\"},{\"name\":\"effect\",\"value\":929,\"url\":\"www.kwcnet.com\"},{\"name\":\"period\",\"value\":225,\"url\":\"www.kwcnet.com\"},{\"name\":\"areaColor\",\"value\":631,\"url\":\"www.kwcnet.com\"},{\"name\":\"borderWidth\",\"value\":3654,\"url\":\"www.kwcnet.com\"},{\"name\":\"nameLocation\",\"value\":4418,\"url\":\"www.kwcnet.com\"},{\"name\":\"position\",\"value\":11723,\"url\":\"www.kwcnet.com\"},{\"name\":\"containLabel\",\"value\":1701,\"url\":\"www.kwcnet.com\"},{\"name\":\"scatter\",\"value\":10718,\"url\":\"www.kwcnet.com\"},{\"name\":\"areaStyle\",\"value\":5310,\"url\":\"www.kwcnet.com\"},{\"name\":\"scale\",\"value\":3859,\"url\":\"www.kwcnet.com\"},{\"name\":\"pieces\",\"value\":414,\"url\":\"www.kwcnet.com\"},{\"name\":\"categories\",\"value\":1000,\"url\":\"www.kwcnet.com\"},{\"name\":\"selectedMode\",\"value\":3825,\"url\":\"www.kwcnet.com\"},{\"name\":\"itemSymbol\",\"value\":273,\"url\":\"www.kwcnet.com\"},{\"name\":\"effectScatter\",\"value\":7147,\"url\":\"www.kwcnet.com\"},{\"name\":\"fontStyle\",\"value\":3376,\"url\":\"www.kwcnet.com\"},{\"name\":\"fontSize\",\"value\":3386,\"url\":\"www.kwcnet.com\"},{\"name\":\"margin\",\"value\":1034,\"url\":\"www.kwcnet.com\"},{\"name\":\"iconStyle\",\"value\":2257,\"url\":\"www.kwcnet.com\"},{\"name\":\"link\",\"value\":1366,\"url\":\"www.kwcnet.com\"},{\"name\":\"axisPointer\",\"value\":5245,\"url\":\"www.kwcnet.com\"},{\"name\":\"showDelay\",\"value\":896,\"url\":\"www.kwcnet.com\"},{\"name\":\"graph\",\"value\":22194,\"url\":\"www.kwcnet.com\"},{\"name\":\"subtext\",\"value\":1442,\"url\":\"www.kwcnet.com\"},{\"name\":\"selected\",\"value\":2881,\"url\":\"www.kwcnet.com\"},{\"name\":\"barCategoryGap\",\"value\":827,\"url\":\"www.kwcnet.com\"},{\"name\":\"barGap\",\"value\":1094,\"url\":\"www.kwcnet.com\"},{\"name\":\"barWidth\",\"value\":1521,\"url\":\"www.kwcnet.com\"},{\"name\":\"coordinateSystem\",\"value\":3622,\"url\":\"www.kwcnet.com\"},{\"name\":\"barBorderRadius\",\"value\":284,\"url\":\"www.kwcnet.com\"},{\"name\":\"z\",\"value\":4014,\"url\":\"www.kwcnet.com\"},{\"name\":\"polarIndex\",\"value\":1456,\"url\":\"www.kwcnet.com\"},{\"name\":\"shadowOffsetX\",\"value\":3046,\"url\":\"www.kwcnet.com\"},{\"name\":\"shadowColor\",\"value\":3771,\"url\":\"www.kwcnet.com\"},{\"name\":\"shadowOffsetY\",\"value\":2475,\"url\":\"www.kwcnet.com\"},{\"name\":\"height\",\"value\":1988,\"url\":\"www.kwcnet.com\"},{\"name\":\"barMinHeight\",\"value\":575,\"url\":\"www.kwcnet.com\"},{\"name\":\"lang\",\"value\":131,\"url\":\"www.kwcnet.com\"},{\"name\":\"symbolRotate\",\"value\":2752,\"url\":\"www.kwcnet.com\"},{\"name\":\"symbolOffset\",\"value\":2549,\"url\":\"www.kwcnet.com\"},{\"name\":\"showAllSymbol\",\"value\":942,\"url\":\"www.kwcnet.com\"},{\"name\":\"transitionDuration\",\"value\":993,\"url\":\"www.kwcnet.com\"},{\"name\":\"bottom\",\"value\":3724,\"url\":\"www.kwcnet.com\"},{\"name\":\"fillerColor\",\"value\":229,\"url\":\"www.kwcnet.com\"},{\"name\":\"nameMap\",\"value\":1249,\"url\":\"www.kwcnet.com\"},{\"name\":\"barMaxWidth\",\"value\":747,\"url\":\"www.kwcnet.com\"},{\"name\":\"radius\",\"value\":2103,\"url\":\"www.kwcnet.com\"},{\"name\":\"center\",\"value\":2425,\"url\":\"www.kwcnet.com\"},{\"name\":\"magicType\",\"value\":3276,\"url\":\"www.kwcnet.com\"},{\"name\":\"labelPrecision\",\"value\":248,\"url\":\"www.kwcnet.com\"},{\"name\":\"option\",\"value\":654,\"url\":\"www.kwcnet.com\"},{\"name\":\"seriesIndex\",\"value\":935,\"url\":\"www.kwcnet.com\"},{\"name\":\"controlPosition\",\"value\":121,\"url\":\"www.kwcnet.com\"},{\"name\":\"itemGap\",\"value\":3188,\"url\":\"www.kwcnet.com\"},{\"name\":\"padding\",\"value\":3481,\"url\":\"www.kwcnet.com\"},{\"name\":\"shadowStyle\",\"value\":347,\"url\":\"www.kwcnet.com\"},{\"name\":\"boxplot\",\"value\":1394,\"url\":\"www.kwcnet.com\"},{\"name\":\"labelFormatter\",\"value\":264,\"url\":\"www.kwcnet.com\"},{\"name\":\"realtime\",\"value\":631,\"url\":\"www.kwcnet.com\"},{\"name\":\"dataBackgroundColor\",\"value\":239,\"url\":\"www.kwcnet.com\"},{\"name\":\"showDetail\",\"value\":247,\"url\":\"www.kwcnet.com\"},{\"name\":\"showDataShadow\",\"value\":217,\"url\":\"www.kwcnet.com\"},{\"name\":\"x\",\"value\":684,\"url\":\"www.kwcnet.com\"},{\"name\":\"valueDim\",\"value\":499,\"url\":\"www.kwcnet.com\"},{\"name\":\"onZero\",\"value\":931,\"url\":\"www.kwcnet.com\"},{\"name\":\"right\",\"value\":3255,\"url\":\"www.kwcnet.com\"},{\"name\":\"clockwise\",\"value\":1035,\"url\":\"www.kwcnet.com\"},{\"name\":\"itemWidth\",\"value\":1732,\"url\":\"www.kwcnet.com\"},{\"name\":\"trigger\",\"value\":3840,\"url\":\"www.kwcnet.com\"},{\"name\":\"axis\",\"value\":379,\"url\":\"www.kwcnet.com\"},{\"name\":\"selectedOffset\",\"value\":670,\"url\":\"www.kwcnet.com\"},{\"name\":\"startAngle\",\"value\":1293,\"url\":\"www.kwcnet.com\"},{\"name\":\"minAngle\",\"value\":590,\"url\":\"www.kwcnet.com\"},{\"name\":\"top\",\"value\":4637,\"url\":\"www.kwcnet.com\"},{\"name\":\"avoidLabelOverlap\",\"value\":870,\"url\":\"www.kwcnet.com\"},{\"name\":\"labelLine\",\"value\":3785,\"url\":\"www.kwcnet.com\"},{\"name\":\"sankey\",\"value\":2933,\"url\":\"www.kwcnet.com\"},{\"name\":\"endAngle\",\"value\":213,\"url\":\"www.kwcnet.com\"},{\"name\":\"start\",\"value\":779,\"url\":\"www.kwcnet.com\"},{\"name\":\"roam\",\"value\":1738,\"url\":\"www.kwcnet.com\"},{\"name\":\"fontWeight\",\"value\":2828,\"url\":\"www.kwcnet.com\"},{\"name\":\"fontFamily\",\"value\":2490,\"url\":\"www.kwcnet.com\"},{\"name\":\"subtextStyle\",\"value\":2066,\"url\":\"www.kwcnet.com\"},{\"name\":\"indicator\",\"value\":853,\"url\":\"www.kwcnet.com\"},{\"name\":\"sublink\",\"value\":708,\"url\":\"www.kwcnet.com\"},{\"name\":\"zoom\",\"value\":1038,\"url\":\"www.kwcnet.com\"},{\"name\":\"subtarget\",\"value\":659,\"url\":\"www.kwcnet.com\"},{\"name\":\"length\",\"value\":1060,\"url\":\"www.kwcnet.com\"},{\"name\":\"itemSize\",\"value\":505,\"url\":\"www.kwcnet.com\"},{\"name\":\"controlStyle\",\"value\":452,\"url\":\"www.kwcnet.com\"},{\"name\":\"yAxisIndex\",\"value\":2529,\"url\":\"www.kwcnet.com\"},{\"name\":\"edgeLabel\",\"value\":1188,\"url\":\"www.kwcnet.com\"},{\"name\":\"radiusAxisIndex\",\"value\":354,\"url\":\"www.kwcnet.com\"},{\"name\":\"scaleLimit\",\"value\":1313,\"url\":\"www.kwcnet.com\"},{\"name\":\"geoIndex\",\"value\":535,\"url\":\"www.kwcnet.com\"},{\"name\":\"regions\",\"value\":1892,\"url\":\"www.kwcnet.com\"},{\"name\":\"itemHeight\",\"value\":1290,\"url\":\"www.kwcnet.com\"},{\"name\":\"nodes\",\"value\":644,\"url\":\"www.kwcnet.com\"},{\"name\":\"candlestick\",\"value\":3166,\"url\":\"www.kwcnet.com\"},{\"name\":\"crossStyle\",\"value\":466,\"url\":\"www.kwcnet.com\"},{\"name\":\"edges\",\"value\":369,\"url\":\"www.kwcnet.com\"},{\"name\":\"links\",\"value\":3277,\"url\":\"www.kwcnet.com\"},{\"name\":\"layout\",\"value\":846,\"url\":\"www.kwcnet.com\"},{\"name\":\"barBorderColor\",\"value\":721,\"url\":\"www.kwcnet.com\"},{\"name\":\"barBorderWidth\",\"value\":498,\"url\":\"www.kwcnet.com\"},{\"name\":\"treemap\",\"value\":3865,\"url\":\"www.kwcnet.com\"},{\"name\":\"y\",\"value\":367,\"url\":\"www.kwcnet.com\"},{\"name\":\"valueIndex\",\"value\":704,\"url\":\"www.kwcnet.com\"},{\"name\":\"showLegendSymbol\",\"value\":482,\"url\":\"www.kwcnet.com\"},{\"name\":\"mapValueCalculation\",\"value\":492,\"url\":\"www.kwcnet.com\"},{\"name\":\"optionToContent\",\"value\":264,\"url\":\"www.kwcnet.com\"},{\"name\":\"handleColor\",\"value\":187,\"url\":\"www.kwcnet.com\"},{\"name\":\"handleSize\",\"value\":271,\"url\":\"www.kwcnet.com\"},{\"name\":\"showContent\",\"value\":1853,\"url\":\"www.kwcnet.com\"},{\"name\":\"angleAxisIndex\",\"value\":406,\"url\":\"www.kwcnet.com\"},{\"name\":\"endValue\",\"value\":327,\"url\":\"www.kwcnet.com\"},{\"name\":\"triggerOn\",\"value\":1720,\"url\":\"www.kwcnet.com\"},{\"name\":\"contentToOption\",\"value\":169,\"url\":\"www.kwcnet.com\"},{\"name\":\"buttonColor\",\"value\":71,\"url\":\"www.kwcnet.com\"},{\"name\":\"rotate\",\"value\":1144,\"url\":\"www.kwcnet.com\"},{\"name\":\"hoverLink\",\"value\":335,\"url\":\"www.kwcnet.com\"},{\"name\":\"outOfRange\",\"value\":491,\"url\":\"www.kwcnet.com\"},{\"name\":\"textareaColor\",\"value\":58,\"url\":\"www.kwcnet.com\"},{\"name\":\"textareaBorderColor\",\"value\":58,\"url\":\"www.kwcnet.com\"},{\"name\":\"textColor\",\"value\":60,\"url\":\"www.kwcnet.com\"},{\"name\":\"buttonTextColor\",\"value\":66,\"url\":\"www.kwcnet.com\"},{\"name\":\"category\",\"value\":336,\"url\":\"www.kwcnet.com\"},{\"name\":\"hideDelay\",\"value\":786,\"url\":\"www.kwcnet.com\"},{\"name\":\"alwaysShowContent\",\"value\":1267,\"url\":\"www.kwcnet.com\"},{\"name\":\"extraCssText\",\"value\":901,\"url\":\"www.kwcnet.com\"},{\"name\":\"effectType\",\"value\":277,\"url\":\"www.kwcnet.com\"},{\"name\":\"force\",\"value\":1820,\"url\":\"www.kwcnet.com\"},{\"name\":\"rippleEffect\",\"value\":723,\"url\":\"www.kwcnet.com\"},{\"name\":\"edgeSymbolSize\",\"value\":329,\"url\":\"www.kwcnet.com\"},{\"name\":\"showEffectOn\",\"value\":271,\"url\":\"www.kwcnet.com\"},{\"name\":\"gravity\",\"value\":199,\"url\":\"www.kwcnet.com\"},{\"name\":\"edgeLength\",\"value\":193,\"url\":\"www.kwcnet.com\"},{\"name\":\"layoutAnimation\",\"value\":152,\"url\":\"www.kwcnet.com\"},{\"name\":\"length2\",\"value\":169,\"url\":\"www.kwcnet.com\"},{\"name\":\"enterable\",\"value\":957,\"url\":\"www.kwcnet.com\"},{\"name\":\"dim\",\"value\":83,\"url\":\"www.kwcnet.com\"},{\"name\":\"readOnly\",\"value\":143,\"url\":\"www.kwcnet.com\"},{\"name\":\"levels\",\"value\":444,\"url\":\"www.kwcnet.com\"},{\"name\":\"textGap\",\"value\":256,\"url\":\"www.kwcnet.com\"},{\"name\":\"pixelRatio\",\"value\":84,\"url\":\"www.kwcnet.com\"},{\"name\":\"nodeScaleRatio\",\"value\":232,\"url\":\"www.kwcnet.com\"},{\"name\":\"draggable\",\"value\":249,\"url\":\"www.kwcnet.com\"},{\"name\":\"brushType\",\"value\":158,\"url\":\"www.kwcnet.com\"},{\"name\":\"radarIndex\",\"value\":152,\"url\":\"www.kwcnet.com\"},{\"name\":\"large\",\"value\":182,\"url\":\"www.kwcnet.com\"},{\"name\":\"edgeSymbol\",\"value\":675,\"url\":\"www.kwcnet.com\"},{\"name\":\"largeThreshold\",\"value\":132,\"url\":\"www.kwcnet.com\"},{\"name\":\"leafDepth\",\"value\":73,\"url\":\"www.kwcnet.com\"},{\"name\":\"childrenVisibleMin\",\"value\":73,\"url\":\"www.kwcnet.com\"},{\"name\":\"minSize\",\"value\":35,\"url\":\"www.kwcnet.com\"},{\"name\":\"maxSize\",\"value\":35,\"url\":\"www.kwcnet.com\"},{\"name\":\"sort\",\"value\":90,\"url\":\"www.kwcnet.com\"},{\"name\":\"funnelAlign\",\"value\":61,\"url\":\"www.kwcnet.com\"},{\"name\":\"source\",\"value\":336,\"url\":\"www.kwcnet.com\"},{\"name\":\"nodeClick\",\"value\":200,\"url\":\"www.kwcnet.com\"},{\"name\":\"curveness\",\"value\":350,\"url\":\"www.kwcnet.com\"},{\"name\":\"areaSelectStyle\",\"value\":104,\"url\":\"www.kwcnet.com\"},{\"name\":\"parallelIndex\",\"value\":52,\"url\":\"www.kwcnet.com\"},{\"name\":\"initLayout\",\"value\":359,\"url\":\"www.kwcnet.com\"},{\"name\":\"trailLength\",\"value\":116,\"url\":\"www.kwcnet.com\"},{\"name\":\"boxWidth\",\"value\":20,\"url\":\"www.kwcnet.com\"},{\"name\":\"back\",\"value\":53,\"url\":\"www.kwcnet.com\"},{\"name\":\"rewind\",\"value\":110,\"url\":\"www.kwcnet.com\"},{\"name\":\"zoomToNodeRatio\",\"value\":80,\"url\":\"www.kwcnet.com\"},{\"name\":\"squareRatio\",\"value\":60,\"url\":\"www.kwcnet.com\"},{\"name\":\"parallelAxisDefault\",\"value\":358,\"url\":\"www.kwcnet.com\"},{\"name\":\"checkpointStyle\",\"value\":440,\"url\":\"www.kwcnet.com\"},{\"name\":\"nodeWidth\",\"value\":49,\"url\":\"www.kwcnet.com\"},{\"name\":\"color0\",\"value\":62,\"url\":\"www.kwcnet.com\"},{\"name\":\"layoutIterations\",\"value\":56,\"url\":\"www.kwcnet.com\"},{\"name\":\"nodeGap\",\"value\":54,\"url\":\"www.kwcnet.com\"},{\"name\":\"color(Array\",\"value\":76,\"url\":\"www.kwcnet.com\"},{\"name\":\"<string>)\",\"value\":76,\"url\":\"www.kwcnet.com\"},{\"name\":\"repulsion\",\"value\":276,\"url\":\"www.kwcnet.com\"},{\"name\":\"tiled\",\"value\":105,\"url\":\"www.kwcnet.com\"},{\"name\":\"currentIndex\",\"value\":145,\"url\":\"www.kwcnet.com\"},{\"name\":\"axisType\",\"value\":227,\"url\":\"www.kwcnet.com\"},{\"name\":\"loop\",\"value\":97,\"url\":\"www.kwcnet.com\"},{\"name\":\"playInterval\",\"value\":112,\"url\":\"www.kwcnet.com\"},{\"name\":\"borderColor0\",\"value\":23,\"url\":\"www.kwcnet.com\"},{\"name\":\"gap\",\"value\":43,\"url\":\"www.kwcnet.com\"},{\"name\":\"autoPlay\",\"value\":123,\"url\":\"www.kwcnet.com\"},{\"name\":\"showPlayBtn\",\"value\":25,\"url\":\"www.kwcnet.com\"},{\"name\":\"breadcrumb\",\"value\":119,\"url\":\"www.kwcnet.com\"},{\"name\":\"colorMappingBy\",\"value\":85,\"url\":\"www.kwcnet.com\"},{\"name\":\"id\",\"value\":18,\"url\":\"www.kwcnet.com\"},{\"name\":\"blurSize\",\"value\":85,\"url\":\"www.kwcnet.com\"},{\"name\":\"minOpacity\",\"value\":50,\"url\":\"www.kwcnet.com\"},{\"name\":\"maxOpacity\",\"value\":54,\"url\":\"www.kwcnet.com\"},{\"name\":\"prevIcon\",\"value\":12,\"url\":\"www.kwcnet.com\"},{\"name\":\"children\",\"value\":21,\"url\":\"www.kwcnet.com\"},{\"name\":\"shape\",\"value\":98,\"url\":\"www.kwcnet.com\"},{\"name\":\"nextIcon\",\"value\":12,\"url\":\"www.kwcnet.com\"},{\"name\":\"showNextBtn\",\"value\":17,\"url\":\"www.kwcnet.com\"},{\"name\":\"stopIcon\",\"value\":21,\"url\":\"www.kwcnet.com\"},{\"name\":\"visibleMin\",\"value\":83,\"url\":\"www.kwcnet.com\"},{\"name\":\"visualDimension\",\"value\":97,\"url\":\"www.kwcnet.com\"},{\"name\":\"colorSaturation\",\"value\":56,\"url\":\"www.kwcnet.com\"},{\"name\":\"colorAlpha\",\"value\":66,\"url\":\"www.kwcnet.com\"},{\"name\":\"emptyItemWidth\",\"value\":10,\"url\":\"www.kwcnet.com\"},{\"name\":\"inactiveOpacity\",\"value\":4,\"url\":\"www.kwcnet.com\"},{\"name\":\"activeOpacity\",\"value\":4,\"url\":\"www.kwcnet.com\"},{\"name\":\"showPrevBtn\",\"value\":19,\"url\":\"www.kwcnet.com\"},{\"name\":\"playIcon\",\"value\":26,\"url\":\"www.kwcnet.com\"},{\"name\":\"ellipsis\",\"value\":19,\"url\":\"www.kwcnet.com\"},{\"name\":\"gapWidth\",\"value\":19,\"url\":\"www.kwcnet.com\"},{\"name\":\"borderColorSaturation\",\"value\":10,\"url\":\"www.kwcnet.com\"},{\"name\":\"handleIcon\",\"value\":2,\"url\":\"www.kwcnet.com\"},{\"name\":\"handleStyle\",\"value\":6,\"url\":\"www.kwcnet.com\"},{\"name\":\"borderType\",\"value\":1,\"url\":\"www.kwcnet.com\"},{\"name\":\"constantSpeed\",\"value\":1,\"url\":\"www.kwcnet.com\"},{\"name\":\"polyline\",\"value\":2,\"url\":\"www.kwcnet.com\"},{\"name\":\"blendMode\",\"value\":1,\"url\":\"www.kwcnet.com\"},{\"name\":\"dataBackground\",\"value\":1,\"url\":\"www.kwcnet.com\"},{\"name\":\"textAlign\",\"value\":1,\"url\":\"www.kwcnet.com\"},{\"name\":\"textBaseline\",\"value\":1,\"url\":\"www.kwcnet.com\"},{\"name\":\"brush\",\"value\":3,\"url\":\"www.kwcnet.com\"}]', 'ciyun');
INSERT INTO `up_chartdata` VALUES ('31', '[{\"value\":100,\"name\":\"数据处理\"},{\"value\":80,\"name\":\"数据分析\"},{\"value\":60,\"name\":\"基础架构\"},{\"value\":40,\"name\":\"数据可视化\"},{\"value\":20,\"name\":\"行业应用\"}]', 'loudoutu');
INSERT INTO `up_chartdata` VALUES ('32', '[{\"value\":0.45}]', 'shuiqiutu');
INSERT INTO `up_chartdata` VALUES ('33', '[{\"xName\":\"黄岛街道\",\"yName\":\"研究生教育\",\"x\":0,\"y\":0,\"value\":51},{\"xName\":\"辛安街道\",\"yName\":\"博士研究生毕业\",\"x\":0,\"y\":1,\"value\":44},{\"xName\":\"薛家岛街道\",\"yName\":\"硕士研究生毕业\",\"x\":0,\"y\":2,\"value\":30},{\"xName\":\"灵珠山街道\",\"yName\":\"大学本科教育\",\"x\":0,\"y\":3,\"value\":7},{\"xName\":\"长江路街道\",\"yName\":\"大学专科教育\",\"x\":0,\"y\":4,\"value\":36},{\"xName\":\"红石崖街道\",\"yName\":\"中等职业教育\",\"x\":0,\"y\":5,\"value\":30}]', 'relitu');
INSERT INTO `up_chartdata` VALUES ('34', '[{\"name\":2000,\"min\":1.06,\"Q1\":6.5175,\"median\":9.835,\"Q3\":14.5825,\"max\":26.68,\"series\":\"系列一\"},{\"name\":2001,\"min\":1.61,\"Q1\":2.8575,\"median\":7.805,\"Q3\":15.5625,\"max\":34.620,\"series\":\"系列一\"},{\"name\":2002,\"min\":1.94,\"Q1\":4.66,\"median\":11.315,\"Q3\":16.5125,\"max\":34.620,\"series\":\"系列一\"},{\"name\":2003,\"min\":3.39,\"Q1\":8.11,\"median\":11.84,\"Q3\":27.965,\"max\":57.73,\"series\":\"系列一\"},{\"name\":2004,\"min\":2.88,\"Q1\":7.47,\"median\":16.175,\"Q3\":32.18,\"max\":69.25,\"series\":\"系列一\"},{\"name\":2005,\"min\":4.111,\"Q1\":8.5975,\"median\":21.375,\"Q3\":40.3325,\"max\":87.9380,\"series\":\"系列一\"},{\"name\":2006,\"min\":3.61,\"Q1\":4.8575,\"median\":7.805,\"Q3\":15.5625,\"max\":34.620,\"series\":\"系列一\"}]', 'xiangxingtu');
INSERT INTO `up_chartdata` VALUES ('35', '[{\"type\":\"heatmap\",\"name\":\"安徽\",\"lng\":121,\"lat\":31.86119,\"lng2\":116.283042,\"lat2\":39.608266,\"value\":300},{\"type\":\"heatmap\",\"name\":\"北京\",\"lng\":120.38,\"lat\":37.35,\"lng2\":122.207216,\"lat2\":29.608266,\"value\":30},{\"type\":\"heatmap\",\"name\":\"福建\",\"lng\":118.306239,\"lat\":26.058039,\"lng2\":122.207216,\"lat2\":29.608266,\"value\":120},{\"type\":\"heatmap\",\"name\":\"甘肃\",\"lng\":102.823557,\"lat\":36.058039,\"lng2\":122.207216,\"lat2\":29.608266,\"value\":138},{\"type\":\"heatmap\",\"name\":\"广东\",\"lng\":112.280637,\"lat\":23.82402,\"lng2\":122.207216,\"lat2\":29.608266,\"value\":321},{\"type\":\"heatmap\",\"name\":\"广西\",\"lng\":108.320004,\"lat\":22.82402,\"lng2\":122.207216,\"lat2\":29.608266,\"value\":321},{\"type\":\"heatmap\",\"name\":\"贵州\",\"lng\":105.713478,\"lat\":26.578343,\"lng2\":112.280637,\"lat2\":23.82402,\"value\":239},{\"type\":\"heatmap\",\"name\":\"海南\",\"lng\":109.33119,\"lat\":20.031971,\"lng2\":105.713478,\"lat2\":26.578343,\"value\":88},{\"type\":\"effectScatter\",\"name\":\"河北\",\"lng\":115.502461,\"lat\":38.045474,\"lng2\":105.713478,\"lat2\":26.578343,\"value\":289},{\"type\":\"effectScatter\",\"name\":\"河南\",\"lng\":114.665412,\"lat\":34.757975,\"lng2\":115.502461,\"lat2\":38.045474,\"value\":89},{\"type\":\"effectScatter\",\"name\":\"黑龙江\",\"lng\":125.642464,\"lat\":45.756967,\"lng2\":115.502461,\"lat2\":38.045474,\"value\":249},{\"type\":\"effectScatter\",\"name\":\"湖北\",\"lng\":113.298572,\"lat\":30.584355,\"lng2\":115.502461,\"lat2\":38.045474,\"value\":65},{\"type\":\"effectScatter\",\"name\":\"湖南\",\"lng\":111.982279,\"lat\":28.19409,\"lng2\":113.298572,\"lat2\":30.584355,\"value\":213},{\"type\":\"effectScatter\",\"name\":\"吉林\",\"lng\":124.3245,\"lat\":43.886841,\"lng2\":113.298572,\"lat2\":30.584355,\"value\":201},{\"type\":\"effectScatter\",\"name\":\"江苏\",\"lng\":117.767413,\"lat\":32.041544,\"lng2\":124.3245,\"lat2\":43.886841,\"value\":155},{\"type\":\"effectScatter\",\"name\":\"江西\",\"lng\":115.892151,\"lat\":26.676493,\"lng2\":124.3245,\"lat2\":43.886841,\"value\":129},{\"type\":\"scatter\",\"name\":\"辽宁\",\"lng\":123.429096,\"lat\":40.796767,\"lng2\":124.3245,\"lat2\":43.886841,\"value\":120},{\"type\":\"scatter\",\"name\":\"内蒙古\",\"lng\":110.670801,\"lat\":40.818311,\"lng2\":123.429096,\"lat2\":40.796767,\"value\":181},{\"type\":\"scatter\",\"name\":\"宁夏\",\"lng\":107.278179,\"lat\":38.46637,\"lng2\":123.429096,\"lat2\":40.796767,\"value\":287},{\"type\":\"scatter\",\"name\":\"青海\",\"lng\":102.778916,\"lat\":36.623178,\"lng2\":123.429096,\"lat2\":40.796767,\"value\":278},{\"type\":\"scatter\",\"name\":\"山东\",\"lng\":116.000923,\"lat\":36.675807,\"lng2\":123.429096,\"lat2\":40.796767,\"value\":109},{\"type\":\"scatter\",\"name\":\"山西\",\"lng\":112.549248,\"lat\":37.857014,\"lng2\":123.429096,\"lat2\":40.796767,\"value\":233},{\"type\":\"scatter\",\"name\":\"陕西\",\"lng\":107.948024,\"lat\":34.263161,\"lng2\":112.549248,\"lat2\":37.857014,\"value\":388},{\"type\":\"scatter\",\"name\":\"上海\",\"lng\":121.472644,\"lat\":31.231706,\"lng2\":112.549248,\"lat2\":37.857014,\"value\":400},{\"type\":\"lines\",\"name\":\"四川\",\"lng\":105.065735,\"lat\":30.659462,\"lng2\":112.549248,\"lat2\":37.857014,\"value\":324},{\"type\":\"lines\",\"name\":\"天津\",\"lng\":118.190182,\"lat\":39.125596,\"lng2\":112.549248,\"lat2\":37.857014,\"value\":255},{\"type\":\"lines\",\"name\":\"西藏\",\"lng\":90.132212,\"lat\":29.660361,\"lng2\":118.190182,\"lat2\":39.125596,\"value\":362},{\"type\":\"lines\",\"name\":\"新疆\",\"lng\":88.617733,\"lat\":43.792818,\"lng2\":118.190182,\"lat2\":39.125596,\"value\":123},{\"type\":\"lines\",\"name\":\"云南\",\"lng\":101.712251,\"lat\":25.040609,\"lng2\":118.190182,\"lat2\":39.125596,\"value\":311},{\"type\":\"lines\",\"name\":\"浙江\",\"lng\":120.153576,\"lat\":30.287459,\"lng2\":118.190182,\"lat2\":39.125596,\"value\":233},{\"type\":\"lines\",\"name\":\"重庆\",\"lng\":106.504962,\"lat\":28.533155,\"lng2\":120.153576,\"lat2\":30.287459,\"value\":279}]', 'zhongguoditu');
INSERT INTO `up_chartdata` VALUES ('36', '[]', 'biaoti');
INSERT INTO `up_chartdata` VALUES ('37', '[]', 'area');
INSERT INTO `up_chartdata` VALUES ('38', '[]', 'shijian');
INSERT INTO `up_chartdata` VALUES ('39', '[{\"iframeUrl\":\"http://www.openvticn.com/\"}]', 'wangye');
INSERT INTO `up_chartdata` VALUES ('40', '[{\"desc\":\"这是第一张图片的描述\",\"imgurl\":\"http://www.kwcnet.com/uploadfile/2018/0611/20180611045949541.png\"},{\"desc\":\"这是第二张图片的描述\",\"imgurl\":\"http://www.kwcnet.com/uploadfile/2018/0611/20180611050208218.png\"},{\"desc\":\"这是第三张图片的描述\",\"imgurl\":\"http://www.kwcnet.com/uploadfile/2018/0611/20180611050109882.png\"}]', 'lunbotu');
INSERT INTO `up_chartdata` VALUES ('41', '[]', '[]');
INSERT INTO `up_chartdata` VALUES ('42', '[{\"richtext\":\"<div style=\\\"text-align:left;\\\"><span style=\\\"color:#61BD6D;font-family:-apple-system, system-ui, &quot;font-size:14px;white-space:normal;background-color:#FFFFFF;\\\">这是富文本</span><br /></div>\"}]', 'fuwenben');
INSERT INTO `up_chartdata` VALUES ('43', '[]', 'shipinbofangqi');
INSERT INTO `up_chartdata` VALUES ('44', '[{\"coutnum\":\"5\"}]', 'jishuban');
INSERT INTO `up_chartdata` VALUES ('45', '[{\"text\":\"北京开维创科技有限公司是一家集系统集成、维保（续保）等于一体的专业化IT服务商。公司拥有全系列思科产品线和庞大的全国备件库网络，并运用完善、高效的服务技术体系来满足不同地区不同客户的各种需求。公司业务涉及网络基础设施建设咨询、设计、设备支持（设备提供、调试）等专业化系统维护服务，并且长期向思科的各级代理商，IDC机房数据中心，系统集成商及各中小企业网络机房提供最好的备件服务。\"}]', 'duoxingwenben');
INSERT INTO `up_chartdata` VALUES ('46', '[{\"divertext\":\"北京开维创科技有限公司是一家集系统集成、维保（续保）等于一体的专业化IT服务商。\"}]', 'paomadeng');
INSERT INTO `up_chartdata` VALUES ('47', '[]', 'chaolianjie');
INSERT INTO `up_chartdata` VALUES ('48', '[{\"prognum\":\"20\"}]', 'jindutiao');
INSERT INTO `up_chartdata` VALUES ('49', '[{\"label\":\"company\",\"data\":\"Electrical Systems\",\"row\":1},{\"label\":\"name\",\"data\":\"Ted\",\"row\":1},{\"label\":\"surname\",\"data\":\"Smith\",\"row\":1},{\"label\":\"age\",\"data\":30,\"row\":1},{\"label\":\"email\",\"data\":\"ted.smith@gmail.com\",\"row\":1},{\"label\":\"company\",\"data\":\"Energy and Oil\",\"row\":2},{\"label\":\"name\",\"data\":\"Ed\",\"row\":2},{\"label\":\"surname\",\"data\":\"Johnson\",\"row\":2},{\"label\":\"age\",\"data\":35,\"row\":2},{\"label\":\"email\",\"data\":\"ed.johnson@gmail.com\",\"row\":2},{\"label\":\"company\",\"data\":\"Airbus\",\"row\":3},{\"label\":\"name\",\"data\":\"Sam\",\"row\":3},{\"label\":\"surname\",\"data\":\"Williams\",\"row\":3},{\"label\":\"age\",\"data\":38,\"row\":3},{\"label\":\"email\",\"data\":\"sam.williams@gmail.com\",\"row\":3},{\"label\":\"company\",\"data\":\"Renault\",\"row\":4},{\"label\":\"name\",\"data\":\"Alexander\",\"row\":4},{\"label\":\"surname\",\"data\":\"Brown\",\"row\":4},{\"label\":\"age\",\"data\":24,\"row\":4},{\"label\":\"email\",\"data\":\"alexander.brown@gmail.com\",\"row\":4}]', 'biaoge');
INSERT INTO `up_chartdata` VALUES ('50', '[{\"prognum\":\"50\"}]', 'huanxingjindutiao');
INSERT INTO `up_chartdata` VALUES ('51', '[{\"type\":\"scatter3D\",\"name\":\"安徽\",\"lng\":121,\"lat\":31.86119,\"lng2\":116.283042,\"lat2\":39.608266,\"value\":300},{\"type\":\"scatter3D\",\"name\":\"北京\",\"lng\":120.38,\"lat\":37.35,\"lng2\":122.207216,\"lat2\":29.608266,\"value\":30},{\"type\":\"scatter3D\",\"name\":\"福建\",\"lng\":118.306239,\"lat\":26.058039,\"lng2\":122.207216,\"lat2\":29.608266,\"value\":120},{\"type\":\"scatter3D\",\"name\":\"甘肃\",\"lng\":102.823557,\"lat\":36.058039,\"lng2\":122.207216,\"lat2\":29.608266,\"value\":138},{\"type\":\"scatter3D\",\"name\":\"广东\",\"lng\":112.280637,\"lat\":23.82402,\"lng2\":122.207216,\"lat2\":29.608266,\"value\":321},{\"type\":\"scatter3D\",\"name\":\"广西\",\"lng\":108.320004,\"lat\":22.82402,\"lng2\":122.207216,\"lat2\":29.608266,\"value\":321},{\"type\":\"scatter3D\",\"name\":\"贵州\",\"lng\":105.713478,\"lat\":26.578343,\"lng2\":112.280637,\"lat2\":23.82402,\"value\":239},{\"type\":\"scatter3D\",\"name\":\"海南\",\"lng\":109.33119,\"lat\":20.031971,\"lng2\":105.713478,\"lat2\":26.578343,\"value\":88},{\"type\":\"bar3D\",\"name\":\"河北\",\"lng\":115.502461,\"lat\":38.045474,\"lng2\":105.713478,\"lat2\":26.578343,\"value\":289},{\"type\":\"bar3D\",\"name\":\"河南\",\"lng\":114.665412,\"lat\":34.757975,\"lng2\":115.502461,\"lat2\":38.045474,\"value\":89},{\"type\":\"bar3D\",\"name\":\"黑龙江\",\"lng\":125.642464,\"lat\":45.756967,\"lng2\":115.502461,\"lat2\":38.045474,\"value\":249},{\"type\":\"bar3D\",\"name\":\"湖北\",\"lng\":113.298572,\"lat\":30.584355,\"lng2\":115.502461,\"lat2\":38.045474,\"value\":65},{\"type\":\"bar3D\",\"name\":\"湖南\",\"lng\":111.982279,\"lat\":28.19409,\"lng2\":113.298572,\"lat2\":30.584355,\"value\":213},{\"type\":\"bar3D\",\"name\":\"吉林\",\"lng\":124.3245,\"lat\":43.886841,\"lng2\":113.298572,\"lat2\":30.584355,\"value\":201},{\"type\":\"bar3D\",\"name\":\"江苏\",\"lng\":117.767413,\"lat\":32.041544,\"lng2\":124.3245,\"lat2\":43.886841,\"value\":155},{\"type\":\"lines3D\",\"name\":\"江西\",\"lng\":115.892151,\"lat\":26.676493,\"lng2\":124.3245,\"lat2\":43.886841,\"value\":129},{\"type\":\"lines3D\",\"name\":\"辽宁\",\"lng\":123.429096,\"lat\":40.796767,\"lng2\":124.3245,\"lat2\":43.886841,\"value\":120},{\"type\":\"lines3D\",\"name\":\"内蒙古\",\"lng\":110.670801,\"lat\":40.818311,\"lng2\":123.429096,\"lat2\":40.796767,\"value\":181},{\"type\":\"lines3D\",\"name\":\"宁夏\",\"lng\":107.278179,\"lat\":38.46637,\"lng2\":123.429096,\"lat2\":40.796767,\"value\":287},{\"type\":\"lines3D\",\"name\":\"青海\",\"lng\":102.778916,\"lat\":36.623178,\"lng2\":123.429096,\"lat2\":40.796767,\"value\":278},{\"type\":\"lines3D\",\"name\":\"山东\",\"lng\":116.000923,\"lat\":36.675807,\"lng2\":123.429096,\"lat2\":40.796767,\"value\":109},{\"type\":\"lines3D\",\"name\":\"山西\",\"lng\":112.549248,\"lat\":37.857014,\"lng2\":123.429096,\"lat2\":40.796767,\"value\":233},{\"type\":\"lines3D\",\"name\":\"陕西\",\"lng\":107.948024,\"lat\":34.263161,\"lng2\":112.549248,\"lat2\":37.857014,\"value\":388},{\"type\":\"lines3D\",\"name\":\"上海\",\"lng\":121.472644,\"lat\":31.231706,\"lng2\":112.549248,\"lat2\":37.857014,\"value\":400},{\"type\":\"lines3D\",\"name\":\"四川\",\"lng\":105.065735,\"lat\":30.659462,\"lng2\":112.549248,\"lat2\":37.857014,\"value\":324},{\"type\":\"lines3D\",\"name\":\"天津\",\"lng\":118.190182,\"lat\":39.125596,\"lng2\":112.549248,\"lat2\":37.857014,\"value\":255},{\"type\":\"lines3D\",\"name\":\"西藏\",\"lng\":90.132212,\"lat\":29.660361,\"lng2\":118.190182,\"lat2\":39.125596,\"value\":362},{\"type\":\"lines3D\",\"name\":\"新疆\",\"lng\":88.617733,\"lat\":43.792818,\"lng2\":118.190182,\"lat2\":39.125596,\"value\":123},{\"type\":\"lines3D\",\"name\":\"云南\",\"lng\":101.712251,\"lat\":25.040609,\"lng2\":118.190182,\"lat2\":39.125596,\"value\":311},{\"type\":\"lines3D\",\"name\":\"浙江\",\"lng\":120.153576,\"lat\":30.287459,\"lng2\":118.190182,\"lat2\":39.125596,\"value\":233},{\"type\":\"lines3D\",\"name\":\"重庆\",\"lng\":106.504962,\"lat\":28.533155,\"lng2\":120.153576,\"lat2\":30.287459,\"value\":279}]', '3dzhongguoditu');
INSERT INTO `up_chartdata` VALUES ('52', '[{\"label\":\"\",\"data\":\"Electrical Systems\",\"row\":1},{\"label\":\"name\",\"data\":\"Ted\",\"row\":1},{\"label\":\"surname\",\"data\":\"Smith\",\"row\":1},{\"label\":\"age\",\"data\":30,\"row\":1},{\"label\":\"email\",\"data\":\"ted.smith@gmail.com\",\"row\":1},{\"label\":\"company\",\"data\":\"Energy and Oil\",\"row\":2},{\"label\":\"name\",\"data\":\"Ed\",\"row\":2},{\"label\":\"surname\",\"data\":\"Johnson\",\"row\":2},{\"label\":\"age\",\"data\":35,\"row\":2},{\"label\":\"email\",\"data\":\"ed.johnson@gmail.com\",\"row\":2},{\"label\":\"company\",\"data\":\"Airbus\",\"row\":3},{\"label\":\"name\",\"data\":\"Sam\",\"row\":3},{\"label\":\"surname\",\"data\":\"Williams\",\"row\":3},{\"label\":\"age\",\"data\":38,\"row\":3},{\"label\":\"email\",\"data\":\"sam.williams@gmail.com\",\"row\":3},{\"label\":\"company\",\"data\":\"Renault\",\"row\":4},{\"label\":\"name\",\"data\":\"Alexander\",\"row\":4},{\"label\":\"surname\",\"data\":\"Brown\",\"row\":4},{\"label\":\"age\",\"data\":24,\"row\":4},{\"label\":\"email\",\"data\":\"alexander.brown@gmail.com\",\"row\":4}]', 'lunbobiaoge');
INSERT INTO `up_chartdata` VALUES ('53', '[{\"x\":1,\"y\":1,\"z\":1,\"series\":\"系列二\"},{\"x\":2,\"y\":2,\"z\":2,\"series\":\"系列二\"},{\"x\":3,\"y\":3,\"z\":3,\"series\":\"系列二\"},{\"x\":4,\"y\":4,\"z\":5,\"series\":\"系列二\"},{\"x\":5,\"y\":5,\"z\":5,\"series\":\"系列二\"},{\"x\":6,\"y\":8,\"z\":8,\"series\":\"系列一\"},{\"x\":7,\"y\":4,\"z\":2,\"series\":\"系列一\"},{\"x\":8,\"y\":3,\"z\":5,\"series\":\"系列一\"},{\"x\":9,\"y\":8,\"z\":8,\"series\":\"系列一\"},{\"x\":10,\"y\":15,\"z\":3,\"series\":\"系列一\"},{\"x\":11,\"y\":2,\"z\":1,\"series\":\"系列一\"},{\"x\":12,\"y\":12,\"z\":4,\"series\":\"系列一\"}]', 'sandianzhexiantu');
INSERT INTO `up_chartdata` VALUES ('54', '[{\"x\":1,\"y\":1,\"z\":1,\"r\":10,\"series\":\"系列二\"},{\"x\":2,\"y\":2,\"z\":2,\"r\":10,\"series\":\"系列二\"},{\"x\":3,\"y\":3,\"z\":3,\"r\":10,\"series\":\"系列二\"},{\"x\":4,\"y\":4,\"z\":5,\"r\":10,\"series\":\"系列二\"},{\"x\":5,\"y\":5,\"z\":5,\"r\":10,\"series\":\"系列二\"},{\"x\":6,\"y\":8,\"z\":8,\"r\":10,\"series\":\"系列一\"},{\"x\":7,\"y\":4,\"z\":2,\"r\":10,\"series\":\"系列一\"},{\"x\":8,\"y\":3,\"z\":5,\"r\":10,\"series\":\"系列一\"},{\"x\":9,\"y\":8,\"z\":8,\"r\":10,\"series\":\"系列一\"},{\"x\":10,\"y\":15,\"z\":3,\"r\":10,\"series\":\"系列一\"},{\"x\":11,\"y\":2,\"z\":1,\"r\":10,\"series\":\"系列一\"},{\"x\":12,\"y\":12,\"z\":4,\"r\":10,\"series\":\"系列一\"},{\"x\":13,\"y\":9,\"z\":5,\"r\":10,\"series\":\"系列一\"}]', 'qipaozhexiantu');
INSERT INTO `up_chartdata` VALUES ('55', '[{\"name\":\"A\",\"barval\":11,\"lineval\":111},{\"name\":\"B\",\"barval\":12,\"lineval\":112},{\"name\":\"C\",\"barval\":13,\"lineval\":113},{\"name\":\"D\",\"barval\":14,\"lineval\":114},{\"name\":\"E\",\"barval\":15,\"lineval\":115},{\"name\":\"F\",\"barval\":16,\"lineval\":116},{\"name\":\"G\",\"barval\":17,\"lineval\":117},{\"name\":\"H\",\"barval\":18,\"lineval\":118},{\"name\":\"I\",\"barval\":19,\"lineval\":119}]', 'danzhouzhexianzhutu');
INSERT INTO `up_chartdata` VALUES ('56', '[{\"name\":\"三级标题\",\"value\":3,\"pid\":3,\"cid\":4},{\"name\":\"二级标题\",\"value\":2,\"pid\":2,\"cid\":3},{\"name\":\"一级标题\",\"value\":6,\"pid\":1,\"cid\":2},{\"name\":\"顶级标题\",\"value\":10,\"pid\":0,\"cid\":1},{\"name\":\"一级标题\",\"value\":4,\"pid\":1,\"cid\":5},{\"name\":\"顶级标题2\",\"value\":20,\"pid\":0,\"cid\":6},{\"name\":\"一级标题2\",\"value\":11,\"pid\":6,\"cid\":7}]', 'juxingshutu');
INSERT INTO `up_chartdata` VALUES ('57', '[{\"type\":\"lines\",\"name\":\"尼日利亚\",\"lng\":-4.388361,\"lat\":11.186148,\"lng2\":121.4648,\"lat2\":31.2891,\"value\":271},{\"type\":\"lines\",\"name\":\"美国洛杉矶\",\"lng\":-118.24311,\"lat\":34.052713,\"lng2\":121.4648,\"lat2\":31.2891,\"value\":45},{\"type\":\"lines\",\"name\":\"香港邦泰\",\"lng\":114.195466,\"lat\":22.282751,\"lng2\":121.4648,\"lat2\":31.2891,\"value\":271},{\"type\":\"lines1\",\"name\":\"上海\",\"lng\":121.4648,\"lat\":31.2891,\"lng2\":-4.388361,\"lat2\":11.186148,\"value\":271},{\"type\":\"lines1\",\"name\":\"上海\",\"lng2\":-118.24311,\"lat2\":34.052713,\"lng\":121.4648,\"lat\":31.2891,\"value\":45},{\"type\":\"lines1\",\"name\":\"上海\",\"lng2\":114.195466,\"lat2\":22.282751,\"lng1\":121.4648,\"lat1\":31.2891,\"value\":271},{\"type\":\"heatmap\",\"name\":\"尼日利亚\",\"lng\":-4.388361,\"lat\":11.186148,\"value\":271},{\"type\":\"heatmap\",\"name\":\"美国洛杉矶\",\"lng\":-118.24311,\"lat\":34.052713,\"value\":40},{\"type\":\"heatmap\",\"name\":\"香港邦泰\",\"lng\":114.195466,\"lat\":22.282751,\"value\":120},{\"type\":\"effectScatter\",\"name\":\"尼日利亚\",\"lng\":-4.388361,\"lat\":11.186148,\"value\":271},{\"type\":\"effectScatter\",\"name\":\"美国洛杉矶\",\"lng\":-118.24311,\"lat\":34.052713,\"value\":40},{\"type\":\"effectScatter\",\"name\":\"香港邦泰\",\"lng\":114.195466,\"lat\":22.282751,\"value\":120},{\"type\":\"scatter\",\"name\":\"尼日利亚\",\"lng\":-4.388361,\"lat\":11.186148,\"value\":271},{\"type\":\"scatter\",\"name\":\"美国洛杉矶\",\"lng\":-118.24311,\"lat\":34.052713,\"value\":40},{\"type\":\"scatter\",\"name\":\"香港邦泰\",\"lng\":114.195466,\"lat\":22.282751,\"value\":120}]', 'shijieditu');
INSERT INTO `up_chartdata` VALUES ('58', '[{\"type\":\"lines3D\",\"name\":\"尼日利亚\",\"lng\":-4.388361,\"lat\":11.186148,\"altid\":3,\"lng2\":121.4648,\"lat2\":31.2891,\"altid2\":3,\"value\":271},{\"type\":\"lines3D\",\"name\":\"美国洛杉矶\",\"lng\":-118.24311,\"lat\":34.052713,\"altid\":3,\"lng2\":121.4648,\"lat2\":31.2891,\"altid2\":1,\"value\":45},{\"type\":\"lines3D\",\"name\":\"香港邦泰\",\"lng\":114.195466,\"lat\":22.282751,\"altid\":2,\"lng2\":121.4648,\"lat2\":31.2891,\"altid2\":2,\"value\":271},{\"type\":\"lines3D\",\"name\":\"上海\",\"lng\":121.4648,\"lat\":31.2891,\"altid\":1,\"lng2\":-4.388361,\"lat2\":11.186148,\"altid2\":1,\"value\":271},{\"type\":\"lines3D\",\"name\":\"上海\",\"lng2\":-118.24311,\"lat2\":34.052713,\"lng\":121.4648,\"lat\":31.2891,\"value\":45},{\"type\":\"lines3D\",\"name\":\"上海\",\"lng2\":114.195466,\"lat2\":22.282751,\"altid\":1,\"lng1\":121.4648,\"lat1\":31.2891,\"altid2\":2,\"value\":271},{\"type\":\"scatter3D\",\"name\":\"尼日利亚\",\"lng\":-4.388361,\"lat\":11.186148,\"altid\":1,\"altid2\":2,\"value\":271},{\"type\":\"scatter3D\",\"name\":\"美国洛杉矶\",\"lng\":-118.24311,\"lat\":34.052713,\"altid\":1,\"altid2\":2,\"value\":40},{\"type\":\"scatter3D\",\"name\":\"香港邦泰\",\"lng\":114.195466,\"lat\":22.282751,\"altid\":1,\"altid2\":2,\"value\":120},{\"type\":\"bar3D\",\"name\":\"尼日利亚\",\"lng\":-4.388361,\"lat\":11.186148,\"altid\":1,\"altid2\":2,\"value\":271},{\"type\":\"bar3D\",\"name\":\"美国洛杉矶\",\"lng\":-118.24311,\"altid\":1,\"altid2\":2,\"lat\":34.052713,\"value\":40},{\"type\":\"bar3D\",\"name\":\"香港邦泰\",\"lng\":114.195466,\"lat\":22.282751,\"altid\":1,\"altid2\":2,\"value\":120}]', 'diqiuyi');
INSERT INTO `up_chartdata` VALUES ('59', '[{\"type\":\"heatmap\",\"name\":\"安徽\",\"lng\":121,\"lat\":31.86119,\"lng2\":116.283042,\"lat2\":39.608266,\"value\":300},{\"type\":\"heatmap\",\"name\":\"北京\",\"lng\":120.38,\"lat\":37.35,\"lng2\":122.207216,\"lat2\":29.608266,\"value\":30},{\"type\":\"heatmap\",\"name\":\"福建\",\"lng\":118.306239,\"lat\":26.058039,\"lng2\":122.207216,\"lat2\":29.608266,\"value\":120},{\"type\":\"heatmap\",\"name\":\"甘肃\",\"lng\":102.823557,\"lat\":36.058039,\"lng2\":122.207216,\"lat2\":29.608266,\"value\":138},{\"type\":\"heatmap\",\"name\":\"广东\",\"lng\":112.280637,\"lat\":23.82402,\"lng2\":122.207216,\"lat2\":29.608266,\"value\":321},{\"type\":\"heatmap\",\"name\":\"广西\",\"lng\":108.320004,\"lat\":22.82402,\"lng2\":122.207216,\"lat2\":29.608266,\"value\":321},{\"type\":\"heatmap\",\"name\":\"贵州\",\"lng\":105.713478,\"lat\":26.578343,\"lng2\":112.280637,\"lat2\":23.82402,\"value\":239},{\"type\":\"heatmap\",\"name\":\"海南\",\"lng\":109.33119,\"lat\":20.031971,\"lng2\":105.713478,\"lat2\":26.578343,\"value\":88},{\"type\":\"effectScatter\",\"name\":\"河北\",\"lng\":115.502461,\"lat\":38.045474,\"lng2\":105.713478,\"lat2\":26.578343,\"value\":289},{\"type\":\"effectScatter\",\"name\":\"河南\",\"lng\":114.665412,\"lat\":34.757975,\"lng2\":115.502461,\"lat2\":38.045474,\"value\":89},{\"type\":\"effectScatter\",\"name\":\"黑龙江\",\"lng\":125.642464,\"lat\":45.756967,\"lng2\":115.502461,\"lat2\":38.045474,\"value\":249},{\"type\":\"effectScatter\",\"name\":\"湖北\",\"lng\":113.298572,\"lat\":30.584355,\"lng2\":115.502461,\"lat2\":38.045474,\"value\":65},{\"type\":\"effectScatter\",\"name\":\"湖南\",\"lng\":111.982279,\"lat\":28.19409,\"lng2\":113.298572,\"lat2\":30.584355,\"value\":213},{\"type\":\"effectScatter\",\"name\":\"吉林\",\"lng\":124.3245,\"lat\":43.886841,\"lng2\":113.298572,\"lat2\":30.584355,\"value\":201},{\"type\":\"effectScatter\",\"name\":\"江苏\",\"lng\":117.767413,\"lat\":32.041544,\"lng2\":124.3245,\"lat2\":43.886841,\"value\":155},{\"type\":\"effectScatter\",\"name\":\"江西\",\"lng\":115.892151,\"lat\":26.676493,\"lng2\":124.3245,\"lat2\":43.886841,\"value\":129},{\"type\":\"scatter\",\"name\":\"辽宁\",\"lng\":123.429096,\"lat\":40.796767,\"lng2\":124.3245,\"lat2\":43.886841,\"value\":120},{\"type\":\"scatter\",\"name\":\"内蒙古\",\"lng\":110.670801,\"lat\":40.818311,\"lng2\":123.429096,\"lat2\":40.796767,\"value\":181},{\"type\":\"scatter\",\"name\":\"宁夏\",\"lng\":107.278179,\"lat\":38.46637,\"lng2\":123.429096,\"lat2\":40.796767,\"value\":287},{\"type\":\"scatter\",\"name\":\"青海\",\"lng\":102.778916,\"lat\":36.623178,\"lng2\":123.429096,\"lat2\":40.796767,\"value\":278},{\"type\":\"scatter\",\"name\":\"山东\",\"lng\":116.000923,\"lat\":36.675807,\"lng2\":123.429096,\"lat2\":40.796767,\"value\":109},{\"type\":\"scatter\",\"name\":\"山西\",\"lng\":112.549248,\"lat\":37.857014,\"lng2\":123.429096,\"lat2\":40.796767,\"value\":233},{\"type\":\"scatter\",\"name\":\"陕西\",\"lng\":107.948024,\"lat\":34.263161,\"lng2\":112.549248,\"lat2\":37.857014,\"value\":388},{\"type\":\"scatter\",\"name\":\"上海\",\"lng\":121.472644,\"lat\":31.231706,\"lng2\":112.549248,\"lat2\":37.857014,\"value\":400},{\"type\":\"lines\",\"name\":\"四川\",\"lng\":105.065735,\"lat\":30.659462,\"lng2\":112.549248,\"lat2\":37.857014,\"value\":324},{\"type\":\"lines\",\"name\":\"天津\",\"lng\":118.190182,\"lat\":39.125596,\"lng2\":112.549248,\"lat2\":37.857014,\"value\":255},{\"type\":\"lines\",\"name\":\"西藏\",\"lng\":90.132212,\"lat\":29.660361,\"lng2\":118.190182,\"lat2\":39.125596,\"value\":362},{\"type\":\"lines\",\"name\":\"新疆\",\"lng\":88.617733,\"lat\":43.792818,\"lng2\":118.190182,\"lat2\":39.125596,\"value\":123},{\"type\":\"lines\",\"name\":\"云南\",\"lng\":101.712251,\"lat\":25.040609,\"lng2\":118.190182,\"lat2\":39.125596,\"value\":311},{\"type\":\"lines\",\"name\":\"浙江\",\"lng\":120.153576,\"lat\":30.287459,\"lng2\":118.190182,\"lat2\":39.125596,\"value\":233},{\"type\":\"lines\",\"name\":\"重庆\",\"lng\":106.504962,\"lat\":28.533155,\"lng2\":120.153576,\"lat2\":30.287459,\"value\":279}]', 'gismap');
INSERT INTO `up_chartdata` VALUES ('60', '[{\"type\":\"bar3D\",\"name\":\"尼日利亚\",\"lng\":-4.388361,\"lat\":11.186148,\"alt\":100,\"value\":271},{\"type\":\"bar3D\",\"name\":\"美国洛杉矶\",\"lng\":-118.24311,\"lat\":34.052713,\"alt\":100,\"value\":40},{\"type\":\"bar3D\",\"name\":\"香港邦泰\",\"lng\":114.195466,\"lat\":22.282751,\"alt\":100,\"value\":120},{\"type\":\"scatter3D\",\"name\":\"尼日利亚\",\"lng\":-4.388361,\"lat\":11.186148,\"alt\":100,\"value\":271},{\"type\":\"scatter3D\",\"name\":\"美国洛杉矶\",\"lng\":-118.24311,\"lat\":34.052713,\"alt\":100,\"value\":40},{\"type\":\"scatter3D\",\"name\":\"香港邦泰\",\"lng\":114.195466,\"lat\":22.282751,\"alt\":100,\"value\":120},{\"type\":\"lines3D\",\"name\":\"尼日利亚\",\"lng\":-4.388361,\"lat\":11.186148,\"lng2\":121.4648,\"lat2\":31.2891,\"value\":271,\"alt\":271,\"alt2\":45},{\"type\":\"lines3D\",\"name\":\"美国洛杉矶\",\"lng\":-118.24311,\"lat\":34.052713,\"lng2\":121.4648,\"lat2\":31.2891,\"value\":45,\"alt\":100,\"alt2\":145}]', '3dshijieditu');
INSERT INTO `up_chartdata` VALUES ('61', '[{\"xname\":\"新增收入\",\"yname\":\"星期一\",\"value\":5},{\"xname\":\"新增建档\",\"yname\":\"星期二\",\"value\":15},{\"xname\":\"打卡人数\",\"yname\":\"星期三\",\"value\":10},{\"xname\":\"扫楼数\",\"yname\":\"星期四\",\"value\":8}]', '3dzhuzhuangtu');
INSERT INTO `up_chartdata` VALUES ('62', '[{\"name\":\"Total\",\"source\":\"Total\",\"target\":\"Environment\",\"value\":0.342284047256003},{\"name\":\"Environment\",\"source\":\"Environment\",\"target\":\"Land use\",\"value\":0.32322870366987},{\"name\":\"Land use\",\"source\":\"Land use\",\"target\":\"Cocoa butter\",\"value\":0.177682517071359},{\"name\":\"Cocoa butter (Organic)\",\"source\":\"Land use\",\"target\":\"Cocoa mass (Organic)\",\"value\":0.137241325342711},{\"name\":\"Cocoa mass (Organic)\",\"source\":\"Land use\",\"target\":\"Hazelnuts (Organic)\",\"value\":0.00433076373512774},{\"name\":\"Hazelnuts (Organic)\",\"source\":\"Land use\",\"target\":\"Cane sugar (Organic)\",\"value\":0.00296956039863467},{\"name\":\"Cane sugar (Organic)\",\"source\":\"Land use\",\"target\":\"Vegetables (Organic)\",\"value\":0.00100453712203756},{\"name\":\"Vegetables (Organic)\",\"source\":\"Environment\",\"target\":\"Climate change\",\"value\":0.0112886157414413},{\"name\":\"Climate change\",\"source\":\"Climate change\",\"target\":\"Cocoa butter (Organic)\",\"value\":0.00676852971933996},{\"name\":\"Harmful substances\",\"source\":\"Climate change\",\"target\":\"Cocoa mass (Organic)\",\"value\":0.00394686874786743},{\"name\":\"Water use\",\"source\":\"Climate change\",\"target\":\"Cane sugar (Organic)\",\"value\":0.000315972058711838},{\"name\":\"Resource depletion\",\"source\":\"Climate change\",\"target\":\"Hazelnuts (Organic)\",\"value\":0.000218969462265292},{\"name\":\"Refrigeration\",\"source\":\"Climate change\",\"target\":\"Vegetables (Organic)\",\"value\":0.0000382757532567656},{\"name\":\"Packaging\",\"source\":\"Environment\",\"target\":\"Harmful substances\",\"value\":0.00604275542495656},{\"name\":\"Human rights\",\"source\":\"Harmful substances\",\"target\":\"Cocoa mass (Organic)\",\"value\":0.0055125989240741},{\"name\":\"Child labour\",\"source\":\"Harmful substances\",\"target\":\"Cocoa butter (Organic)\",\"value\":0.000330017607892127},{\"name\":\"Coconut oil (Organic)\",\"source\":\"Harmful substances\",\"target\":\"Cane sugar (Organic)\",\"value\":0.000200138892990337},{\"name\":\"Forced labour\",\"source\":\"Harmful substances\",\"target\":\"Hazelnuts (Organic)\",\"value\":0},{\"name\":\"Health safety\",\"source\":\"Harmful substances\",\"target\":\"Vegetables (Organic)\",\"value\":0},{\"name\":\"Access to water\",\"source\":\"Environment\",\"target\":\"Water use\",\"value\":0.00148345269044703},{\"name\":\"Freedom of association\",\"source\":\"Water use\",\"target\":\"Cocoa butter (Organic)\",\"value\":0.00135309891304186},{\"name\":\"Access to lan\",\"source\":\"Total\",\"target\":\"Environment\",\"value\":0.342284047256003},{\"name\":\"Sufficient wage\",\"source\":\"Water use\",\"target\":\"Cocoa mass (Organic)\",\"value\":0.000105714137908639},{\"name\":\"Equal rights migrants\",\"source\":\"Water use\",\"target\":\"Hazelnuts (Organic)\",\"value\":0.0000133452642581887},{\"name\":\"Discrimination\",\"source\":\"Water use\",\"target\":\"Cane sugar (Organic)\",\"value\":0.00000878074837009238},{\"name\":\"Working hours\",\"source\":\"Water use\",\"target\":\"Vegetables (Organic)\",\"value\":0.0000025136268682477},{\"source\":\"Environment\",\"target\":\"Resource depletion\",\"value\":0.000240519729288764},{\"source\":\"Resource depletion\",\"target\":\"Cane sugar (Organic)\",\"value\":0.000226237279345084},{\"source\":\"Resource depletion\",\"target\":\"Vegetables (Organic)\",\"value\":0.0000142824499436793},{\"source\":\"Resource depletion\",\"target\":\"Hazelnuts (Organic)\",\"value\":0},{\"source\":\"Resource depletion\",\"target\":\"Cocoa mass (Organic)\",\"value\":0},{\"source\":\"Resource depletion\",\"target\":\"Cocoa butter (Organic)\",\"value\":0},{\"source\":\"Environment\",\"target\":\"Refrigeration\",\"value\":0},{\"source\":\"Environment\",\"target\":\"Packaging\",\"value\":0},{\"source\":\"Total\",\"target\":\"Human rights\",\"value\":0.307574096993239},{\"source\":\"Human rights\",\"target\":\"Child labour\",\"value\":0.0410641202645833},{\"source\":\"Child labour\",\"target\":\"Hazelnuts (Organic)\",\"value\":0.0105339381639722},{\"source\":\"Child labour\",\"target\":\"Cocoa mass (Organic)\",\"value\":0.0105},{\"source\":\"Child labour\",\"target\":\"Cocoa butter (Organic)\",\"value\":0.0087294420777},{\"source\":\"Child labour\",\"target\":\"Coconut oil (Organic)\",\"value\":0.00474399974233333},{\"source\":\"Child labour\",\"target\":\"Cane sugar (Organic)\",\"value\":0.00388226450884445},{\"source\":\"Child labour\",\"target\":\"Vegetables (Organic)\",\"value\":0.00267447577173333},{\"source\":\"Human rights\",\"target\":\"Forced labour\",\"value\":0.0365458590642445},{\"source\":\"Forced labour\",\"target\":\"Hazelnuts (Organic)\",\"value\":0.0114913076376389},{\"source\":\"Forced labour\",\"target\":\"Cocoa butter (Organic)\",\"value\":0.0081134807347},{\"source\":\"Forced labour\",\"target\":\"Cocoa mass (Organic)\",\"value\":0.00765230236575},{\"source\":\"Forced labour\",\"target\":\"Cane sugar (Organic)\",\"value\":0.004},{\"source\":\"Forced labour\",\"target\":\"Vegetables (Organic)\",\"value\":0.00296668823626667},{\"source\":\"Forced labour\",\"target\":\"Coconut oil (Organic)\",\"value\":0.00232208008988889},{\"source\":\"Human rights\",\"target\":\"Health safety\",\"value\":0.0345435327843611},{\"source\":\"Health safety\",\"target\":\"Hazelnuts (Organic)\",\"value\":0.0121419536385},{\"source\":\"Health safety\",\"target\":\"Cocoa mass (Organic)\",\"value\":0.00766772850678333},{\"source\":\"Health safety\",\"target\":\"Cocoa butter (Organic)\",\"value\":0.0056245892061},{\"source\":\"Health safety\",\"target\":\"Coconut oil (Organic)\",\"value\":0.00361616847688889},{\"source\":\"Health safety\",\"target\":\"Cane sugar (Organic)\",\"value\":0.00277374682533333},{\"source\":\"Health safety\",\"target\":\"Vegetables (Organic)\",\"value\":0.00271934613075556},{\"source\":\"Human rights\",\"target\":\"Access to water\",\"value\":0.0340206659360667},{\"source\":\"Access to water\",\"target\":\"Cocoa mass (Organic)\",\"value\":0.0105},{\"source\":\"Access to water\",\"target\":\"Cocoa butter (Organic)\",\"value\":0.0089274160792},{\"source\":\"Access to water\",\"target\":\"Hazelnuts (Organic)\",\"value\":0.0054148022845},{\"source\":\"Access to water\",\"target\":\"Cane sugar (Organic)\",\"value\":0.00333938149786667},{\"source\":\"Access to water\",\"target\":\"Vegetables (Organic)\",\"value\":0.00314663377488889},{\"source\":\"Access to water\",\"target\":\"Coconut oil (Organic)\",\"value\":0.00269243229961111},{\"source\":\"Human rights\",\"target\":\"Freedom of association\",\"value\":0.0320571523941667},{\"source\":\"Freedom of association\",\"target\":\"Hazelnuts (Organic)\",\"value\":0.0132312483463611},{\"source\":\"Freedom of association\",\"target\":\"Cocoa butter (Organic)\",\"value\":0.0077695200707},{\"source\":\"Freedom of association\",\"target\":\"Cocoa mass (Organic)\",\"value\":0.00510606573995},{\"source\":\"Freedom of association\",\"target\":\"Vegetables (Organic)\",\"value\":0.00354321156324444},{\"source\":\"Freedom of association\",\"target\":\"Cane sugar (Organic)\",\"value\":0.00240710667391111},{\"source\":\"Freedom of association\",\"target\":\"Coconut oil (Organic)\",\"value\":0},{\"source\":\"Human rights\",\"target\":\"Access to land\",\"value\":0.0315022209894056},{\"source\":\"Access to land\",\"target\":\"Hazelnuts (Organic)\",\"value\":0.00964970063322223},{\"source\":\"Access to land\",\"target\":\"Cocoa mass (Organic)\",\"value\":0.00938530207965},{\"source\":\"Access to land\",\"target\":\"Cocoa butter (Organic)\",\"value\":0.0060110791848},{\"source\":\"Access to land\",\"target\":\"Cane sugar (Organic)\",\"value\":0.00380818314608889},{\"source\":\"Access to land\",\"target\":\"Vegetables (Organic)\",\"value\":0.00264795594564445},{\"source\":\"Access to land\",\"target\":\"Coconut oil (Organic)\",\"value\":0},{\"source\":\"Human rights\",\"target\":\"Sufficient wage\",\"value\":0.0287776757227333},{\"source\":\"Sufficient wage\",\"target\":\"Cocoa mass (Organic)\",\"value\":0.00883512456493333},{\"source\":\"Sufficient wage\",\"target\":\"Cocoa butter (Organic)\",\"value\":0.0078343367268},{\"source\":\"Sufficient wage\",\"target\":\"Coconut oil (Organic)\",\"value\":0.00347879026511111},{\"source\":\"Sufficient wage\",\"target\":\"Hazelnuts (Organic)\",\"value\":0.00316254211388889},{\"source\":\"Sufficient wage\",\"target\":\"Vegetables (Organic)\",\"value\":0.00281013722808889},{\"source\":\"Sufficient wage\",\"target\":\"Cane sugar (Organic)\",\"value\":0.00265674482391111},{\"source\":\"Human rights\",\"target\":\"Equal rights migrants\",\"value\":0.0271146645119444},{\"source\":\"Equal rights migrants\",\"target\":\"Cocoa butter (Organic)\",\"value\":0.0071042315061},{\"source\":\"Equal rights migrants\",\"target\":\"Cocoa mass (Organic)\",\"value\":0.00636673210005},{\"source\":\"Equal rights migrants\",\"target\":\"Hazelnuts (Organic)\",\"value\":0.00601459775836111},{\"source\":\"Equal rights migrants\",\"target\":\"Coconut oil (Organic)\",\"value\":0.00429185583138889},{\"source\":\"Equal rights migrants\",\"target\":\"Cane sugar (Organic)\",\"value\":0.00182647471915556},{\"source\":\"Equal rights migrants\",\"target\":\"Vegetables (Organic)\",\"value\":0.00151077259688889},{\"source\":\"Human rights\",\"target\":\"Discrimination\",\"value\":0.0211217763359833},{\"source\":\"Discrimination\",\"target\":\"Cocoa mass (Organic)\",\"value\":0.00609671700306667},{\"source\":\"Discrimination\",\"target\":\"Cocoa butter (Organic)\",\"value\":0.0047738806325},{\"source\":\"Discrimination\",\"target\":\"Coconut oil (Organic)\",\"value\":0.00368119084494444},{\"source\":\"Discrimination\",\"target\":\"Vegetables (Organic)\",\"value\":0.00286009813604444},{\"source\":\"Discrimination\",\"target\":\"Cane sugar (Organic)\",\"value\":0.00283706180951111},{\"source\":\"Discrimination\",\"target\":\"Hazelnuts (Organic)\",\"value\":0.000872827909916666},{\"source\":\"Human rights\",\"target\":\"Working hours\",\"value\":0.02082642898975},{\"source\":\"Working hours\",\"target\":\"Hazelnuts (Organic)\",\"value\":0.0107216773848333},{\"source\":\"Working hours\",\"target\":\"Coconut oil (Organic)\",\"value\":0.00359009052944444},{\"source\":\"Working hours\",\"target\":\"Vegetables (Organic)\",\"value\":0.00212300379075556},{\"source\":\"Working hours\",\"target\":\"Cocoa butter (Organic)\",\"value\":0.0018518584356},{\"source\":\"Working hours\",\"target\":\"Cocoa mass (Organic)\",\"value\":0.00158227069058333},{\"source\":\"Working hours\",\"target\":\"Cane sugar (Organic)\",\"value\":0.000957528158533333}]', 'sangjitu');
INSERT INTO `up_chartdata` VALUES ('63', '[{\"name\":\"节点1\",\"value\":22.2,\"source\":\"节点1\",\"target\":\"节点2\",\"category\":\"index1\"},{\"name\":\"节点2\",\"value\":33.2,\"source\":\"节点2\",\"target\":\"节点3\",\"category\":\"index2\"},{\"name\":\"节点3\",\"value\":26.2,\"source\":\"节点3\",\"target\":\"节点5\",\"category\":\"index3\"},{\"name\":\"节点4\",\"value\":18.2,\"source\":\"节点4\",\"target\":\"节点3\",\"category\":\"index4\"},{\"name\":\"节点5\",\"value\":43.2,\"source\":\"节点5\",\"target\":\"节点4\",\"category\":\"index5\"},{\"name\":\"节点11\",\"value\":22.2,\"source\":\"节点11\",\"target\":\"节点21\",\"category\":\"index11\"},{\"name\":\"节点21\",\"value\":33.2,\"source\":\"节点21\",\"target\":\"节点31\",\"category\":\"index21\"},{\"name\":\"节点31\",\"value\":26.2,\"source\":\"节点31\",\"target\":\"节点51\",\"category\":\"index31\"},{\"name\":\"节点41\",\"value\":18.2,\"source\":\"节点41\",\"target\":\"节点31\",\"category\":\"index41\"},{\"name\":\"节点51\",\"value\":43.2,\"source\":\"节点51\",\"target\":\"节点41\",\"category\":\"index51\"},{\"name\":\"节点12\",\"value\":22.2,\"source\":\"节点12\",\"target\":\"节点22\",\"category\":\"index12\"},{\"name\":\"节点22\",\"value\":33.2,\"source\":\"节点22\",\"target\":\"节点32\",\"category\":\"index22\"},{\"name\":\"节点32\",\"value\":26.2,\"source\":\"节点32\",\"target\":\"节点52\",\"category\":\"index32\"},{\"name\":\"节点42\",\"value\":18.2,\"source\":\"节点42\",\"target\":\"节点32\",\"category\":\"index42\"},{\"name\":\"节点52\",\"value\":43.2,\"source\":\"节点52\",\"target\":\"节点42\",\"category\":\"index52\"},{\"name\":\"节点13\",\"value\":22.2,\"source\":\"节点13\",\"target\":\"节点23\",\"category\":\"index13\"},{\"name\":\"节点23\",\"value\":33.2,\"source\":\"节点23\",\"target\":\"节点33\",\"category\":\"index23\"},{\"name\":\"节点33\",\"value\":26.2,\"source\":\"节点33\",\"target\":\"节点53\",\"category\":\"index33\"},{\"name\":\"节点43\",\"value\":18.2,\"source\":\"节点43\",\"target\":\"节点33\",\"category\":\"index43\"},{\"name\":\"节点53\",\"value\":43.2,\"source\":\"节点53\",\"target\":\"节点43\",\"category\":\"index53\"},{\"name\":\"节点14\",\"value\":22.2,\"source\":\"节点14\",\"target\":\"节点24\",\"category\":\"index14\"},{\"name\":\"节点24\",\"value\":33.2,\"source\":\"节点24\",\"target\":\"节点34\",\"category\":\"index24\"},{\"name\":\"节点34\",\"value\":26.2,\"source\":\"节点34\",\"target\":\"节点54\",\"category\":\"index34\"},{\"name\":\"节点44\",\"value\":18.2,\"source\":\"节点44\",\"target\":\"节点34\",\"category\":\"index44\"},{\"name\":\"节点54\",\"value\":43.2,\"source\":\"节点54\",\"target\":\"节点44\",\"category\":\"index54\"},{\"name\":\"节点15\",\"value\":22.2,\"source\":\"节点15\",\"target\":\"节点25\",\"category\":\"index15\"},{\"name\":\"节点25\",\"value\":33.2,\"source\":\"节点25\",\"target\":\"节点35\",\"category\":\"index25\"},{\"name\":\"节点35\",\"value\":26.2,\"source\":\"节点35\",\"target\":\"节点55\",\"category\":\"index35\"},{\"name\":\"节点45\",\"value\":18.2,\"source\":\"节点45\",\"target\":\"节点35\",\"category\":\"index45\"},{\"name\":\"节点55\",\"value\":43.2,\"source\":\"节点55\",\"target\":\"节点45\",\"category\":\"index55\"}]', 'hexiantu');
INSERT INTO `up_chartdata` VALUES ('64', '[{\"name\":\"0~5岁\",\"value\":\"320\",\"series\":\"系列一\"},{\"name\":\"5~10岁\",\"value\":\"220\",\"series\":\"系列一\"},{\"name\":\"10-30岁\",\"value\":\"341\",\"series\":\"系列一\"},{\"name\":\"30-50岁\",\"value\":\"120\",\"series\":\"系列一\"},{\"name\":\"50-70岁\",\"value\":\"280\",\"series\":\"系列一\"},{\"name\":\"0~5岁\",\"value\":\"-320\",\"series\":\"系列二\"},{\"name\":\"5~10岁\",\"value\":\"-220\",\"series\":\"系列二\"},{\"name\":\"10-30岁\",\"value\":\"-341\",\"series\":\"系列二\"},{\"name\":\"30-50岁\",\"value\":\"-120\",\"series\":\"系列二\"},{\"name\":\"50-70岁\",\"value\":\"-280\",\"series\":\"系列二\"}]', 'shuangxianghengxiangzhuzhuangtu');
INSERT INTO `up_chartdata` VALUES ('65', '[{\"coutnum\":\"5\"}]', 'jishuban2');
INSERT INTO `up_chartdata` VALUES ('66', '[{\"name\":\"三级标题\",\"value\":3,\"pid\":3,\"cid\":4},{\"name\":\"二级标题\",\"value\":2,\"pid\":2,\"cid\":3},{\"name\":\"一级标题\",\"value\":6,\"pid\":1,\"cid\":2},{\"name\":\"顶级标题\",\"value\":10,\"pid\":0,\"cid\":1},{\"name\":\"一级标题\",\"value\":4,\"pid\":1,\"cid\":5},{\"name\":\"顶级标题2\",\"value\":20,\"pid\":0,\"cid\":6},{\"name\":\"一级标题2\",\"value\":11,\"pid\":6,\"cid\":7}]', 'xuritu');
INSERT INTO `up_chartdata` VALUES ('67', '[{\"xdata\":0,\"ydata\":1.5,\"value\":120,\"series\":\"系列一\"},{\"xdata\":1,\"ydata\":5.5,\"value\":260,\"series\":\"系列一\"},{\"xdata\":2,\"ydata\":9.5,\"value\":180,\"series\":\"系列一\"},{\"xdata\":4,\"ydata\":13.5,\"value\":260,\"series\":\"系列一\"},{\"xdata\":5,\"ydata\":17.5,\"value\":140,\"series\":\"系列一\"},{\"xdata\":6,\"ydata\":21.5,\"value\":300,\"series\":\"系列一\"},{\"xdata\":0,\"ydata\":4,\"value\":55,\"series\":\"系列二\"},{\"xdata\":1,\"ydata\":8,\"value\":77,\"series\":\"系列二\"},{\"xdata\":2,\"ydata\":12,\"value\":180,\"series\":\"系列二\"},{\"xdata\":4,\"ydata\":14,\"value\":100,\"series\":\"系列二\"},{\"xdata\":5,\"ydata\":18,\"value\":120,\"series\":\"系列二\"},{\"xdata\":6,\"ydata\":22,\"value\":80,\"series\":\"系列二\"}]', '3dzhexiantu');
INSERT INTO `up_chartdata` VALUES ('68', '[{\"name\":\"北京\",\"value\":20,\"series\":\"系列一\"},{\"name\":\"上海\",\"value\":12,\"series\":\"系列一\"},{\"name\":\"武汉\",\"value\":27,\"series\":\"系列一\"},{\"name\":\"吕梁\",\"value\":29,\"series\":\"系列一\"},{\"name\":\"山西\",\"value\":20,\"series\":\"系列一\"},{\"name\":\"海南\",\"value\":12,\"series\":\"系列一\"},{\"name\":\"合肥\",\"value\":27,\"series\":\"系列一\"},{\"name\":\"济南\",\"value\":29,\"series\":\"系列一\"},{\"name\":\"贵州\",\"value\":10,\"series\":\"系列一\"},{\"name\":\"吉林\",\"value\":22,\"series\":\"系列一\"},{\"name\":\"五台山\",\"value\":17,\"series\":\"系列一\"},{\"name\":\"四川\",\"value\":19,\"series\":\"系列一\"},{\"name\":\"郑州\",\"value\":14,\"series\":\"系列一\"}]', 'xuxianzhexiantu');
INSERT INTO `up_chartdata` VALUES ('69', '[{\"name\":\"直接访问\",\"value\":20},{\"name\":\"邮件营销\",\"value\":20},{\"name\":\"联盟广告\",\"value\":20},{\"name\":\"视频广告\",\"value\":20},{\"name\":\"搜索引擎\",\"value\":20}]', 'huanzhuangfaguangzhanbitu');
INSERT INTO `up_chartdata` VALUES ('70', '[{\"label\":\"company\",\"data\":\"Electrical Systems\",\"row\":1},{\"label\":\"name\",\"data\":\"Ted\",\"row\":1},{\"label\":\"surname\",\"data\":\"Smith\",\"row\":1},{\"label\":\"age\",\"data\":30,\"row\":1},{\"label\":\"email\",\"data\":\"ted.smith@gmail.com\",\"row\":1},{\"label\":\"company\",\"data\":\"Energy and Oil\",\"row\":2},{\"label\":\"name\",\"data\":\"Ed\",\"row\":2},{\"label\":\"surname\",\"data\":\"Johnson\",\"row\":2},{\"label\":\"age\",\"data\":35,\"row\":2},{\"label\":\"email\",\"data\":\"ed.johnson@gmail.com\",\"row\":2},{\"label\":\"company\",\"data\":\"Airbus\",\"row\":3},{\"label\":\"name\",\"data\":\"Sam\",\"row\":3},{\"label\":\"surname\",\"data\":\"Williams\",\"row\":3},{\"label\":\"age\",\"data\":38,\"row\":3},{\"label\":\"email\",\"data\":\"sam.williams@gmail.com\",\"row\":3},{\"label\":\"company\",\"data\":\"Renault\",\"row\":4},{\"label\":\"name\",\"data\":\"Alexander\",\"row\":4},{\"label\":\"surname\",\"data\":\"Brown\",\"row\":4},{\"label\":\"age\",\"data\":24,\"row\":4},{\"label\":\"email\",\"data\":\"alexander.brown@gmail.com\",\"row\":4}]', 'nengyuanbiaoge');
INSERT INTO `up_chartdata` VALUES ('71', '[{\"label\":\"company\",\"data\":\"Electrical Systems\",\"row\":1},{\"label\":\"name\",\"data\":\"Ted\",\"row\":1},{\"label\":\"surname\",\"data\":\"Smith\",\"row\":1},{\"label\":\"age\",\"data\":30,\"row\":1},{\"label\":\"email\",\"data\":\"ted.smith@gmail.com\",\"row\":1},{\"label\":\"company\",\"data\":\"Energy and Oil\",\"row\":2},{\"label\":\"name\",\"data\":\"Ed\",\"row\":2},{\"label\":\"surname\",\"data\":\"Johnson\",\"row\":2},{\"label\":\"age\",\"data\":35,\"row\":2},{\"label\":\"email\",\"data\":\"ed.johnson@gmail.com\",\"row\":2},{\"label\":\"company\",\"data\":\"Airbus\",\"row\":3},{\"label\":\"name\",\"data\":\"Sam\",\"row\":3},{\"label\":\"surname\",\"data\":\"Williams\",\"row\":3},{\"label\":\"age\",\"data\":38,\"row\":3},{\"label\":\"email\",\"data\":\"sam.williams@gmail.com\",\"row\":3},{\"label\":\"company\",\"data\":\"Renault\",\"row\":4},{\"label\":\"name\",\"data\":\"Alexander\",\"row\":4},{\"label\":\"surname\",\"data\":\"Brown\",\"row\":4},{\"label\":\"age\",\"data\":24,\"row\":4},{\"label\":\"email\",\"data\":\"alexander.brown@gmail.com\",\"row\":4}]', 'paimingbiaoge');
INSERT INTO `up_chartdata` VALUES ('72', '[{\"name\":\"A\",\"value\":15,\"series\":\"系列一\"},{\"name\":\"B\",\"value\":50,\"series\":\"系列一\"},{\"name\":\"C\",\"value\":30,\"series\":\"系列一\"},{\"name\":\"D\",\"value\":20,\"series\":\"系列一\"},{\"name\":\"A\",\"value\":25,\"series\":\"系列二\"},{\"name\":\"B\",\"value\":15,\"series\":\"系列二\"},{\"name\":\"C\",\"value\":35,\"series\":\"系列二\"},{\"name\":\"D\",\"value\":25,\"series\":\"系列二\"},{\"name\":\"A\",\"value\":35,\"series\":\"系列三\"},{\"name\":\"B\",\"value\":15,\"series\":\"系列三\"},{\"name\":\"C\",\"value\":25,\"series\":\"系列三\"},{\"name\":\"D\",\"value\":30,\"series\":\"系列三\"}]', 'zhuixingzhutu');
INSERT INTO `up_chartdata` VALUES ('73', '[{\"value\":2,\"name\":\"一月\",\"series\":\"系列一\"},{\"value\":3,\"name\":\"二月\",\"series\":\"系列一\"},{\"value\":1,\"name\":\"三月\",\"series\":\"系列一\"},{\"value\":2,\"name\":\"四月\",\"series\":\"系列一\"},{\"value\":1,\"name\":\"五月\",\"series\":\"系列一\"},{\"value\":2,\"name\":\"六月\",\"series\":\"系列一\"},{\"value\":2,\"name\":\"一月\",\"series\":\"系列二\"},{\"value\":3,\"name\":\"二月\",\"series\":\"系列二\"},{\"value\":1,\"name\":\"三月\",\"series\":\"系列二\"},{\"value\":2,\"name\":\"四月\",\"series\":\"系列二\"},{\"value\":1,\"name\":\"五月\",\"series\":\"系列二\"},{\"value\":2,\"name\":\"六月\",\"series\":\"系列二\"}]', '2.5dduidietu');
INSERT INTO `up_chartdata` VALUES ('74', '[{\"value\":\"35%\",\"total\":\"A\"},{\"value\":\"80%\",\"total\":\"B\"}]', 'baifenbiduibibintu');
INSERT INTO `up_chartdata` VALUES ('75', '[{\"value\":\"35%\",\"name\":\"A\"}]', 'jindutiaoxingma');
INSERT INTO `up_chartdata` VALUES ('76', '[{\"name\":\"海南三亚\",\"value\":663},{\"name\":\"江苏东台\",\"value\":234},{\"name\":\"广东惠州\",\"value\":234},{\"name\":\"北京105厂\",\"value\":523}]', 'jianbianzhuzhuangtu');
INSERT INTO `up_chartdata` VALUES ('77', '[{\"title\":\"阿勒泰\",\"value\":160,\"percent\":\"60%\",\"standard\":100}]', '3dzhuzhuangqushibianhuatu');
INSERT INTO `up_chartdata` VALUES ('78', '[{\"name\":\"海南三亚\",\"value\":663},{\"name\":\"江苏东台\",\"value\":234},{\"name\":\"广东惠州\",\"value\":234},{\"name\":\"北京105厂\",\"value\":523}]', 'shuipingfaguangzhuzhuangtu');
INSERT INTO `up_chartdata` VALUES ('79', '[{\"name\":\"安徽合肥\",\"value\":25,\"series\":\"主叫\"},{\"name\":\"江苏东台\",\"value\":13,\"series\":\"主叫\"},{\"name\":\"海南三亚\",\"value\":4,\"series\":\"主叫\"},{\"name\":\"安徽合肥\",\"value\":22,\"series\":\"被叫\"},{\"name\":\"江苏东台\",\"value\":42,\"series\":\"被叫\"},{\"name\":\"海南三亚\",\"value\":12,\"series\":\"被叫\"}]', 'fenzujianbianzhuzhuangtu');
INSERT INTO `up_chartdata` VALUES ('80', '[{\"lengend\":\"第一季度\",\"value\":[{\"name\":\"淮北市\",\"value\":150,\"series\":\"系列二\"},{\"name\":\"淮南市\",\"value\":220,\"series\":\"系列二\"},{\"name\":\"合肥市\",\"value\":260,\"series\":\"系列二\"},{\"name\":\"芜湖市\",\"value\":200,\"series\":\"系列二\"},{\"name\":\"铜陵市\",\"value\":240,\"series\":\"系列二\"},{\"name\":\"黄山市\",\"value\":280,\"series\":\"系列二\"},{\"name\":\"池州市\",\"value\":450,\"series\":\"系列二\"},{\"name\":\"阜阳市\",\"value\":320,\"series\":\"系列二\"},{\"name\":\"宣城市\",\"value\":420,\"series\":\"系列二\"},{\"name\":\"亳州市\",\"value\":310,\"series\":\"系列二\"},{\"name\":\"滁州市\",\"value\":260,\"series\":\"系列二\"},{\"name\":\"安庆市\",\"value\":240,\"series\":\"系列二\"},{\"name\":\"蚌埠市\",\"value\":360,\"series\":\"系列二\"},{\"name\":\"宿州市\",\"value\":350,\"series\":\"系列二\"},{\"name\":\"马鞍山市\",\"value\":40,\"series\":\"系列二\"}]}, {\"lengend\":\"第二季度\",\"value\":[{\"name\":\"淮北市\",\"value\":70,\"series\":\"系列二\"},{\"name\":\"淮南市\",\"value\":170,\"series\":\"系列二\"},{\"name\":\"合肥市\",\"value\":40,\"series\":\"系列二\"},{\"name\":\"芜湖市\",\"value\":170,\"series\":\"系列二\"},{\"name\":\"铜陵市\",\"value\":240,\"series\":\"系列二\"},{\"name\":\"黄山市\",\"value\":200,\"series\":\"系列二\"},{\"name\":\"池州市\",\"value\":330,\"series\":\"系列二\"},{\"name\":\"阜阳市\",\"value\":120,\"series\":\"系列二\"},{\"name\":\"宣城市\",\"value\":350,\"series\":\"系列二\"},{\"name\":\"亳州市\",\"value\":110,\"series\":\"系列二\"},{\"name\":\"滁州市\",\"value\":190,\"series\":\"系列二\"},{\"name\":\"安庆市\",\"value\":278,\"series\":\"系列二\"},{\"name\":\"蚌埠市\",\"value\":300,\"series\":\"系列二\"},{\"name\":\"宿州市\",\"value\":500,\"series\":\"系列二\"},{\"name\":\"马鞍山市\",\"value\":20,\"series\":\"系列二\"}]}, {\"lengend\":\"第三季度\",\"value\":[{\"name\":\"淮北市\",\"value\":70,\"series\":\"系列二\"},{\"name\":\"淮南市\",\"value\":33,\"series\":\"系列二\"},{\"name\":\"合肥市\",\"value\":555,\"series\":\"系列二\"},{\"name\":\"芜湖市\",\"value\":333,\"series\":\"系列二\"},{\"name\":\"铜陵市\",\"value\":666,\"series\":\"系列二\"},{\"name\":\"黄山市\",\"value\":222,\"series\":\"系列二\"},{\"name\":\"池州市\",\"value\":450,\"series\":\"系列二\"},{\"name\":\"阜阳市\",\"value\":210,\"series\":\"系列二\"},{\"name\":\"宣城市\",\"value\":530,\"series\":\"系列二\"},{\"name\":\"亳州市\",\"value\":260,\"series\":\"系列二\"},{\"name\":\"滁州市\",\"value\":440,\"series\":\"系列二\"},{\"name\":\"安庆市\",\"value\":710,\"series\":\"系列二\"},{\"name\":\"蚌埠市\",\"value\":400,\"series\":\"系列二\"},{\"name\":\"宿州市\",\"value\":100,\"series\":\"系列二\"},{\"name\":\"马鞍山市\",\"value\":80,\"series\":\"系列二\"}]}, {\"lengend\":\"第四季度\",\"value\":[{\"name\":\"淮北市\",\"value\":250,\"series\":\"系列二\"},{\"name\":\"淮南市\",\"value\":320,\"series\":\"系列二\"},{\"name\":\"合肥市\",\"value\":460,\"series\":\"系列二\"},{\"name\":\"芜湖市\",\"value\":500,\"series\":\"系列二\"},{\"name\":\"铜陵市\",\"value\":140,\"series\":\"系列二\"},{\"name\":\"黄山市\",\"value\":180,\"series\":\"系列二\"},{\"name\":\"池州市\",\"value\":650,\"series\":\"系列二\"},{\"name\":\"阜阳市\",\"value\":120,\"series\":\"系列二\"},{\"name\":\"宣城市\",\"value\":220,\"series\":\"系列二\"},{\"name\":\"亳州市\",\"value\":610,\"series\":\"系列二\"},{\"name\":\"滁州市\",\"value\":760,\"series\":\"系列二\"},{\"name\":\"安庆市\",\"value\":840,\"series\":\"系列二\"},{\"name\":\"蚌埠市\",\"value\":260,\"series\":\"系列二\"},{\"name\":\"宿州市\",\"value\":650,\"series\":\"系列二\"},{\"name\":\"马鞍山市\",\"value\":330,\"series\":\"系列二\"}]}]', 'timelinebar');
INSERT INTO `up_chartdata` VALUES ('81', '[{\"name\":\"2012\",\"value\":3,\"series\":\"增加值增长率\"},{\"name\":\"2013\",\"value\":7,\"series\":\"增加值增长率\"},{\"name\":\"2014\",\"value\":6,\"series\":\"增加值增长率\"},{\"name\":\"2015\",\"value\":4,\"series\":\"增加值增长率\"},{\"name\":\"2016\",\"value\":5,\"series\":\"增加值增长率\"},{\"name\":\"2012\",\"value\":7,\"series\":\"企业主营业务收入增长率\"},{\"name\":\"2013\",\"value\":4,\"series\":\"企业主营业务收入增长率\"},{\"name\":\"2014\",\"value\":6,\"series\":\"企业主营业务收入增长率\"},{\"name\":\"2015\",\"value\":5,\"series\":\"企业主营业务收入增长率\"},{\"name\":\"2016\",\"value\":4,\"series\":\"企业主营业务收入增长率\"},{\"name\":\"2012\",\"value\":4,\"series\":\"总产值\"},{\"name\":\"2013\",\"value\":4,\"series\":\"总产值\"},{\"name\":\"2014\",\"value\":3,\"series\":\"总产值\"},{\"name\":\"2015\",\"value\":3,\"series\":\"总产值\"},{\"name\":\"2016\",\"value\":3,\"series\":\"总产值\"},{\"name\":\"2012\",\"value\":4,\"series\":\"增加值\"},{\"name\":\"2013\",\"value\":5,\"series\":\"增加值\"},{\"name\":\"2014\",\"value\":5,\"series\":\"增加值\"},{\"name\":\"2015\",\"value\":5,\"series\":\"增加值\"},{\"name\":\"2016\",\"value\":5,\"series\":\"增加值\"},{\"name\":\"2012\",\"value\":4,\"series\":\"企业主营业务收入\"},{\"name\":\"2013\",\"value\":6,\"series\":\"企业主营业务收入\"},{\"name\":\"2014\",\"value\":7,\"series\":\"企业主营业务收入\"},{\"name\":\"2015\",\"value\":7,\"series\":\"企业主营业务收入\"},{\"name\":\"2016\",\"value\":10,\"series\":\"企业主营业务收入\"}]', 'zhexianzhuzhuangtu');
INSERT INTO `up_chartdata` VALUES ('82', '[{\"name\":\"雷达图\",\"value\":19354,\"target\":\"类型一\"},{\"name\":\"类型一\",\"value\":19354,\"target\":\"类型一\"},{\"name\":\"类型二\",\"value\":\"\",\"target\":\"类型一\"},{\"name\":\"类型三\",\"value\":\"\",\"target\":\"类型一\"},{\"name\":\"雷达图\",\"value\":14750,\"target\":\"类型二\"},{\"name\":\"类型一\",\"value\":\"\",\"target\":\"类型二\"},{\"name\":\"类型二\",\"value\":14750,\"target\":\"类型二\"},{\"name\":\"类型三\",\"value\":\"\",\"target\":\"类型二\"},{\"name\":\"雷达图\",\"value\":32718,\"target\":\"类型三\"},{\"name\":\"类型一\",\"value\":\"\",\"target\":\"类型三\"},{\"name\":\"类型二\",\"value\":\"\",\"target\":\"类型三\"},{\"name\":\"类型三\",\"value\":32718,\"target\":\"类型三\"}]', 'leidabianxingtu');
INSERT INTO `up_chartdata` VALUES ('83', '[{\"name\":\"电量统计\",\"value\":60,\"total\":100}]', 'shujubingtu');
INSERT INTO `up_chartdata` VALUES ('84', '[{\"name\":\"当前用户数\",\"value\":96886,\"total\":100000},{\"name\":\"移动端用户数\",\"value\":6666,\"total\":100000},{\"name\":\"电脑端用户数\",\"value\":666,\"total\":100000}]', 'huanxingshujutu');
INSERT INTO `up_chartdata` VALUES ('85', '[{\"value\":30,\"name\":\"在校学生\",\"total\":100},{\"value\":70,\"name\":\"职业设计人员\",\"total\":100},{\"value\":50,\"name\":\"兼职设计者\",\"total\":100},{\"value\":80,\"name\":\"业余爱好者\",\"total\":100}]', 'huanxingbilitu');
INSERT INTO `up_chartdata` VALUES ('86', '[{\"value\":1,\"name\":\"项目1\"},{\"value\":0,\"name\":\"项目2\"},{\"value\":0,\"name\":\"项目3\"},{\"value\":0,\"name\":\"项目4\"},{\"value\":0,\"name\":\"项目5\"},{\"value\":0,\"name\":\"项目6\"},{\"value\":0,\"name\":\"项目7\"},{\"value\":0,\"name\":\"项目8\"}]', 'babianxingtu2');
INSERT INTO `up_chartdata` VALUES ('87', '[{\"value\":55,\"name\":\"中层干部\"},{\"value\":46,\"name\":\"小组领导\"},{\"value\":88,\"name\":\"集团员工\"},{\"value\":26,\"name\":\"高层员工\"}]', 'siyecaotu');

-- ----------------------------
-- Table structure for up_collection
-- ----------------------------
DROP TABLE IF EXISTS `up_collection`;
CREATE TABLE `up_collection` (
  `collectionid` int(11) NOT NULL AUTO_INCREMENT COMMENT '收藏ID',
  `tconfig` text NOT NULL COMMENT '收藏图表配置',
  `tid` int(11) NOT NULL COMMENT '图表ID',
  `uid` int(11) DEFAULT NULL COMMENT '用户id',
  `is_col` int(1) DEFAULT '1',
  PRIMARY KEY (`collectionid`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='收藏图表';

-- ----------------------------
-- Records of up_collection
-- ----------------------------

-- ----------------------------
-- Table structure for up_databaselist
-- ----------------------------
DROP TABLE IF EXISTS `up_databaselist`;
CREATE TABLE `up_databaselist` (
  `lid` int(11) NOT NULL AUTO_INCREMENT COMMENT '数据库ID',
  `databasesname` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL COMMENT 'TP 框架TYPE',
  PRIMARY KEY (`lid`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='数据库列表';

-- ----------------------------
-- Records of up_databaselist
-- ----------------------------
INSERT INTO `up_databaselist` VALUES ('1', 'MySql', 'mysql');
INSERT INTO `up_databaselist` VALUES ('2', 'PgSql', 'pgsql');
INSERT INTO `up_databaselist` VALUES ('3', 'SQLServer', 'sqlsrv');
INSERT INTO `up_databaselist` VALUES ('4', 'Oracle', 'oracle');

-- ----------------------------
-- Table structure for up_databasesource
-- ----------------------------
DROP TABLE IF EXISTS `up_databasesource`;
CREATE TABLE `up_databasesource` (
  `baseid` int(11) NOT NULL AUTO_INCREMENT COMMENT '数据库源ID',
  `basename` varchar(255) NOT NULL COMMENT '数据库连接名字',
  `baseconfig` text NOT NULL COMMENT '数据库配置',
  `stype` varchar(32) NOT NULL DEFAULT '4' COMMENT '数据源类型 默认4 数据库源',
  `remark` text,
  `dbname` varchar(255) DEFAULT NULL COMMENT '用户填写的数据库名',
  `databases` text COMMENT '数据库中的表',
  `sid` int(11) NOT NULL COMMENT '所属分类',
  `createtime` int(11) NOT NULL COMMENT '连接的全部数据库',
  PRIMARY KEY (`baseid`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='数据库连接源表';

-- ----------------------------
-- Records of up_databasesource
-- ----------------------------

-- ----------------------------
-- Table structure for up_datament
-- ----------------------------
DROP TABLE IF EXISTS `up_datament`;
CREATE TABLE `up_datament` (
  `daid` int(10) NOT NULL AUTO_INCREMENT COMMENT '表id',
  `cid` varchar(255) DEFAULT NULL COMMENT '分类id 1是工业大数据，2是电力，3是未来工厂，4是石油销售，5是网络监控',
  `sid` int(10) DEFAULT NULL COMMENT '数据源id',
  `dataname` varchar(255) DEFAULT NULL COMMENT '数据名称',
  `datatype` varchar(255) DEFAULT NULL COMMENT '数据类型 1是自定义视图，2是API，3是数据库连接，4是excel，5是网络监控',
  `filepath` varchar(255) DEFAULT '' COMMENT '文件路径(API地址)',
  `returnsql` varchar(255) DEFAULT '' COMMENT 'SQL语句',
  `data` longtext COMMENT '返回值',
  `remark` text COMMENT '备注',
  `tablename` text COMMENT '数据表',
  `createtime` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP COMMENT '创建时间',
  `ccid` int(11) DEFAULT NULL COMMENT '分类id(修改分类要用)',
  PRIMARY KEY (`daid`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of up_datament
-- ----------------------------

-- ----------------------------
-- Table structure for up_datamentgroup
-- ----------------------------
DROP TABLE IF EXISTS `up_datamentgroup`;
CREATE TABLE `up_datamentgroup` (
  `sid` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT NULL,
  `screenname` varchar(255) DEFAULT NULL,
  `remarks` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`sid`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Records of up_datamentgroup
-- ----------------------------
INSERT INTO `up_datamentgroup` VALUES ('1', null, '工业大数据', null);
INSERT INTO `up_datamentgroup` VALUES ('2', null, '电力', null);
INSERT INTO `up_datamentgroup` VALUES ('3', null, '未来工厂', null);
INSERT INTO `up_datamentgroup` VALUES ('4', null, '石油销售', null);
INSERT INTO `up_datamentgroup` VALUES ('5', null, '网络监控', null);

-- ----------------------------
-- Table structure for up_datamentname
-- ----------------------------
DROP TABLE IF EXISTS `up_datamentname`;
CREATE TABLE `up_datamentname` (
  `cid` int(11) NOT NULL AUTO_INCREMENT COMMENT '数据类型id',
  `name` varchar(255) DEFAULT NULL COMMENT '数据类型',
  `type` varchar(255) DEFAULT NULL COMMENT '分类名称',
  PRIMARY KEY (`cid`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Records of up_datamentname
-- ----------------------------
INSERT INTO `up_datamentname` VALUES ('1', 'Excel/Csv', 'excel/csv');
INSERT INTO `up_datamentname` VALUES ('2', 'API', 'api');
INSERT INTO `up_datamentname` VALUES ('3', 'SQL', 'sql');
INSERT INTO `up_datamentname` VALUES ('4', 'WebSocket', 'websocket');
INSERT INTO `up_datamentname` VALUES ('5', '自定义视图', '自定义视图');

-- ----------------------------
-- Table structure for up_datasource
-- ----------------------------
DROP TABLE IF EXISTS `up_datasource`;
CREATE TABLE `up_datasource` (
  `daid` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `cid` int(11) NOT NULL COMMENT '分类id',
  `dtid` int(11) NOT NULL COMMENT '数据类型ID',
  `dataname` varchar(100) NOT NULL COMMENT '数据名称',
  `createtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `remark` varchar(200) DEFAULT NULL COMMENT '备注',
  `uploadpath` varchar(100) DEFAULT NULL COMMENT 'excel文件路径/api网址',
  `structure` varchar(200) DEFAULT NULL COMMENT 'API结构',
  `updatetime` int(8) DEFAULT NULL COMMENT '自动更新时间间隔',
  `source` varchar(100) DEFAULT NULL COMMENT '自定义数据源名字',
  `host` varchar(100) DEFAULT NULL COMMENT '服务器名',
  `username` varchar(100) DEFAULT NULL COMMENT '用户名',
  `password` varchar(100) DEFAULT NULL COMMENT '密码',
  `port` int(8) DEFAULT NULL COMMENT '端口',
  `dbname` varchar(50) DEFAULT NULL COMMENT '数据库名',
  `link` varchar(100) DEFAULT NULL COMMENT '数据源链接',
  `len` int(11) DEFAULT NULL,
  `tablename` varchar(50) DEFAULT NULL COMMENT '表名',
  `field` varchar(50) DEFAULT NULL COMMENT '字段',
  `returnsql` longtext,
  `returnjson` longtext,
  `autoupdate` tinyint(4) DEFAULT NULL,
  `request` tinyint(4) DEFAULT NULL,
  `cookie` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`daid`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='数据库源存储表';

-- ----------------------------
-- Records of up_datasource
-- ----------------------------

-- ----------------------------
-- Table structure for up_datatype
-- ----------------------------
DROP TABLE IF EXISTS `up_datatype`;
CREATE TABLE `up_datatype` (
  `did` int(11) NOT NULL AUTO_INCREMENT COMMENT '数据类型ID',
  `typename` varchar(255) NOT NULL COMMENT '数据类型名称',
  PRIMARY KEY (`did`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Records of up_datatype
-- ----------------------------
INSERT INTO `up_datatype` VALUES ('1', '数据库连接');
INSERT INTO `up_datatype` VALUES ('2', 'Excel');
INSERT INTO `up_datatype` VALUES ('3', 'API');
INSERT INTO `up_datatype` VALUES ('4', 'SQL');
INSERT INTO `up_datatype` VALUES ('5', '自定义视图');

-- ----------------------------
-- Table structure for up_d_group1
-- ----------------------------
DROP TABLE IF EXISTS `up_d_group1`;
CREATE TABLE `up_d_group1` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `inserttime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `data` varchar(1000) NOT NULL,
  `count` int(11) NOT NULL,
  `class` varchar(1000) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Records of up_d_group1
-- ----------------------------

-- ----------------------------
-- Table structure for up_excelfile
-- ----------------------------
DROP TABLE IF EXISTS `up_excelfile`;
CREATE TABLE `up_excelfile` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(1000) NOT NULL,
  `src` varchar(1000) NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `username` varchar(300) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Records of up_excelfile
-- ----------------------------

-- ----------------------------
-- Table structure for up_file1
-- ----------------------------
DROP TABLE IF EXISTS `up_file1`;
CREATE TABLE `up_file1` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `src` text NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Records of up_file1
-- ----------------------------

-- ----------------------------
-- Table structure for up_groupimg1
-- ----------------------------
DROP TABLE IF EXISTS `up_groupimg1`;
CREATE TABLE `up_groupimg1` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `src` text CHARACTER SET utf8 NOT NULL,
  `width` int(10) NOT NULL,
  `height` int(10) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Records of up_groupimg1
-- ----------------------------

-- ----------------------------
-- Table structure for up_icon
-- ----------------------------
DROP TABLE IF EXISTS `up_icon`;
CREATE TABLE `up_icon` (
  `iconid` int(11) NOT NULL AUTO_INCREMENT COMMENT 'iconID',
  `iconpath` varchar(255) NOT NULL COMMENT 'icon地址',
  PRIMARY KEY (`iconid`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='ICON';

-- ----------------------------
-- Records of up_icon
-- ----------------------------

-- ----------------------------
-- Table structure for up_ips1
-- ----------------------------
DROP TABLE IF EXISTS `up_ips1`;
CREATE TABLE `up_ips1` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `inserttime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `hostip` varchar(20) NOT NULL,
  `time` varchar(25) NOT NULL,
  `fw` varchar(20) NOT NULL,
  `pri` varchar(20) NOT NULL,
  `type` varchar(20) NOT NULL,
  `recorder` varchar(20) NOT NULL,
  `proto` varchar(20) NOT NULL,
  `src` varchar(20) NOT NULL,
  `sport` varchar(20) NOT NULL,
  `dst` varchar(20) NOT NULL,
  `dstaddr` varchar(3000) NOT NULL,
  `dport` varchar(20) NOT NULL,
  `repeats` varchar(20) NOT NULL,
  `msg` varchar(100) NOT NULL,
  `op` varchar(20) NOT NULL,
  `sdev` varchar(20) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Records of up_ips1
-- ----------------------------

-- ----------------------------
-- Table structure for up_log1
-- ----------------------------
DROP TABLE IF EXISTS `up_log1`;
CREATE TABLE `up_log1` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `operation` varchar(300) NOT NULL,
  `username` varchar(300) NOT NULL,
  `state` varchar(30) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Records of up_log1
-- ----------------------------

-- ----------------------------
-- Table structure for up_mailimage1
-- ----------------------------
DROP TABLE IF EXISTS `up_mailimage1`;
CREATE TABLE `up_mailimage1` (
  `imgid` int(11) NOT NULL AUTO_INCREMENT,
  `imgurl` text NOT NULL COMMENT '图片url',
  `inserttime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '上传时间',
  `userid` int(11) NOT NULL COMMENT '上传系统人员id',
  `mailid` int(11) NOT NULL COMMENT '邮件id',
  PRIMARY KEY (`imgid`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=COMPACT COMMENT='邮件内部图片表';

-- ----------------------------
-- Records of up_mailimage1
-- ----------------------------

-- ----------------------------
-- Table structure for up_page
-- ----------------------------
DROP TABLE IF EXISTS `up_page`;
CREATE TABLE `up_page` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `did` int(11) NOT NULL,
  `data` longtext NOT NULL,
  `name` varchar(1000) NOT NULL,
  `imgdata` text NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Records of up_page
-- ----------------------------

-- ----------------------------
-- Table structure for up_pagedir1
-- ----------------------------
DROP TABLE IF EXISTS `up_pagedir1`;
CREATE TABLE `up_pagedir1` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(1000) NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Records of up_pagedir1
-- ----------------------------

-- ----------------------------
-- Table structure for up_permission
-- ----------------------------
DROP TABLE IF EXISTS `up_permission`;
CREATE TABLE `up_permission` (
  `pid` int(11) NOT NULL AUTO_INCREMENT COMMENT '权限表',
  `pname` varchar(255) DEFAULT NULL COMMENT '资源名称',
  `lv` tinyint(2) NOT NULL DEFAULT '2' COMMENT '目录等级(0 主菜单 1 子菜单 3操作)',
  `urlm` varchar(255) DEFAULT NULL COMMENT '命名空间',
  `urlc` varchar(255) DEFAULT NULL COMMENT '控制器',
  `urla` varchar(255) DEFAULT NULL COMMENT '控制器方法',
  `parentid` int(11) NOT NULL DEFAULT '0' COMMENT '父级ID  ',
  `path` varchar(255) NOT NULL DEFAULT '' COMMENT '前端路径',
  `identification` varchar(255) NOT NULL DEFAULT ' ' COMMENT '权限标识',
  `remarks` varchar(255) NOT NULL DEFAULT ' ' COMMENT '备注',
  PRIMARY KEY (`pid`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=179 DEFAULT CHARSET=utf8 COMMENT='权限表';

-- ----------------------------
-- Records of up_permission
-- ----------------------------
INSERT INTO `up_permission` VALUES ('1', '我的可视化', '0', 'index', 'windex', 'index', '0', '', 'my ksh', '我的可视化');
INSERT INTO `up_permission` VALUES ('2', '可视化', '1', '', '', '', '1', 'visual', 'ksh', '可视化');
INSERT INTO `up_permission` VALUES ('4', '轮播管理', '1', null, null, null, '1', 'carouselscreen', '', '');
INSERT INTO `up_permission` VALUES ('5', '数据管理', '0', 'index', 'Datasource', 'dataList', '0', '', '-', '-');
INSERT INTO `up_permission` VALUES ('6', '数据源管理', '1', null, null, null, '5', 'datasource', '', '');
INSERT INTO `up_permission` VALUES ('7', '权限管理', '0', 'index', 'windex', 'index', '0', '', '', '');
INSERT INTO `up_permission` VALUES ('8', '用户管理', '1', 'index', 'windex', 'userlist', '7', 'users', '', '');
INSERT INTO `up_permission` VALUES ('9', '用户组', '1', 'index', 'windex', 'rolelist', '7', 'usergroup', '', '');
INSERT INTO `up_permission` VALUES ('10', '分类管理', '1', 'index', 'windex', 'manageclass', '7', 'classification', '', '');
INSERT INTO `up_permission` VALUES ('12', '操作日志', '1', 'index', 'windex', 'loglist', '7', 'logging', '', '');
INSERT INTO `up_permission` VALUES ('13', '系统管理', '0', null, null, null, '0', '', '', '');
INSERT INTO `up_permission` VALUES ('14', '常规设置', '1', 'index', 'setsystem', 'generalList', '13', 'convent', '', '');
INSERT INTO `up_permission` VALUES ('15', '附件设置', '1', 'index', 'setsystem', 'attachment', '13', 'attach', '', '');
INSERT INTO `up_permission` VALUES ('16', '安全设置', '1', 'index', 'setsystem', 'safeList', '13', 'security', '', '');
INSERT INTO `up_permission` VALUES ('17', '数据库备份', '1', 'index', 'setsystem', 'backupList', '13', 'dbbackup', '', '');
INSERT INTO `up_permission` VALUES ('18', '图片附件', '1', 'index', 'setsystem', 'attList', '13', 'imageattach', '', '');
INSERT INTO `up_permission` VALUES ('19', '账号管理', '0', null, null, null, '0', '', '', '');
INSERT INTO `up_permission` VALUES ('20', '修改密码', '1', '', '', '', '19', 'editpassword', 'test', 'test');
INSERT INTO `up_permission` VALUES ('21', '基本信息', '1', '', '', '', '19', 'basicinfo', '', '');
INSERT INTO `up_permission` VALUES ('22', '获取用户信息', '2', 'index', 'windex', 'getuserlist', '0', '', '', '');
INSERT INTO `up_permission` VALUES ('23', '查询用户组', '3', 'index', 'windex', 'rolelist', '9', '', '', '');
INSERT INTO `up_permission` VALUES ('27', '分类添加', '3', 'index', 'woperating', 'classadd', '10', '', '', '');
INSERT INTO `up_permission` VALUES ('28', '分类删除', '3', 'index', 'woperating', 'classdel', '10', '', '', '');
INSERT INTO `up_permission` VALUES ('29', '分类编辑', '3', 'index', 'woperating', 'rename', '10', '', '', '');
INSERT INTO `up_permission` VALUES ('30', '获取基于用户组的用户和权限', '2', 'index', 'windex', 'permission', '0', '', '', '');
INSERT INTO `up_permission` VALUES ('31', '获取用户组信息', '2', 'index', 'windex', 'getrole', '0', '', '', '');
INSERT INTO `up_permission` VALUES ('32', '获取所有用户信息', '2', 'index', 'windex', 'getuser', '0', '', '', '');
INSERT INTO `up_permission` VALUES ('33', '获取所有权限列表', '2', 'index', 'windex', 'permissionget', '0', '', '', '');
INSERT INTO `up_permission` VALUES ('34', '新增用户组', '3', 'index', 'woperating', 'roleadd', '9', '', '', '');
INSERT INTO `up_permission` VALUES ('35', '删除用户组', '3', 'index', 'woperating', 'roledel', '9', '', '', '');
INSERT INTO `up_permission` VALUES ('36', '修改用户组', '3', 'index', 'woperating', 'roleupd', '9', '', '', '');
INSERT INTO `up_permission` VALUES ('37', '新增用户', '3', 'index', 'woperating', 'useradd', '8', '', '', '');
INSERT INTO `up_permission` VALUES ('38', '删除用户', '3', 'index', 'woperating', 'userdel', '8', '', '', '');
INSERT INTO `up_permission` VALUES ('39', '修改用户', '3', 'index', 'woperating', 'userupdate', '8', '', '', '');
INSERT INTO `up_permission` VALUES ('40', '获取用户组权限', '2', 'index', 'windex', 'rolepermission', '0', '', '', '');
INSERT INTO `up_permission` VALUES ('41', '获取用户组用户', '2', 'index', 'windex', 'roleuser', '0', '', '', '');
INSERT INTO `up_permission` VALUES ('45', '安全设置', '2', 'index', 'woperating', 'safeset', '0', '', '', '');
INSERT INTO `up_permission` VALUES ('46', '获取用户基本信息', '2', 'index', 'woperating', 'getusermsg', '0', '', '', '');
INSERT INTO `up_permission` VALUES ('47', '获取指定用户ID', '2', 'index', 'windex', 'getUserMessage', '0', '', '', '');
INSERT INTO `up_permission` VALUES ('48', '获取所有分类信息', '2', 'index', 'windex', 'getScreenGroup', '0', '', '', '');
INSERT INTO `up_permission` VALUES ('49', '获取首页路径', '2', 'index', 'windex', 'getindexpath', '0', '', ' 获取首页路径', ' 获取首页路径');
INSERT INTO `up_permission` VALUES ('50', '修改系统常规设置', '2', 'index', 'setsystem', 'generalSet', '14', '', ' ', ' ');
INSERT INTO `up_permission` VALUES ('51', '上传logo', '2', 'index', 'setsystem', 'uploadimg', '14', '', ' ', ' ');
INSERT INTO `up_permission` VALUES ('52', '上传附件', '2', 'index', 'setsystem', 'upattachment', '15', '', ' ', ' ');
INSERT INTO `up_permission` VALUES ('53', '安全设置修改', '2', 'index', 'setsystem', 'safe', '16', '', ' ', ' ');
INSERT INTO `up_permission` VALUES ('54', '添加数据库备份', '2', 'index', 'setsystem', 'backup', '17', '', ' ', ' ');
INSERT INTO `up_permission` VALUES ('55', '数据库备份下载', '2', 'index', 'setsystem', 'backupdown', '17', '', ' ', ' ');
INSERT INTO `up_permission` VALUES ('56', '查看大图', '2', 'index', 'setsystem', 'showimg', '18', '', ' ', ' ');
INSERT INTO `up_permission` VALUES ('57', '删除附件', '2', 'index', 'setsystem', 'attdelete', '18', '', ' ', ' ');
INSERT INTO `up_permission` VALUES ('58', '调整分类', '2', 'index', 'datasource', 'grouping', '5', '', ' ', ' ');
INSERT INTO `up_permission` VALUES ('59', '删除数据', '2', 'index', 'datasource', 'deldata', '5', '', ' ', ' ');
INSERT INTO `up_permission` VALUES ('60', '修改', '2', 'index', 'datasource', 'updata', '5', '', ' ', ' ');
INSERT INTO `up_permission` VALUES ('61', '文件上传', '2', 'index', 'datasource', 'uploadFile', '5', '', ' ', ' ');
INSERT INTO `up_permission` VALUES ('62', '添加excel', '2', 'index', 'datasource', 'addexcel', '5', '', ' ', ' ');
INSERT INTO `up_permission` VALUES ('63', '添加API', '2', 'index', 'datasource', 'addapi', '5', '', ' ', ' ');
INSERT INTO `up_permission` VALUES ('64', '测试数据库链接', '2', 'index', 'datasource', 'testlink', '5', '', ' ', ' ');
INSERT INTO `up_permission` VALUES ('65', '添加数据库连接', '2', 'index', 'datasource', 'dbconnect', '5', '', ' ', ' ');
INSERT INTO `up_permission` VALUES ('66', '数据源列表', '2', 'index', 'datasource', 'sourcelist', '5', '', ' ', ' ');
INSERT INTO `up_permission` VALUES ('67', 'sql语句执行接口', '2', 'index', 'datasource', 'query_sql', '5', '', ' ', ' ');
INSERT INTO `up_permission` VALUES ('68', '添加SQL', '2', 'index', 'datasource', 'addsql', '5', '', ' ', ' ');
INSERT INTO `up_permission` VALUES ('69', '生成表格数据', '2', 'index', 'datasource', 'formdata', '5', '', ' ', ' ');
INSERT INTO `up_permission` VALUES ('70', '添加自定义视图', '2', 'index', 'datasource', 'customview', '5', '', ' ', ' ');
INSERT INTO `up_permission` VALUES ('71', '查询数据源类型', '2', 'index', 'datamap', 'datatype', '2', '', ' ', ' ');
INSERT INTO `up_permission` VALUES ('72', '根据数据类型查出来的已有数据', '2', 'index', 'datamap', 'mapdata', '2', '', ' ', ' ');
INSERT INTO `up_permission` VALUES ('73', '返回的数据', '2', 'index', 'datamap', 'returndata', '2', '', ' ', ' ');
INSERT INTO `up_permission` VALUES ('74', '发布数据存入数据库', '2', 'index', 'datamap', 'publish', '2', '', ' ', ' ');
INSERT INTO `up_permission` VALUES ('75', '发布', '2', 'index', 'datamap', 'release', '2', '', ' ', ' ');
INSERT INTO `up_permission` VALUES ('76', '获取分类信息', '2', 'index', 'windex', 'getscreen', '0', '', ' 获取分类信息', ' 获取分类信息');
INSERT INTO `up_permission` VALUES ('77', '获取指定分类大屏', '2', 'index', 'windex', 'getScreenMsg', '0', '', ' ', ' ');
INSERT INTO `up_permission` VALUES ('78', '查询用户', '3', 'index', 'windex', 'userlist', '8', '', ' ', ' ');
INSERT INTO `up_permission` VALUES ('79', '查询分类', '3', 'index', 'windex', 'manageclass', '10', '', ' ', ' ');
INSERT INTO `up_permission` VALUES ('81', '查询日志', '3', 'windex', 'windex', 'loglist', '12', '', ' ', ' ');
INSERT INTO `up_permission` VALUES ('82', '获取登录日志', '2', 'index', 'windex', 'loguserlist', '0', '', ' ', ' ');
INSERT INTO `up_permission` VALUES ('83', '可视化矩阵', '2', 'index', 'index', 'screenDirSummary', '0', '', ' ', ' ');
INSERT INTO `up_permission` VALUES ('84', '新建模板', '2', 'index', 'index', 'categain ', '0', '', ' ', ' ');
INSERT INTO `up_permission` VALUES ('85', '生成模板', '2', 'index', 'index', 'screenInfo ', '0', '', ' ', ' ');
INSERT INTO `up_permission` VALUES ('86', '获取模板', '2', 'index', 'index', 'getScreenInfo', '0', '', ' ', ' ');
INSERT INTO `up_permission` VALUES ('87', '更新模板', '2', 'index', 'index', 'updateScreenInfo', '0', '', ' ', ' ');
INSERT INTO `up_permission` VALUES ('88', '更新封面', '2', 'index', 'index', 'updateScreenCover', '0', '', ' ', ' ');
INSERT INTO `up_permission` VALUES ('89', '新建可视化矩阵', '2', 'index', 'index', 'screenDir', '0', '', ' ', ' ');
INSERT INTO `up_permission` VALUES ('90', '获取可视化矩阵信息', '2', 'index', 'index', 'getScreenDirInfo', '0', '', ' ', ' ');
INSERT INTO `up_permission` VALUES ('91', '修改可视化矩阵', '2', 'index', 'index', 'updateScreenDir', '0', '', ' ', ' ');
INSERT INTO `up_permission` VALUES ('92', '修改可视化矩阵封面', '2', 'index', 'index', 'updateCover', '0', '', ' ', ' ');
INSERT INTO `up_permission` VALUES ('93', '删除可视化', '2', 'index', 'index', 'deleteScreen', '0', '', ' ', ' ');
INSERT INTO `up_permission` VALUES ('94', '删除可视化矩阵', '2', 'index', 'index', 'deleteScreenDir', '0', '', ' ', ' ');
INSERT INTO `up_permission` VALUES ('95', '查询数据管理', '2', 'index', 'datasource', 'datalist', '0', '', ' ', ' ');
INSERT INTO `up_permission` VALUES ('96', '获取安全设置信息', '2', 'index', 'windex', 'safeget', '0', '', ' ', ' ');
INSERT INTO `up_permission` VALUES ('97', '获取可视化矩阵', '2', 'index', 'index', 'screenSummary', '0', '', ' ', ' ');
INSERT INTO `up_permission` VALUES ('98', '修改权限', '2', 'index', 'Woperating', 'roleUpdate', '0', '', ' ', ' ');
INSERT INTO `up_permission` VALUES ('99', '获取权限信息', '2', 'index', 'windex', 'getPermissionMessage', '0', '', ' ', ' ');
INSERT INTO `up_permission` VALUES ('100', '数据备份', '2', 'index', 'setsystem', 'backupdel', '0', '', ' ', ' ');
INSERT INTO `up_permission` VALUES ('101', '获取指定分类信息', '2', 'index', 'windex', 'getScreenSid', '0', '', ' ', ' ');
INSERT INTO `up_permission` VALUES ('102', '获取用户分类', '2', 'index', 'windex', 'getScreenGroupMessage', '0', '', ' ', ' ');
INSERT INTO `up_permission` VALUES ('103', '预览', '2', 'index', 'index', 'getdirscreensummary', '0', '', ' ', ' ');
INSERT INTO `up_permission` VALUES ('145', '修改大屏名字', '2', 'index', 'index', 'updateScreenName', '0', '', ' ', ' ');
INSERT INTO `up_permission` VALUES ('146', '复制大屏', '2', 'index', 'index', 'copyScreen', '0', '', ' ', ' ');
INSERT INTO `up_permission` VALUES ('147', '数据库连接', '1', 'index', 'databasesource', 'getdatabase', '5', 'dbconnect', ' ', ' ');
INSERT INTO `up_permission` VALUES ('148', '我的发布', '1', 'index', 'screen', 'releaselist', '1', 'release', ' ', ' ');
INSERT INTO `up_permission` VALUES ('149', '添加CSV文件', '2', 'index', 'datament', 'addCsv', '0', '', ' ', ' ');
INSERT INTO `up_permission` VALUES ('150', '修改csv', '2', 'index', 'datament', 'updateCsv', '0', '', ' ', ' ');
INSERT INTO `up_permission` VALUES ('151', '删除数据', '2', 'index', 'datament', 'deldata', '0', '', ' ', ' ');
INSERT INTO `up_permission` VALUES ('152', '数据管理数据列表', '2', 'index', 'datament', 'datalist', '0', '', ' ', ' ');
INSERT INTO `up_permission` VALUES ('153', 'http代理', '2', null, null, null, '0', '', ' ', ' ');
INSERT INTO `up_permission` VALUES ('154', '可视化矩阵修改名字', '2', 'index', 'index', 'updateScreenDirName', '0', '', ' ', ' ');
INSERT INTO `up_permission` VALUES ('159', '查询用户路由', '2', 'index', 'windex', 'userRouting', '0', '', ' ', ' ');
INSERT INTO `up_permission` VALUES ('160', '查看指定大屏', '2', 'index', 'index', 'singlescreenSummary', '0', '', ' ', ' ');
INSERT INTO `up_permission` VALUES ('161', '修改用户', '2', 'index', 'Woperating', 'updateBasicMessage', '0', '', ' ', ' ');
INSERT INTO `up_permission` VALUES ('163', '创建模板', '2', 'index', 'template', 'templateInfo', '3', '', ' ', ' 添加模板');
INSERT INTO `up_permission` VALUES ('164', '模板列表', '2', 'index', 'template', 'templateSummary', '3', '', ' ', ' 模板列表');
INSERT INTO `up_permission` VALUES ('165', '删除模板', '2', 'index', 'template', 'deleteTemplate', '3', '', ' ', ' 删除模板');
INSERT INTO `up_permission` VALUES ('166', '修改模板', '2', 'index', 'template', 'updateTemplate', '0', '', ' ', ' 修改模板');
INSERT INTO `up_permission` VALUES ('167', '删除背景图片', '2', 'index', 'index', 'deleteBackground', '0', '', ' ', ' ');
INSERT INTO `up_permission` VALUES ('169', '修改模板封面', '2', 'index', 'template', 'updateTemplateCover', '0', '', ' ', ' ');
INSERT INTO `up_permission` VALUES ('170', '修改矩阵封面', '2', 'index', 'template', 'updateCover', '0', '', ' ', ' ');
INSERT INTO `up_permission` VALUES ('172', '获取用户基本信息', '2', 'index', 'windex', 'getUserMsg', '0', '', ' ', ' ');
INSERT INTO `up_permission` VALUES ('173', '附件设置查询', '2', 'index', 'setSystem', 'getAttachment', '15', '', ' ', ' ');
INSERT INTO `up_permission` VALUES ('174', '导出本地部署', '2', 'index', 'setSystem', 'localDeploy', '148', '', ' ', ' ');
INSERT INTO `up_permission` VALUES ('176', '保存为模板', '2', 'index', 'index', 'savetmp', '3', '', ' ', ' ');
INSERT INTO `up_permission` VALUES ('177', '分享模板', '2', 'index', 'template', 'shareTemplate', '3', '', ' ', ' ');
INSERT INTO `up_permission` VALUES ('178', '恢复默认模板', '2', 'index', 'template', 'resetTemplate', '3', '', ' ', ' ');

-- ----------------------------
-- Table structure for up_publish
-- ----------------------------
DROP TABLE IF EXISTS `up_publish`;
CREATE TABLE `up_publish` (
  `pid` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `scid` int(11) NOT NULL COMMENT '屏幕ID',
  `sid` int(11) DEFAULT '1' COMMENT '分类ID',
  `uid` int(11) NOT NULL COMMENT '添加发布用户ID',
  `viewsnum` int(11) NOT NULL DEFAULT '0' COMMENT '浏览次数',
  `ispuh` tinyint(2) NOT NULL DEFAULT '1' COMMENT '是否发布(0不发布,1发布)',
  `sname` varchar(255) DEFAULT NULL COMMENT '发布名称',
  `is_pwd` tinyint(4) DEFAULT NULL COMMENT '是否使用密码 1：是，0：否',
  `img` varchar(255) DEFAULT NULL COMMENT '封面图片',
  `token` varchar(255) DEFAULT NULL COMMENT 'token',
  `password` varchar(255) DEFAULT NULL COMMENT '密码',
  `expiredate` int(11) NOT NULL DEFAULT '0' COMMENT '过期时间',
  `ptype` tinyint(2) NOT NULL DEFAULT '1' COMMENT '发布类型(1实时画面,2历史快照)',
  `pdata` text,
  `shid` int(11) DEFAULT '0' COMMENT '快照关联ID',
  `link` varchar(255) NOT NULL COMMENT '发布链接',
  `createtime` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `extype` tinyint(1) DEFAULT '0' COMMENT '过期状态(1是过期，0是没过期)',
  `acid` text COMMENT '用于爱创用户标识',
  `localdeploy` tinyint(2) NOT NULL DEFAULT '0' COMMENT '是否导出(0为不导出 1位导出)',
  PRIMARY KEY (`pid`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='发布信息表';

-- ----------------------------
-- Records of up_publish
-- ----------------------------

-- ----------------------------
-- Table structure for up_restorechart
-- ----------------------------
DROP TABLE IF EXISTS `up_restorechart`;
CREATE TABLE `up_restorechart` (
  `rid` int(11) NOT NULL AUTO_INCREMENT,
  `screencharttconfig` text NOT NULL,
  `tid` int(11) NOT NULL,
  `screenid` int(11) NOT NULL,
  `screenchart` text NOT NULL,
  PRIMARY KEY (`rid`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Records of up_restorechart
-- ----------------------------

-- ----------------------------
-- Table structure for up_restorescreen
-- ----------------------------
DROP TABLE IF EXISTS `up_restorescreen`;
CREATE TABLE `up_restorescreen` (
  `screenid` int(11) NOT NULL COMMENT '大屏id',
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sid` int(11) NOT NULL,
  `data` longtext,
  `name` varchar(1000) NOT NULL,
  `imgdata` text COMMENT '背景',
  `sdi` int(11) DEFAULT '1' COMMENT '关联分类名字',
  `publish` tinyint(2) DEFAULT '0' COMMENT '是否发布( 0 未发布  1已发布)',
  `lock` tinyint(2) NOT NULL DEFAULT '0' COMMENT '是否加密(未加密 0 已加密 1)',
  `screentype` tinyint(2) NOT NULL DEFAULT '0' COMMENT '大屏类型(0 普通大屏 1 发布快照 2 大屏模板)',
  `image` text COMMENT '大屏图',
  `thumbnail` text COMMENT '缩略图',
  `src` text COMMENT '原图',
  `ratio` varchar(255) DEFAULT NULL COMMENT '屏幕比例',
  `pixel` varchar(255) DEFAULT NULL COMMENT '模板屏幕大小',
  `password` varchar(255) DEFAULT NULL COMMENT '密码',
  `publishuser` varchar(255) NOT NULL DEFAULT '0' COMMENT '发布用户id',
  `tempcate` int(11) DEFAULT NULL COMMENT '0为通用模板,1为自定义模板',
  `share` int(11) DEFAULT NULL COMMENT '是否分享模板 0为未分享 1为分享',
  `screen_sid` int(11) DEFAULT NULL COMMENT '模版关联id',
  `tmp` int(11) DEFAULT NULL COMMENT '是否为默认模版，1为默认模板',
  `createtime` varchar(11) NOT NULL COMMENT '创建时间',
  `updatetime` varchar(11) NOT NULL COMMENT '修改时间',
  `delete_time` char(50) DEFAULT NULL COMMENT '软删除使用',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='大屏列表';

-- ----------------------------
-- Records of up_restorescreen
-- ----------------------------

-- ----------------------------
-- Table structure for up_role
-- ----------------------------
DROP TABLE IF EXISTS `up_role`;
CREATE TABLE `up_role` (
  `rid` int(11) NOT NULL AUTO_INCREMENT COMMENT '角色ID',
  `rolename` varchar(255) DEFAULT NULL COMMENT '角色名',
  `createtime` int(11) DEFAULT NULL,
  `updatetime` int(11) DEFAULT NULL,
  PRIMARY KEY (`rid`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='角色表';

-- ----------------------------
-- Records of up_role
-- ----------------------------
INSERT INTO `up_role` VALUES ('1', '超级管理员', '1533871347', null);
INSERT INTO `up_role` VALUES ('30', '普通用户', '1571209039', null);

-- ----------------------------
-- Table structure for up_role_permission
-- ----------------------------
DROP TABLE IF EXISTS `up_role_permission`;
CREATE TABLE `up_role_permission` (
  `rpid` int(11) NOT NULL AUTO_INCREMENT COMMENT '角色权限ID',
  `rid` int(11) DEFAULT NULL COMMENT '角色ID',
  `pid` varchar(1000) DEFAULT '' COMMENT '权限ID',
  PRIMARY KEY (`rpid`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='角色权限表';

-- ----------------------------
-- Records of up_role_permission
-- ----------------------------
INSERT INTO `up_role_permission` VALUES ('1', '1', '1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,109,110,112,111,147,148');
INSERT INTO `up_role_permission` VALUES ('30', '30', '1,2,4,5,6,19,20,21,22,23,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,45,46,47,48,49,50,51,52,53,54,55,56,57,58,59,60,61,62,63,64,65,66,67,68,69,70,71,72,73,74,75,76,77,78,79,81,82,83,84,85,86,87,88,89,90,91,92,93,94,95,96,97,98,99,100,101,102,103,145,146,147,148,149,150,151,152,153,154,159,160,161,163,164,165,166,167,169,170,172,173,174,176,177,178,13,18');

-- ----------------------------
-- Table structure for up_safety
-- ----------------------------
DROP TABLE IF EXISTS `up_safety`;
CREATE TABLE `up_safety` (
  `said` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `maxerr` int(8) NOT NULL COMMENT '最大错误登录次数',
  `intervaltime` varchar(50) NOT NULL COMMENT '时间间隔',
  `terminal` int(8) NOT NULL COMMENT '最大允许登录终端',
  `adminlog` tinyint(4) NOT NULL COMMENT '是否开启管理日志（1：开启，0：不开启）',
  `login` tinyint(4) NOT NULL COMMENT '是否允许多人登录（1：允许，0：不允许）',
  PRIMARY KEY (`said`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='用户设置信息表';

-- ----------------------------
-- Records of up_safety
-- ----------------------------
INSERT INTO `up_safety` VALUES ('1', '6', '60', '4000', '1', '1');

-- ----------------------------
-- Table structure for up_screen
-- ----------------------------
DROP TABLE IF EXISTS `up_screen`;
CREATE TABLE `up_screen` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sid` int(11) NOT NULL,
  `data` longtext,
  `name` varchar(1000) NOT NULL,
  `imgdata` text COMMENT '背景',
  `sdi` int(11) DEFAULT '1' COMMENT '关联分类名字',
  `publish` tinyint(2) DEFAULT '0' COMMENT '是否发布( 0 未发布  1已发布)',
  `lock` tinyint(2) NOT NULL DEFAULT '0' COMMENT '是否加密(未加密 0 已加密 1)',
  `screentype` tinyint(2) NOT NULL DEFAULT '0' COMMENT '大屏类型(0 普通大屏 1 发布快照 2 大屏模板)',
  `image` text COMMENT '大屏图',
  `thumbnail` text COMMENT '缩略图',
  `src` text COMMENT '原图',
  `ratio` varchar(255) DEFAULT NULL COMMENT '屏幕比例',
  `pixel` varchar(255) DEFAULT NULL COMMENT '模板屏幕大小',
  `password` varchar(255) DEFAULT NULL COMMENT '密码',
  `publishuser` varchar(255) NOT NULL DEFAULT '0' COMMENT '发布用户id',
  `tempcate` int(11) DEFAULT NULL COMMENT '0为通用模板,1为自定义模板',
  `share` int(11) DEFAULT NULL COMMENT '是否分享模板 0为未分享 1为分享',
  `screen_sid` int(11) DEFAULT NULL COMMENT '模板关联大屏id',
  `tmp` int(11) DEFAULT NULL COMMENT '是否为默认模板，1为默认模板',
  `createtime` varchar(11) NOT NULL COMMENT '创建时间',
  `updatetime` varchar(11) NOT NULL COMMENT '修改时间',
  `delete_time` char(50) DEFAULT NULL COMMENT '软删除使用',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='大屏列表';

-- ----------------------------
-- Records of up_screen
-- ----------------------------

-- ----------------------------
-- Table structure for up_screenchart
-- ----------------------------
DROP TABLE IF EXISTS `up_screenchart`;
CREATE TABLE `up_screenchart` (
  `tid` int(11) NOT NULL AUTO_INCREMENT COMMENT '图表ID',
  `screenid` int(11) DEFAULT NULL COMMENT '大屏ID',
  `talias` varchar(255) DEFAULT NULL COMMENT '别名',
  `tname` varchar(255) DEFAULT NULL COMMENT '图表名字',
  `tdata` text COMMENT '图表数据',
  `islock` tinyint(2) NOT NULL DEFAULT '0' COMMENT '是否锁定(0不锁定 1 锁定)',
  `link` varchar(255) DEFAULT NULL COMMENT '图标链接',
  `collection` tinyint(2) NOT NULL DEFAULT '0' COMMENT '收藏',
  `ishide` tinyint(2) NOT NULL DEFAULT '0' COMMENT '是否隐藏(0不隐藏 1 隐藏)',
  `position` int(11) NOT NULL DEFAULT '0' COMMENT '图表定位',
  `createtime` int(11) NOT NULL COMMENT '图表创建时间',
  `updatetime` int(11) NOT NULL COMMENT '图表修改时间',
  `autoupdatetime` int(11) DEFAULT NULL COMMENT '自动刷新开始时间',
  `daid` int(11) DEFAULT NULL,
  PRIMARY KEY (`tid`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='大屏图表';

-- ----------------------------
-- Records of up_screenchart
-- ----------------------------

-- ----------------------------
-- Table structure for up_screencharttconfig
-- ----------------------------
DROP TABLE IF EXISTS `up_screencharttconfig`;
CREATE TABLE `up_screencharttconfig` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `tid` int(11) DEFAULT NULL COMMENT '图表id',
  `screenid` int(11) DEFAULT NULL COMMENT '大屏id',
  `chartData` text COMMENT '图表数据',
  `charttype` varchar(50) NOT NULL COMMENT '图表类型',
  `chartSourceType` varchar(255) DEFAULT NULL COMMENT '图表数据来源来行',
  `collection` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否收藏',
  `comkey` varchar(50) DEFAULT NULL COMMENT '组件的资源识别类型',
  `comtype` varchar(50) DEFAULT NULL COMMENT '组件的分类',
  `dataOpt` text COMMENT '组件的数据配置',
  `height` double DEFAULT NULL COMMENT '高度',
  `ishide` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否隐藏',
  `islock` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否锁定',
  `key` varchar(50) DEFAULT NULL COMMENT '组件在当前大屏的可识别的别名',
  `name` varchar(255) NOT NULL COMMENT '图表的名字',
  `resizable` tinyint(1) NOT NULL COMMENT '组件能否被改变大小和位置',
  `showBorder` tinyint(1) NOT NULL DEFAULT '0' COMMENT '显示边框',
  `width` double NOT NULL COMMENT '图表的宽度',
  `x` double NOT NULL DEFAULT '0' COMMENT '图表的x坐标',
  `y` double NOT NULL DEFAULT '0' COMMENT '图表的y坐标',
  `selectDaid` int(11) DEFAULT NULL,
  `drilling` varchar(255) DEFAULT NULL COMMENT '下钻',
  `parenttid` int(11) DEFAULT NULL COMMENT '下钻父级tid',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Records of up_screencharttconfig
-- ----------------------------

-- ----------------------------
-- Table structure for up_screendir
-- ----------------------------
DROP TABLE IF EXISTS `up_screendir`;
CREATE TABLE `up_screendir` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sid` int(11) NOT NULL,
  `name` varchar(1000) NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `data` longtext,
  `imgdata` varchar(255) DEFAULT NULL,
  `screenid` varchar(255) DEFAULT NULL COMMENT '大屏矩阵关联的大屏ID',
  `createtime` int(11) DEFAULT '0' COMMENT '创建时间',
  `updatetime` int(11) DEFAULT '0' COMMENT '修改时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Records of up_screendir
-- ----------------------------
INSERT INTO `up_screendir` VALUES ('1', '1', '可视化矩阵', '2019-07-26 14:23:05', '{\"option\":{\"width\":1920,\"height\":1080,\"scale\":4},\"screenList\":[{\"key\":\"matrx5d65G7GIUl0RF\",\"width\":1920,\"height\":1079,\"maxWidth\":5000,\"maxHeight\":5000,\"x\":0,\"y\":0,\"imgdata\":\"/Cover/3b0ad3ac18b9a7207ef9cd10b550a2dc_image.jpg\",\"id\":61,\"name\":\"测试副本\"}]}', '//Cover/137cfd38d1855a3b57c16255a03def41_image.jpg', ',61,', '1564121877', '1564122129');
INSERT INTO `up_screendir` VALUES ('2', '1', '矩阵', '2019-07-26 14:18:18', null, null, null, '1564121898', '0');

-- ----------------------------
-- Table structure for up_screengroup
-- ----------------------------
DROP TABLE IF EXISTS `up_screengroup`;
CREATE TABLE `up_screengroup` (
  `sid` int(11) NOT NULL AUTO_INCREMENT COMMENT '屏幕分类',
  `uid` varchar(88) NOT NULL DEFAULT '' COMMENT '关联用户ID',
  `screenname` varchar(255) NOT NULL,
  `number` tinyint(2) DEFAULT '0' COMMENT '分类中成员个数',
  `remarks` varchar(255) DEFAULT NULL COMMENT '备注',
  PRIMARY KEY (`sid`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='屏幕分类';

-- ----------------------------
-- Records of up_screengroup
-- ----------------------------
INSERT INTO `up_screengroup` VALUES ('1', '', '默认分类', '0', '默认分类');

-- ----------------------------
-- Table structure for up_system
-- ----------------------------
DROP TABLE IF EXISTS `up_system`;
CREATE TABLE `up_system` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `hostip` varchar(300) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Records of up_system
-- ----------------------------

-- ----------------------------
-- Table structure for up_systemset
-- ----------------------------
DROP TABLE IF EXISTS `up_systemset`;
CREATE TABLE `up_systemset` (
  `sysid` int(11) NOT NULL AUTO_INCREMENT COMMENT '系统id',
  `sysname` varchar(100) NOT NULL COMMENT '系统名称',
  `website` varchar(100) NOT NULL COMMENT '网址',
  `port` int(8) NOT NULL COMMENT '端口',
  `logopath` varchar(100) NOT NULL COMMENT 'logo路径',
  `publish` tinyint(4) NOT NULL COMMENT '是否发布（1：发布，0：不发布）',
  PRIMARY KEY (`sysid`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='系统设置';

-- ----------------------------
-- Records of up_systemset
-- ----------------------------
INSERT INTO `up_systemset` VALUES ('1', '可视化系统', 'http://127_0_0_1:8080', '8080', 'http://v_kwcnet_com/uploads/logo/20180817/1cc773b1570c1befc8b524e795f07011_png', '1');

-- ----------------------------
-- Table structure for up_template
-- ----------------------------
DROP TABLE IF EXISTS `up_template`;
CREATE TABLE `up_template` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `updatetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `name` varchar(100) NOT NULL,
  `ratio` varchar(30) DEFAULT NULL COMMENT '比例',
  `pixel` varchar(40) DEFAULT NULL COMMENT '屏幕大小',
  `src` text COMMENT '原图',
  `thumbnail` text COMMENT '缩略图',
  `image` text COMMENT '大屏图',
  `data` text COMMENT '数据',
  `sid` tinyint(2) DEFAULT NULL COMMENT '模板类型',
  `createtime` int(11) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='可视化模板';

-- ----------------------------
-- Records of up_template
-- ----------------------------

-- ----------------------------
-- Table structure for up_token
-- ----------------------------
DROP TABLE IF EXISTS `up_token`;
CREATE TABLE `up_token` (
  `tid` int(11) NOT NULL AUTO_INCREMENT COMMENT '用户登录记录表',
  `uid` int(11) DEFAULT NULL COMMENT '用户id',
  `token` varchar(255) DEFAULT NULL COMMENT 'token',
  `tokentime` int(11) DEFAULT NULL COMMENT 'token过期时间',
  `few` int(2) DEFAULT NULL COMMENT '第几个登录的',
  PRIMARY KEY (`tid`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='用户登录记录表';

-- ----------------------------
-- Records of up_token
-- ----------------------------

-- ----------------------------
-- Table structure for up_tryoutuser
-- ----------------------------
DROP TABLE IF EXISTS `up_tryoutuser`;
CREATE TABLE `up_tryoutuser` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(300) NOT NULL,
  `token` varchar(300) NOT NULL,
  `deadline` int(11) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Records of up_tryoutuser
-- ----------------------------

-- ----------------------------
-- Table structure for up_unity
-- ----------------------------
DROP TABLE IF EXISTS `up_unity`;
CREATE TABLE `up_unity` (
  `unityid` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT '默认文件名',
  `filename` varchar(255) NOT NULL COMMENT '真实文件名',
  `path` varchar(255) NOT NULL COMMENT '文件路径',
  `version` int(11) NOT NULL COMMENT '版本号',
  `createtime` int(11) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`unityid`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='上传的3D模型';

-- ----------------------------
-- Records of up_unity
-- ----------------------------
INSERT INTO `up_unity` VALUES ('1', '11', '11-10.xlsx', '/unity/11-10.xlsx', '10', '1559102481');

-- ----------------------------
-- Table structure for up_unityjson
-- ----------------------------
DROP TABLE IF EXISTS `up_unityjson`;
CREATE TABLE `up_unityjson` (
  `jsonid` int(11) NOT NULL AUTO_INCREMENT,
  `jsonname` text COMMENT '名字',
  `createtime` int(11) NOT NULL,
  PRIMARY KEY (`jsonid`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='上传的3DJSON';

-- ----------------------------
-- Records of up_unityjson
-- ----------------------------

-- ----------------------------
-- Table structure for up_upattachment
-- ----------------------------
DROP TABLE IF EXISTS `up_upattachment`;
CREATE TABLE `up_upattachment` (
  `upid` int(11) NOT NULL AUTO_INCREMENT COMMENT '附件id',
  `thumb` varchar(255) NOT NULL COMMENT '缩略图',
  `url` varchar(255) NOT NULL COMMENT '图片地址',
  `waterurl` varchar(255) NOT NULL COMMENT '水印大图',
  `warterthumb` varchar(255) NOT NULL COMMENT '水印缩略图',
  PRIMARY KEY (`upid`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='大屏图片';

-- ----------------------------
-- Records of up_upattachment
-- ----------------------------

-- ----------------------------
-- Table structure for up_user
-- ----------------------------
DROP TABLE IF EXISTS `up_user`;
CREATE TABLE `up_user` (
  `uid` int(11) NOT NULL AUTO_INCREMENT COMMENT '用户表',
  `username` varchar(255) NOT NULL COMMENT '用户名',
  `salt` int(11) NOT NULL COMMENT '盐',
  `password` varchar(255) NOT NULL COMMENT '密码',
  `email` varchar(255) DEFAULT NULL COMMENT '邮箱',
  `realname` varchar(80) DEFAULT '' COMMENT '真实姓名',
  `createtime` int(11) DEFAULT '0' COMMENT '创建时间',
  `online` tinyint(5) NOT NULL DEFAULT '0' COMMENT '登录状态（同时在线人数）',
  `logins` tinyint(5) DEFAULT '1' COMMENT '同时登录个数',
  `status` tinyint(3) DEFAULT '0' COMMENT '登录锁',
  `locktime` int(11) DEFAULT NULL COMMENT '锁定时间',
  `error` tinyint(5) DEFAULT '0' COMMENT '密码错误次数',
  `lastlogintime` int(11) DEFAULT '0' COMMENT '最后登录时间',
  `openlog` tinyint(2) DEFAULT '0' COMMENT '是否开启日志',
  `locktimeset` int(11) DEFAULT '7200' COMMENT '锁定时间长度',
  `maxerr` tinyint(3) DEFAULT '5' COMMENT '登录最大错误次数',
  `phone` varchar(22) DEFAULT '' COMMENT '联系电话',
  `avatar` varchar(255) DEFAULT '/uploads/staticimg/perview_avatar_2.png' COMMENT '头像',
  `address` varchar(255) DEFAULT '' COMMENT '地址',
  `sid` varchar(255) NOT NULL DEFAULT ' ',
  `external_id` int(11) DEFAULT NULL COMMENT '外部用户ID',
  PRIMARY KEY (`uid`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='用户表';

-- ----------------------------
-- Records of up_user
-- ----------------------------
INSERT INTO `up_user` VALUES ('1', 'admin', '7732876', 'd2fb0527777f9b2614c4275b78908938', '12323SW@qq.com', 'admin', '1554392837', '34', '1', '0', '0', '0', '1571620148', '0', '7200', '5', '15323243242', '/uploads/staticimg/perview_avatar_2.png', '北京海淀区', '1,2,3,2,3,7,11,12,13,17,18,19,20,21,22,23', null);

-- ----------------------------
-- Table structure for up_userlog
-- ----------------------------
DROP TABLE IF EXISTS `up_userlog`;
CREATE TABLE `up_userlog` (
  `lid` int(11) NOT NULL AUTO_INCREMENT COMMENT '日志ID',
  `uid` int(11) NOT NULL COMMENT '用户ID',
  `username` varchar(80) DEFAULT NULL COMMENT '用户名',
  `realname` varchar(80) DEFAULT NULL COMMENT '真实姓名',
  `userrole` varchar(80) DEFAULT NULL COMMENT '用户角色',
  `lastlogintime` int(11) DEFAULT NULL COMMENT '用户最后一次登录',
  `ip` varchar(20) NOT NULL COMMENT '操作ip',
  `operating` varchar(255) DEFAULT NULL COMMENT '操作内容',
  `type` tinyint(2) NOT NULL DEFAULT '0' COMMENT '(1为显示登录2为显示操作0位都显示)',
  `status` tinyint(2) NOT NULL DEFAULT '1',
  `rid` int(11) NOT NULL COMMENT '角色id',
  PRIMARY KEY (`lid`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='用户日志';

-- ----------------------------
-- Records of up_userlog
-- ----------------------------

-- ----------------------------
-- Table structure for up_user_role
-- ----------------------------
DROP TABLE IF EXISTS `up_user_role`;
CREATE TABLE `up_user_role` (
  `ur_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '用户角色ID',
  `uid` int(11) DEFAULT NULL COMMENT '用户ID',
  `rid` int(11) DEFAULT NULL COMMENT '角色ID',
  PRIMARY KEY (`ur_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='用户角色';

-- ----------------------------
-- Records of up_user_role
-- ----------------------------
INSERT INTO `up_user_role` VALUES ('2', '1', '1');

-- ----------------------------
-- Table structure for up_variable
-- ----------------------------
DROP TABLE IF EXISTS `up_variable`;
CREATE TABLE `up_variable` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `variable` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Records of up_variable
-- ----------------------------
