## 云组件中心模块说明

### 开发规范

#### HTML

- 中划线命名 eg: class="user-list"
- 语义化 易读性 eg: user, list 不要aa,bb,cc类似的
- 少用浮动和定位 : 使用的flex
- 行内元素禁止嵌套块级元素，特殊情况除外
- 表格形式的布局尽量用 table
- 列表形式的布局尽量用 ul,li
- 重复的HTMl，写成组件，增加复用性
- 尽量每一个模块都要有注释
- 尽量 不用id
- 能用字体的不要使用图片

### css

- css选择器最好不要嵌套超过4层
- 多用css3的属性
- 每一个模块尽量注释
- 命名语义化和中划线形式
- 预编译器采用less
- 重复性的样式写成函数
- 组件内部的样式比较多的，写成外部样式，引入

### JavaScript

- js语法统一使用es6的语法
- 变量命名用小驼峰命名 eg：userList
- 组件统一采用大驼峰命名 eg: UserList
- 变量声明用let
- 所用的常量都要用const定义在使用
- 函数使用箭头函数

## 插件开发指导

- 配置：命名为: plugin.xml

```
<?xml version="1.0" encoding="UTF-8"?>
<calcModuleDef>
	<moduleId>test_clound_compontent</moduleId>
	<moduleVersion>1.0</moduleVersion>
	<moduleWriter>作者名称</moduleWriter>
	<moduleTime>2020-07-19</moduleTime>
	<moduleDesc>测试组件</moduleDesc>
	<modulePicture>preview.png</modulePicture>
	<modulePreview>index.html</modulePreview>
	<moduleClassRouter>index.js</moduleClassRouter>
	<moduleTestData>testData.json</moduleTestData>
	<moduleMethodName>TestBar</moduleMethodName>
	<publicJavascriptImport></publicJavascriptImport>
	<publicCssImport></publicCssImport>
	<javascriptImport>
		<src>public/echarts.js</src>
		<src>public/jquery.min.js</src>
	</javascriptImport>
	<cssImport>
		<src>public/index.css</src>
	</cssImport>
	<moduleDataSet>
		<x>
			<allowed>true</allowed>
			<inputNumber>1</inputNumber>
			<isNull>false</isNull>
			<description>请选择数据的字段</description>
			<axisType>category</axisType>
		</x>
		<y>
			<allowed>true</allowed>
			<inputNumber>n</inputNumber>
			<isNull>false</isNull>
			<description>请选择数据的字段</description>
			<axisType>value</axisType>
		</y>
	</moduleDataSet>
	<moduleParam>
		<param>
			<id>xEn</id>
			<desc>x轴单位</desc>
			<inputType>textbox</inputType>
			<defaultValue></defaultValue>
			<remark>x轴单位</remark>
		</param>
		<param>
			<id>yEn</id>
			<desc>y轴单位</desc>
			<inputType>textbox</inputType>
			<defaultValue></defaultValue>
			<remark>y轴单位</remark>
		</param>
	</moduleParam>
</calcModuleDef>
```

- html: 命名为index.html

  示例：

  ```html
  <!DOCTYPE html>
  <meta charset="utf-8">
  
  <head>
  	<title></title>
  	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
  	<script type="text/javascript" src="public/echarts.js"></script>
  	<link rel="stylesheet" href="public/index.css">
  	<script type="text/javascript" src="index.js"></script>
  </head>
  <style>
  	#test {
  		width: 1000px;
  		height: 500px;
  	}
  </style>
  
  <body>
  	<div id="test"></div>
  </body>
  <script>
  	const option = {
  		el: 'test',
  		params: [{
  			"id": "xEn",
  			"desc": "x轴单位",
  			"inputType": "textbox",
  			"defaultValue": "城市",
  			"remark": "x轴单位"
  		}, {
  			"id": "yEn",
  			"desc": "y轴单位",
  			"inputType": "textbox",
  			"defaultValue": "人数",
  			"remark": "y轴单位"
  		}],
  		dataset: [{
  			"疑似病例": 726,
  			"治疗数量": 841,
  			"城市(1)": "北京",
  			"感染数(1)": 658,
  			"疑似病例(1)": 193,
  			"治疗数量(1)": 203,
  			"x": "北京",
  			"y": 713
  		}, {
  			"疑似病例": 280,
  			"治疗数量": 717,
  			"城市(1)": "武汉",
  			"感染数(1)": 440,
  			"疑似病例(1)": 480,
  			"治疗数量(1)": 167,
  			"x": "武汉",
  			"y": 778
  		}, {
  			"疑似病例": 749,
  			"治疗数量": 325,
  			"城市(1)": "上海",
  			"感染数(1)": 534,
  			"疑似病例(1)": 426,
  			"治疗数量(1)": 389,
  			"x": "上海",
  			"y": 124
  		}, {
  			"疑似病例": 695,
  			"治疗数量": 778,
  			"城市(1)": "南京",
  			"感染数(1)": 577,
  			"疑似病例(1)": 682,
  			"治疗数量(1)": 595,
  			"x": "南京",
  			"y": 881
  		}, {
  			"疑似病例": 879,
  			"治疗数量": 883,
  			"城市(1)": "杭州",
  			"感染数(1)": 486,
  			"疑似病例(1)": 357,
  			"治疗数量(1)": 680,
  			"x": "杭州",
  			"y": 546
  		}, {
  			"疑似病例": 978,
  			"治疗数量": 548,
  			"城市(1)": "深圳",
  			"感染数(1)": 126,
  			"疑似病例(1)": 442,
  			"治疗数量(1)": 508,
  			"x": "深圳",
  			"y": 798
  		}]
  	}
  	window.onload = () => {
  		const chart = new window['TestBar'](option)
  		chart.init()
  	}
  </script>
  ```

- JavaScript: 根据plugin.xml进行配置

- CSS:根据plugin.xml进行配置

- 静态数据：

- 预览图：根据plugin.xml进行配置

- 公共资源库：

  1. 发行包中public目录下CommonPlugins目录为公共插件资源

  2. 已存在的有echarts、jquery

  3. 用法示例
	
     ```html
   <!-- 此时应在插件目录新增public目录并放入echarts -->
     <script type="text/javascript" src="public/echarts.js"></script>
   <!-- 改为如下代码即可 -->
     <script type="text/javascript" src="/CommonPlugins/echarts2.1/echarts.js"></script>
     ```
  
  可用的公共资源如下：
  
  echarts4.8
  
  ```html
  <script type="text/javascript" src="/CommonPlugins/echarts4.8/echarts.min.js"></script>
  ```
  
  echarts5.0
  
  ```html
  <script type="text/javascript" src="/CommonPlugins/echarts5.0/echarts.min.js"></script>
  ```
  
  jquery3.5
  
  ```html
  <script type="text/javascript" src="/CommonPlugins/jquery3.5/jquery.min.js"></script>
  ```

**请注意在使用公共资源时一定要保持plugin.xml与index.html保持一致**

