<?php
/** 
 *  类修改
 */
namespace app\addons\cache\hook;

class Chartdata
{
    /**
     * 主程序用于类型判断及输出响应数据
     * $type int 图表类型
     * $data array 数据 (不能为空)
     */
    public static function index(string $type  , array $data, $chartName)
    {
        //声明图表类型变量
        $charttype = config('chartType.'.$type);
        //将数据传入处理方法内
        // if (!$charttype)  return $chartName.'未定义的类型';
        if (!$charttype)  return $data;
        $chartData = self::$charttype($data);
        return $chartData ?: $chartName.'数据映射有误！！！';
    }

    /**
     * table
     */
    public static function table( array $data)
    {
        //声明表头数组
        $fields = [];
        //声明表数据数组
        $tabledata = [];
        //遍历数组
        foreach( $data as $value) {
             //判断value 中是否有label和data
             if(!isset($value['label']) || !isset($value['data'])) {
                //如果没设置name和value则跳出本次循环
                continue;
            }
            //将表头加入到表头数组                   
            if(!in_array($value['label'],$fields)) {
                $fields[] = $value['label'];
            }
            $tabledata[$value['row']]["$value[label]"] = $value['data'];
        
        }
        //表头数组变为关联数组
        // $fields = self::toIndexArr($fields);
        $newfields = [];
        $i = 0 ;
        //表头取存为二维数组
        foreach($fields as $value) {
            $newfields[$i]['label'] = $value;
            $newfields[$i]['prop'] = $value;
            $i++;
        }
        //数据数组变为关联数组
        $tabledata = self::toIndexArr($tabledata);
        //将两个数组组成一个数组
        $result = [['label' => $newfields ,'data' => $tabledata]];
        //返回数组
        return $result;
        
        
    }

    //轮播表格
    public static function lunbotable( array $data)
    {
        
        //声明表头数组
        $fields = [];
        //声明表数据数组
        $tabledata = [];
        //遍历数组 获取$fields
        foreach( $data as $value) {
             //判断value 中是否有label和data
             if(!isset($value['label']) || !isset($value['data'])) {
                //如果没设置name和value则跳出本次循环
                
            }
            //将表头加入到表头数组                   
            if(!in_array($value['label'],$fields)) {
                $label = str_replace( " " , "", $value['label']);
                if ($label == "") {
                    continue;
                }
                $fields[] = $value['label'];
            }
            $tabledata[$value['row']]["$value[label]"] = $value['data'];
        }
        //判断是否有表头
        if (empty($fields)) {
            return $fields;
        }
        
        $fieldsLens = count($fields);
        //定义tmp
        $tmp = [];
        $i = 0;
        //再次遍历数组按fields顺序加入tabledata
        foreach ($tabledata as $val) {
            foreach ($fields as $value) {
                if (isset($val[$value])) {
                    $tmp[$i][] = $val[$value];
                }else {
                    $tmp[$i][] = '';
                }
            }   
            $i++;
        }
        $datas['thead'] = $fields;
        $datas['tbody'] = $tmp;
        return $datas;
        
        
    }

    //web
    public static function web(array $data )
    {
        //定义返回数组
        $datas = [];
        //取出数组最后一个值
        $last = end($data);
        //对最后一个值进行处理
        $datas = $last;
        return $datas;
    } 

    //web 1
    public static function carousel(array $data) 
    {
        //定义返回数组
        $datas = [];
        //遍历数组
        foreach($data as $key => $value) {
            //判断是否有图片地址
            if(!isset($value['imgurl'])) {
                continue;
            }
            //判断是否有描述
            if(!isset($value['desc'])) {
                $value['desc'] = '';
            }
            $datas['imglist'][] = $value['imgurl'];
            $datas['desclist'][] = $value['desc'];
        }
        //不许其他操作 直接返回datas
        return $datas;
    }

    //折线图,柱状图 
    public static function line(array $data)
    {
         //声明新数组
         $datas = [];
         //声明xAxis数组用户存储name的值
         $xAxis = [];
         //遍历数组
         foreach($data as $key => $value) {
            //判断value 中是否有name和value
            if(!isset($value['value']) || !isset($value['name'])) {
                //如果没设置name和value则跳出本次循环
                continue;
            }
            //判断是否有系列series
            if (!isset($value['series']) || $value['series'] == '') {
                //如果没有系列则给定default系列
                $value['series'] = "default";
            }
            //判断name是否在数组xAxis内
            if(!in_array($value['name'], $xAxis)) {
                //如果name不在数组内则加入数组
                $xAxis[] = $value['name'];
            }
            //将series加入到datas['series']中
            $datas['series'][$value['series']]['seriesName'] = $value['series'];
            //将value加入datas['series']['data']中
            $datas['series'][$value['series']]['data'][$value['name']] = $value['value'];
         }
         //判断datas是否为空
         if(empty($datas)) {
             //datas为空下面程序无法执行返回datas
            return $datas; 
         }
         //将xAxis加入data
         $datas['xAxis'] = $xAxis;
         //将$data['series]转换为索引数组
         $datas['series'] = self::toIndexArr($datas['series']);
         //遍历将$data['series']['data']转换为索引数组
         foreach($datas['series'] as $key => $value) {
            //data转换为索引数组
            $datas['series'][$key]['data'] = self::toIndexArr($value['data']);
         }
         return $datas;
    }

    //折线图,散点图,柱状图 一
    public static function lineblack(array $data)
    {
        //声明新数组
        $datas = [];
        //声明values数组用户存储name的值
        $values = [];
        // dump($data);
        //遍历数组
        foreach($data as $key => $value) {
            //判断是否有系列 value  name
            if(!isset($value['value']) || !isset($value['name'])) {
                continue;
            }
             //判断是否有系列series
             if (!isset($value['series'])) {
                //如果没有系列则给定default系列
                $value['series'] = "default";
            }
            //横轴的键加入values
            $values[] = $value['name'];
            //用val将value保存
            $val = $value;
            //删除name
            unset($val['name']);
            //判断series是否存在
            if(isset($val['value'])) {
                //以name作为键吧系列取出
                $datas['value'][$value['name']][] = $val['value'];
            }else{
                $datas['value'][$value['name']][] = '';
            }
            //判断series是否存在
            if(isset($val['series'])) {
                //以name作为键吧系列取出
                $datas['series'][$value['name']][] = $val['series'];
            }else{
                $datas['series'][$value['name']][] = '';
            }
        }
        //判断datas是否为空
        if(empty($datas)) {
            //datas为空下面程序无法执行返回datas
           return $datas; 
        }
        //将values进行数组去重去除重复的值
        $values = array_unique($values);
        $i = 0;
        //遍历去重后的数组
        foreach($values as $value) {
            $datas['name'][$i][] = $value;
            $i++;
        }
        //判断$datas['value']是否为空
        if(isset($datas['value'])){
            //将vlaue转换为索引数组
            $datas['value'] = self::toIndexArr($datas['value']);
            //将series转换为索引数组
            $datas['series'] = self::toIndexArr($datas['series']);
            //series只需要一个
            $datas['series'] = array_shift($datas['series']);
        }
        return $datas;
    }

    //饼图
    protected static function pie(array $data) 
    {
       //声明新的存储数组$datas 
       $datas = [];
       //定义存储name的legend数组
       $legend = [];
       //遍历data数组
       foreach($data as $key => $value) {
           //判断键是否有series , name , value , 如果数据不全则跳过本次赋值
           if(!isset($value['value']) || !isset($value['name'])) {
                continue;
            }
            if(!isset($value['series'])) {
                $value['series'] = 'default';
                $data[$key]['series'] = 'default';
            }
           //判断$value['name']是否在规定的legend数组内
           if(!in_array($value['name'] , $legend)) {
                //将value中的name值存入规定的legend数组
                $legend[] = $value['name'];
           }
           //系列series系列name和value进行风分组
           $datas['series'][$value['series']]['seriesName'] = $value['series'];
           //删除value中的series
           unset($value['series']);
           //将name和value存入$datas['series'][$data[$key]['series']]['data']中
           $datas['series'][$data[$key]['series']]['data'][$value['name']] = $value;
       }
        //判断$datas是否为空
        if(empty($datas)) {
            return $datas;
        }
       //遍历datas['series']
       foreach($datas['series'] as $key => $value) {
           //将atas['series']中的data转换为索引数组
            $datas['series'][$key]['data'] = self::toIndexArr($value['data']);
       }
       //将$datas['series']转换为索引数组
       $datas['series'] = self::toIndexArr($datas['series']);
       //将legend存入datas
       $datas['name'] = $legend;
       return $datas;
    }

    //饼图 第一版
    protected static function pie1black(array $data)
    { 
        //对数据进行分组
        $groupdata = self::groupData($data);
        //将关联数组变成索引数组
        $result = self::toIndexArr($groupdata);
        //直接返回data即可
        return $result;
    }

    //散点图
    protected static function scatter(array $data)
    {

        //定义新存储数组
        $datas = [];
        $i = 0;
        //遍历data
        foreach($data as $key => $value) {
            //判断键是否有series , name , value , 如果数据不全则跳过本次赋值
            if(!isset($value['name']) ||  !isset($value['value'])) {
                continue;
            }
            //判断是否有系列series
            if (!isset($value['series'])) {
            //如果没有系列则给定default系列
            $value['series'] = "default";
            }
            //复制value值用于删除series
            $val = $value;
            //将series赋值规定格式用以series为键用tmp存储
            $datas['tmp'][$value['series']]['series'] = $val['series'];
            //删除series
            unset($val['series']);
            //将name value 以索引格式存入value
            $datas['tmp'][$value['series']]['value'][$i][] = $val['name'];
            $datas['tmp'][$value['series']]['value'][$i][] = $val['value'];
            //将name存入单独的name键中
            $datas['name'][] = $val['name'];
            $i++;
        }
        //判断$datas是否为空
        if(empty($datas)) {
            return $datas;
        }
        //取出$datas['name]中的重复值
        $datas['name'] = array_unique($datas['name']);
        
        //遍历$datas['data']将其变为索引数组
        foreach($datas['tmp'] as $key => $value) {
            //将tmp装换为索引数组
            $datas['tmp'][$key]['value'] = self::toIndexArr($value['value']);
        }
        //遍历$datas['data']将其变为索引数组
        foreach($datas['tmp'] as $key => $value) {
            //将tmp装换为索引数组
            $datas['data'][] = $value;
        }
        
        //删除数组中的tmp键
        unset($datas['tmp']);
        //返回数组
        return $datas;
    }


    /**
     * 对数据进行分组
     */
    protected static function groupData(array  $data)
    {
        
        //声明数组,存储分组后的数据
        $group = [];
        //遍历数据,查看是否有分组
        foreach($data as $key => $value) {
            //判断是否有series
            if(isset($value['series'])) {
                //给value一个临时变量
                $arr = $value;
                //销毁series
                unset($arr['series']);
                //给group赋值
                $group[$value['series']][] = $arr;
            }else {
                $group[0][] = $value;
            }
        }

       return $group;
    }

    //图表类型pie1
    protected static function pie1(array $data) 
    {
        $datas = [];
        //声明数组,存储分组后的数据
        $group = [];
        $legend = [];
        //遍历数组
        foreach($data as $key => $value) {
            //判断value中是否有name 和value 键
            if (!isset($value['name']) || !isset($value['value']) ) {
                //不满足条件则不返回
                continue;
            }
            //判断legend数组中有没有$value['name']
            if(!in_array( $value['name'] , $legend )) {
                $legend[] = $value['name'];
            }
            //将value中的name,value存入$datas中
            $datas['data'][$value['name']]['name'] = $value['name'];
            $datas['data'][$value['name']]['value'] = $value['value'];
        }
        //判断$datas[data]是否为空
        if(empty($datas['data'])) {
            //返回空数组
            return $datas;
        }
        //将legend加入data[legend]中
        $datas['legend'] = $legend;
        //将data转换成索引数组
        $datas['data'] = self::toIndexArr($datas['data']);
        //返回数组
        return $datas;
    }

    //图表类型pie2
    protected static function pie2(array $data) 
    {

        //声明数组,存储分组后的数据
        $group = [];
        //遍历数组
        foreach($data as $key => $value) {
            //判断value中是否有name 和value 键
            if (!isset($value['name']) || !isset($value['value']) ) {
                //不满足条件则不返回
                continue;
            }
            //将value中的name,value存入$datas中
            $datas['name'][] = $value['name'];
            $datas['value'][] = $value['value'];
        }
        //不需要进行下一步操作直接返回数组,无论是否为空
        return $datas;
    }

    //bar1
    protected static function bar1(array $data) 
    {
        // dump($data);die;
        //定义新存储数组
        $datas = [];
        //遍历data
        foreach($data as $key => $value) {
            //判断键是否有series , name , value , 如果数据不全则跳过本次赋值
            if(!isset($value['name']) ||  !isset($value['value'])) {
                continue;
            }
            // dump($value);die;
            //判断是否有系列series
            if (!isset($value['series'])) {
            //如果没有系列则给定default系列
            $value['series'] = "default";
            }
            //复制value值用于删除series
            $val = $value;
            //将series赋值规定格式用以series为键用tmp存储
            $datas['tmp'][$value['series']]['seriesName'] = $val['series'];
            //删除series
            unset($val['series']);
            //将name value 以索引格式存入value
            $datas['tmp'][$value['series']]['data'][$val['name']] = $val['value'];
            //将name存入单独的name键中
            $datas['name'][] = $val['name'];
        }
        // dump($datas);die;
        //判断$datas是否为空
        if(empty($datas)) {
            return $datas;
        }
        //取出$datas['name]中的重复值
        $datas['name'] = self::toIndexArr(array_unique($datas['name']));
        
        //遍历$datas['data']将其变为索引数组
        foreach($datas['tmp'] as $key => $value) {
            //将tmp装换为索引数组
            $datas['tmp'][$key]['data'] = self::toIndexArr($value['data']);
        }
        //遍历$datas['series']将其变为索引数组
        foreach($datas['tmp'] as $key => $value) {
            //将tmp装换为索引数组
            $datas['series'][] = $value;
        }
        
        //删除数组中的tmp键
        unset($datas['tmp']);
        //返回数组
        return $datas;
    }
    //bar1
    protected static function huxingzhutu(array $data) 
    {
        //定义新存储数组
        $datas = [];
        //遍历data
        foreach($data as $key => $value) {
            //判断键是否有series , name , value , 如果数据不全则跳过本次赋值
            if(!isset($value['name']) ||  !isset($value['value'])) {
                continue;
            }
            //判断是否有系列series
            if (!isset($value['series'])) {
            //如果没有系列则给定default系列
            $value['series'] = "default";
            }
            //复制value值用于删除series
            $val = $value;
            //将series赋值规定格式用以series为键用tmp存储
            $datas['tmp'][$value['series']]['seriesName'] = $val['series'];
            //删除series
            unset($val['series']);
            //将name value 以索引格式存入value
            $datas['tmp'][$value['series']]['data'][$val['name']] = ['name' => $val['name'],'value' => $val['value']];
            //将name存入单独的name键中
            $datas['name'][] = $val['name'];
        }
        //判断$datas是否为空
        if(empty($datas)) {
            return $datas;
        }
        //取出$datas['name]中的重复值
        $datas['name'] = self::toIndexArr(array_unique($datas['name']));
        
        //遍历$datas['data']将其变为索引数组
        foreach($datas['tmp'] as $key => $value) {
            //将tmp装换为索引数组
            $datas['tmp'][$key]['data'] = self::toIndexArr($value['data']);
        }
        //遍历$datas['series']将其变为索引数组
        foreach($datas['tmp'] as $key => $value) {
            //将tmp装换为索引数组
            $datas['series'][] = $value;
        }
        
        //删除数组中的tmp键
        unset($datas['tmp']);
        //返回数组
        return $datas;
    }

    //bar2
    protected static function bar2(array $data) 
    {
        //定义新存储数组
        $datas = [];
        //定义name存储数组
        $name = [];
        //遍历data
        foreach($data as $key => $value) {
            //判断键是否有series , name , value , 如果数据不全则跳过本次赋值
            if(!isset($value['name']) ||  !isset($value['barval']) || !isset($value['lineval'])) {
                continue;
            }
            //判断$value[name]是否加入到name数组中
            if(!in_array($value['name'] , $name )) {
                $name[] = $value['name'];
            }
            //判断是否有系列
            if(!isset($value['series'])) {
                $value['series'] = '默认系列';
            }
            //将系列写入
            $datas['series'][$value['series'].'bar']['seriesType'] = 'bar';
            $datas['series'][$value['series'].'bar']['seriesName'] = $value['series'];
            //将$value[bar]加入到datas数组中
            $datas['series'][$value['series'].'bar']['data'][$value['name']] = $value['barval'];
            //将系列写入
            $datas['series'][$value['series'].'line']['seriesType'] = 'line';
            $datas['series'][$value['series'].'line']['seriesName'] = $value['series'];
            //将$value[bar]加入到datas数组中
            $datas['series'][$value['series'].'line']['data'][$value['name']] = $value['lineval'];
            
        }
        //判断$datas是否为空
        if(empty($datas)) {
            return $datas;
        }
        //将内部转换为索引数组
        $datas = self::seriesToIndex($datas);
        //将name加入到datas
        $datas['name'] = $name;
        return $datas;
    }

    //line4
    protected static function line4(array $data) 
    {
        //定义新存储数组
        $datas = [];
        //定义name存储数组
        $name = [];
        //遍历data
        foreach($data as $key => $value) {
            //判断键是否有series , name , value , 如果数据不全则跳过本次赋值
            if(!isset($value['name']) ||  !isset($value['value']) || !isset($value['value2'])) {
                continue;
            }
            //判断$value[name]是否加入到name数组中
            if(!in_array($value['name'] , $name )) {
                $name[] = $value['name'];
            }
            //判断是否有系列
            if(!isset($value['series'])) {
                $value['series'] = '默认系列';
            }
            //将系列写入
            $datas['series'][$value['series'].'yaxis1']['seriesType'] = 'yaxis1';
            $datas['series'][$value['series'].'yaxis1']['seriesName'] = $value['series'];
            //将$value[bar]加入到datas数组中
            $datas['series'][$value['series'].'yaxis1']['data'][$value['name']] = $value['value'];
            //将系列写入
            $datas['series'][$value['series'].'yaxis2']['seriesType'] = 'yaxis2';
            $datas['series'][$value['series'].'yaxis2']['seriesName'] = $value['series'];
            //将$value[bar]加入到datas数组中
            $datas['series'][$value['series'].'yaxis2']['data'][$value['name']] = $value['value2'];
            
        }
        //判断$datas是否为空
        if(empty($datas)) {
            return $datas;
        }
        
        //将内部转换为索引数组
        $datas = self::seriesToIndex($datas);
        //将name加入到datas
        $datas['name'] = $name;
        return $datas;
    }

    //scatter2
    protected static function scatter2( array $data) 
    {
        $datas = [];
        //定义name存储数组
        $name = [];
        //遍历data
        foreach($data as $key => $value) {
            //判断键是否有 x , y , 如果数据不全则跳过本次赋值
            if(!isset($value['x']) ||  !isset($value['y'])) {
                continue;
            }
            //判断是否有系列
            if(!isset($value['series'])) {
                $value['series'] = '默认系列';
            }

            //判断是否有系列
            if(!isset($value['r'])) {
                $value['r'] = 10.0;
            }

            //将系列名加入series
            $datas['series'][$value['series']]['seriesName'] = $value['series'];
            //将x,y以数组形式加入到data
            $datas['series'][$value['series']]['data'][$value['x'].$value['y']]['value'] = [$value['x'] , $value['y']];
            $datas['series'][$value['series']]['data'][$value['x'].$value['y']]['symbolSize'] = floatval($value['r']);
        }
        //判断datas是否为空
        if(empty($datas)) {
            return $datas;
        }
        //将内部转换为索引数组
        $datas = self::seriesToIndex($datas);
        return $datas;
    }

    //词云
    protected static function wordCloud(array $data)
    {   
        //定义存储数组
        $datas = [];
        $i = 0;
        //遍历数据
        foreach($data as $value){
            //判断name和value是否存在
            if(!isset($value['name']) ||  !isset($value['value'])) {
                continue;
            }
            $datas[$i]['name'] = strval($value['name']);
            $datas[$i]['value'] = intval($value['value']);
            $i++;
        }
        //直接返回
        return $datas;
    }

    //关系
    protected static function funnel( array $data)
    {
        //定义存储数组
        $datas = [];
        $name = [];
        //遍历data
        foreach($data as $key => $value) {
            //判断键是否有series , name , value , 如果数据不全则跳过本次赋值
            if(self::required($value,['name','value'])) {
                continue;
            }
            //判断是否有系列
            if(!isset($value['series'])) {
                $value['series'] = '默认系列';
            }
            //判断$value[name]是否加入到name数组中
            if(!in_array($value['name'] , $name )) {
                $name[] = $value['name'];
            }
            $datas['series'][$value['series']]['seriesName'] = $value['series'];
            $datas['series'][$value['series']]['data'][$value['name']]['name'] = $value['name'];
            $datas['series'][$value['series']]['data'][$value['name']]['value'] = $value['value'];

        }
       //判断$datas是否为空
       if(empty($datas)) {
        return $datas;
        }
        
        //将内部转换为索引数组
        $datas = self::seriesToIndex($datas);
        //将name加入到datas
        $datas['name'] = $name;
        return $datas;
    }

    // /liquidFill
    protected static function liquidFill(array $data)
    {   
        //定义新数组
        $datas = [];
        //遍历$data判断是否符合返回规范
        foreach($data as $value) {
             //判断键是否有 value , 如果数据不全则跳过本次赋值
            if(self::required($value,['value'])) {
                continue;
            }
            $datas['value'] = $value['value'];
        }
        return $datas;
    }

    //gauge
    protected static function gauge(array $data)
    {
        //定义新数组
        $datas = [];
        //定义新数组
        $datas = [];
        //遍历$data判断是否符合返回规范
        foreach($data as $value) {
             //判断键是否有name , value , 如果数据不全则跳过本次赋值
            if(self::required($value,['value','name'])) {
                continue;
            }
            $datas['data'][$value['name']]['name'] = $value['name'];
            $datas['data'][$value['name']]['value'] = $value['value'];
        }
        //判断是否datas为空
        if(empty($datas)) {
            return $datas;
        }

        //转换为索引数组
        $datas['data'] = self::toIndexArr($datas['data']);
        return $datas;
    }

    //radar
    protected static function radar(array $data) 
    {
        //定义新数组
        $datas = [];
        //定义指标数组
        $target = [];
        //定义指标最大值
        $maxTargetValue = [];
        //遍历数组
        foreach($data as $value) {
            //判断键是否有name , value , 如果数据不全则跳过本次赋值
            if(self::required($value,['value','name','target'])) {
                continue;
            }
            //将name加入data中
            $datas['data'][$value['name']]['name'] = $value['name'];
            //将相同名字的value加数data中
            $datas['data'][$value['name']]['value'][] = $value['value'];
            //将相同名字的target加数data中
           //判断$value[name]是否加入到name数组中
            if(!in_array($value['target'] , $target )) {
                $target[] = $value['target'];
            }
            $maxTargetValue[$value['target']][] = $value['value'];
            
        }
        //判断datas是否为空
        if(empty($datas)) {
            return $datas;
        }
        //将datas['data']转换为索引数组
        $datas['data'] = self::toIndexArr($datas['data']);
        //遍历指标数组
        foreach($target as $value){
            //求出该指标的最大值并与指标名一起存入datas
            $datas['target'][] = $value;
            $datas['maxVal'][] = max($maxTargetValue[$value]);
            
        }
        return $datas;
    }

    //boxplot
    protected static function boxplot(array $data)
    {
        //定义存储数组
        $datas = [];
        $name = [];
        //遍历data
        foreach($data as $key => $value) {
            //判断键是否有'min','Q1','median','Q3','max'如果数据不全则跳过本次赋值
            if(self::required($value,['min','Q1','median','Q3','max'])) {
                continue;
            }
            //判断是否有系列
            if(!isset($value['series'])) {
                $value['series'] = '默认系列';
            }
            //判断$value[name]是否加入到name数组中
            if(!in_array($value['name'] , $name )) {
                $name[] = $value['name'];
            }
            $datas['series'][$value['series']]['seriesName'] = $value['series'];
            $datas['series'][$value['series']]['data'][$value['name']]['value'][] = floatval($value['min']);
            $datas['series'][$value['series']]['data'][$value['name']]['value'][] = floatval($value['Q1']);
            $datas['series'][$value['series']]['data'][$value['name']]['value'][] = floatval($value['median']);
            $datas['series'][$value['series']]['data'][$value['name']]['value'][] = floatval($value['Q3']);
            $datas['series'][$value['series']]['data'][$value['name']]['value'][] = floatval($value['max']);

        }
       //判断$datas是否为空
       if(empty($datas)) {
        return $datas;
        }
        
        //将内部转换为索引数组
        $datas = self::seriesToIndex($datas);
        //将name加入到datas
        $datas['name'] = $name;
        return $datas;
    }

    //gridheatmap
    protected static function gridheatmap( array $data)
    {
        
        //定义存储数组
        $datas = [];
        //定义xName
        $xName = [];
        //定义yName
        $yName = [];
        $i = 0;
        //遍历data
        foreach($data as $key => $value) {
            //判断键是否有series , name , value , 如果数据不全则跳过本次赋值
            if(self::required($value,['xName','yName','x','y','value'])) {
                continue;
            }
             //判断$value[name]是否加入到name数组中
             if(!in_array($value['xName'] , $xName )) {
                $xName[] = $value['xName'];
            }
             //判断$value[name]是否加入到name数组中
             if(!in_array($value['yName'] , $yName )) {
                $yName[] = $value['yName'];
            }
            $datas['data'][$i]['value'][] = floatval($value['x']);
            $datas['data'][$i]['value'][] = floatval($value['y']);
            $datas['data'][$i]['value'][] = floatval($value['value']);
            $i++;
        }
        //判断datas是否为空
        if(empty($datas)) {
            return $datas;
        }
        $datas['xAxisData'] = $xName;
        $datas['yAxisData'] = $yName;
        return $datas;
    }

    //graph
    protected static function graph(array $data)
    {
        //定义存储数组
        $datas  = [];
        //定义节点数组
        $category = [];
        //$links 
        $links = [];
        $i=0;
        //遍历data数组
        foreach($data as $value) {
            //判断键是否有series , name , value , 如果数据不全则跳过本次赋值
            if(self::required($value,['name','value','source','target','category'])) {
                continue;
            }
            //将加点出重加入节点数组
            if(!in_array($value['category'] , $category )) {
                $category[] = $value['category'];
            }
            $datas['data'][$value['name']]['name'] = $value['name'];
            $datas['data'][$value['name']]['value'] = floatval($value['value']);
            $datas['data'][$value['name']]['size'] = floatval($value['value']);
           //获取category的下标索引
            $datas['data'][$value['name']]['category'] =  array_search($value['category'], $category);
            
            //给定标识 默认为1
            $j = true;
            //遍历links
            foreach($links as $val) {
                //判断source 和target是否在$links中
                if(in_array($value['source'] , $val) && in_array($value['target'] , $val)) {
                    $j = false;
                    continue;
                }
            }
            //判断标识
            if($j) {
                //将数据加入links
                $links[$i]['source'] = $value['source']; 
                $links[$i]['target'] = $value['target']; 
                $i++;
            }
        }
        //判断datas是否为空
        if(empty($datas)) {
            return $datas;
        }
        

        $datas['data'] = self::toIndexArr($datas['data']);
        $datas['categories'] = $category;
        $datas['links'] = $links;

        return $datas;

    }

    protected static function chinamap(array $data)
    {   
        $type = ["heatmap", "effectScatter", "scatter", "lines"];
        $lines = ["lines"];
        $datas = self::chinaMaps($data,$type,$lines);
        return $datas;
    }

    protected static function chinamap3d(array $data)
    {

        $type = ["lines3D", "scatter3D", "bar3D"];
        $lines = ["lines3D"];
        $datas = self::chinaMaps($data,$type,$lines);
        return $datas;
    }


    //data原数组 $type 所有类型 $lines 双向线
    protected static function chinaMaps($data , $type , $lines)
    {
        //定义返回数组
        $datas  = [];
        //定义type分类数组
        $typeData = [];
        //定义临时数组
        $tmp = [];
        //遍历data数组将type进行分类
        foreach($data as $value) {
            
            //判断是否有字段
           if(self::required($value,['type'])) {
            continue;
            }
            //判断$value中的type是否符合类型
            if(!in_array($value['type'], $type)) {
                continue;
            }
            //判断键是否有name , lng , lat , 如果数据不全则跳过本次赋值
           if(self::required($value,['name' , 'lng' , 'lat'])) {
            continue;
            }

            //通过type类型确定必须的下标
            if(!in_array($value['type'], $lines)) {
                //判断是否有value字段
                if(self::required($value,['value'])) {
                    continue;
                }
                //以type数组顺序将值依次加入datas
                $tmp[$value['type']]['type'] = $value['type'];
                $tmp[$value['type']]['data'][$value['name']]['name'] = $value['name'];
                $tmp[$value['type']]['data'][$value['name']]['value'] = 
                [floatval($value['lng']),floatval($value['lat']),floatval($value['value'])];
            }else {
                //判断键是否有lng2 , lat2 , 如果数据不全则跳过本次赋值
                if(self::required($value,['lng2' , 'lat2' ])) {
                    continue;
                }
                $tmp[$value['type']]['type'] = $value['type'];
                $tmp[$value['type']]['data'][] = 
                [[floatval($value['lng']),floatval($value['lat'])], [floatval($value['lng2']),floatval($value['lat2'])]] ;
            }
           
        }
        //判断datas数组是否为空
        if (empty($tmp)) {
            return $tmp;
        }
       
        //对datas[series]按照type进行排序
        foreach($type as $value) {
            $datas['series'][] = $tmp[$value];
        } 

        // 将datas['series']的value变为索引数组
        $datas['series'] = self::toIndexArr($datas['series']);
       
        //遍历datas将value中的data变为索引数组
        foreach($datas['series'] as $key => $value) {
            $datas['series'][$key]['data'] = self::toIndexArr($value['data']);
        }
        //返回数组
        return $datas;
    }

    //世界地图
    protected static function worldMap(array $data)
    {
        //定义类型决定是data1还是data2
        $type = ["lines","heatmap","effectScatter","scatter"];
        
        $lines = ["lines"];
        //定义返回数组
        $datas  = [];
        //定义type分类数组
        $typeData = [];
       
        //定义临时数组
        $tmp = [];
        //遍历data数组将type进行分类
        foreach($data as $value) {
            //判断是否有字段
            if(self::required($value,['type'])) {
                continue;
            }
           
            //判断$value中的type是否符合类型
            if(!in_array($value['type'], $type)) {
                continue;
            }
            
            //判断键是否有name , lng , lat , 如果数据不全则跳过本次赋值
            if(self::required($value,['name' , 'lng' , 'lat'])) {
            continue;
            }
           

            //通过type类型确定必须的下标
            if(!in_array($value['type'], $lines)) {
                //判断是否有value字段
                if(self::required($value,['value'])) {
                    continue;
                }
                //以type数组顺序将值依次加入datas
                $tmp[$value['type']]['type'] = $value['type'];
                $tmp[$value['type']]['data'][$value['name']]['name'] = $value['name'];
                $tmp[$value['type']]['data'][$value['name']]['value'] = 
                [floatval($value['lng']),floatval($value['lat']),floatval($value['value'])];
            }else {
                //判断键是否有lng2 , lat2 , 如果数据不全则跳过本次赋值
                if(self::required($value,['lng2' , 'lat2' ])) {
                    continue;
                }
                $tmp[$value['type']]['type'] = $value['type'];
                $tmp[$value['type']]['data'][] = 
                [
                    [
                        'coord' => [floatval($value['lng']),floatval($value['lat'])],
                        'value' => floatval($value['value'])],
                        ['coord' =>[floatval($value['lng2']),floatval($value['lat2'])]
                    ]
                ];
            }
        }
        //判断datas数组是否为空
        if (empty($tmp)) {
            return $tmp;
        }
        
        //对datas[series]按照type进行排序
        foreach($type as $value) {
            $datas['series'][] = $tmp[$value];
        } 

        // 将datas['series']的value变为索引数组
        $datas['series'] = self::toIndexArr($datas['series']);
        
        //遍历datas将value中的data变为索引数组
        foreach($datas['series'] as $key => $value) {
            $datas['series'][$key]['data'] = self::toIndexArr($value['data']);
        }
        //返回数组
        return $datas;
    }

    //地球仪
    protected static function globechart(array $data)
    {
        return self::distinguish($data , "globechart");
    }

    //世界地图3d
    protected static function worldMap3d(array $data)
    {
        return self::distinguish($data , "worldMap3d");
    }


    //区分世界地图还是地球仪
    protected static function distinguish(array $data , $types)
    {
        //定义类型决定是data1还是data2
        $type = ["lines3D","scatter3D","bar3D"];
      
        $lines = ["lines3D"];
        //定义返回数组
        $datas  = [];
        //定义type分类数组
        $typeData = [];
        if($types == 'worldMap3d') {
            $alt = "alt";
        }else {
            $alt = "altid";
        }
        //定义临时数组
        $tmp = [];
        //遍历data数组将type进行分类
        foreach($data as $value) {
            //判断是否有字段
            if(self::required($value,['type'])) {
                continue;
            }
           
            //判断$value中的type是否符合类型
            if(!in_array($value['type'], $type)) {
                continue;
            }
            
            //判断键是否有name , lng , lat , 'altid' 如果数据不全则跳过本次赋值
            if(self::required($value,['name' , 'lng' , 'lat',$alt])) {
            continue;
            }
           

            //通过type类型确定必须的下标
            if(!in_array($value['type'], $lines)) {
                //判断是否有value字段
                if(self::required($value,['value'])) {
                    continue;
                }
                //以type数组顺序将值依次加入datas
                $tmp[$value['type']]['type'] = $value['type'];
                $tmp[$value['type']]['defaults'] = $type;
                $tmp[$value['type']]['data'][$value['name']]['name'] = $value['name'];
                $tmp[$value['type']]['data'][$value['name']]['value'] = 
                [floatval($value['lng']),floatval($value['lat']),floatval($value['value'])];
            }else {
                //判断键是否有lng2 , lat2 ,'altid2' 如果数据不全则跳过本次赋值
                if(self::required($value,['lng2' , 'lat2',$alt.'2' ])) {
                    continue;
                }
                $tmp[$value['type']]['type'] = $value['type'];
                $tmp[$value['type']]['defaults'] = $type;
                if($types != 'worldMap3d') {
                    $tmp[$value['type']]['data'][] = [
                        "coords" =>[
                                 [floatval($value['lng']),floatval($value['lat']),floatval($value[$alt])],
                                 [floatval($value['lng2']),floatval($value['lat2']),floatval($value[$alt.'2'])] 
                            ]
                    ];
                }else {
                    $tmp[$value['type']]['data'] = [
                        [
                            [floatval($value['lng']),floatval($value['lat']),floatval($value[$alt])],
                            [floatval($value['lng2']),floatval($value['lat2']),floatval($value[$alt.'2'])] 
                        ]
                    ];
                }

            
            }
        }
        //判断datas数组是否为空
        if (empty($tmp)) {
            return $tmp;
        }
        
        //对datas[series]按照type进行排序
        foreach($type as $value) {
            if(isset($tmp[$value])) {
                $datas['series'][] = $tmp[$value];
            }
        } 

        // 将datas['series']的value变为索引数组
        $datas['series'] = self::toIndexArr($datas['series']);
        
        //遍历datas将value中的data变为索引数组
        foreach($datas['series'] as $key => $value) {
            $datas['series'][$key]['data'] = self::toIndexArr($value['data']);
        }
        //返回数组
        return $datas;
    }

    //scatter3
    protected static function scatter3(array $data)
    {
        //定义存储数组datas
        $datas = [];
        $line = [];
        $scatter = [];
        //遍历data
        foreach ($data as $key => $value) {
            //判断是否有'x','y','z'字段
           if(self::required($value,['x','y','z'])) {
                continue;
           }
           $line[] = ['value' => [$value['x'] , $value['y']]];
           $scatter[] = ['value' => [$value['x'], $value['z']]];
        }

        //判断是否有符合的值
        if (empty($line) || empty($scatter) ) {
            return $datas;
        }
        //设置格式
        $datas['series'][] = ['seriesType' => 'scatter','seriesName' => '默认系列' , 'scatter' => $scatter ];
        $datas['series'][] = ['seriesType' => 'line','seriesName' => '默认系列' , 'scatter' => $line ];
        
        return $datas;
    }

    //scatter4
    protected static function scatter4(array $data)
    {
        //定义存储数组datas
        $datas = [];
        //存储scatter
        $scatter = [];
        //line
        $line = [];
        //遍历data
        foreach ($data as $key => $value) {
            //判断是否有'x','y','z'字段
            if(self::required($value,['x','y','z','r'])) {
                continue;
            }
        
           $scatter[] = ['value' => [$value['x'] , $value['z']] , 'symbolSize' => $value['r']];
           $line[] = ['value' => [$value['x'], $value['y']]];


        }
        //判断是否有符合的值
        if (empty($line) || empty($scatter) ) {
            return $datas;
        }
        //设置格式
        $datas['series'][] = ['seriesType' => 'scatter','seriesName' => '默认系列' , 'scatter' => $scatter ];
        $datas['series'][] = ['seriesType' => 'line','seriesName' => '默认系列' , 'scatter' => $line ];
        
        return $datas;
    }

    //bar11
    protected static function bar11(array $data) 
    {
        //定义存储数组
        $datas = [];
        //定义name数组
        $name = [];
        //定义barval数组
        $barval = [];
        //定义barval数组
        $lineval = [];
        //遍历data
        foreach($data as $value) {
            //判断是否有'name','barval','lineval'字段
            if(self::required($value,['name','barval','lineval'])) {
                continue;
            }
            //判断value[name]是否在数组name中
            if(!in_array($value['name'],$name)) {
                //将name加入到name数组中
                $name[] = $value['name'];
            }
            $barval[] = $value['barval'];
            $lineval[] = $value['lineval'];
        }

        //判断是否有符合的值
        if (empty($barval) || empty($lineval) ) {
            return $datas;
        }
        
        $datas['name'] = $name;
        $datas['series'][] = ["seriesType" => "bar" , "seriesName" => "系列一" ,"data" => $barval];
        $datas['series'][] = ["seriesType" => "line" , "seriesName" => "系列一" ,"data" => $lineval];
        return $datas;
    }

    //bar12
    protected static function bar12(array $data)
    {
        //定义会数组
        $datas = [];
        //定义name数组
        $xname = [];
        $yname = [];
        //遍历data
        foreach($data as $value) {
            //判断是否有'xname','yname','value'字段
            if(self::required($value,['xname','yname','value'])) {
                continue;
            }
            //判断xname,和 ,yname是否出现过
            // if(!in_array($value['xname'], $xname) && !in_array($value['yname'], $yname)) {
                $xname[] = $value['xname'];
                $yname[] = $value['yname'];
                $datas['data'][] = ["value" => [$value['xname'],$value['yname'],$value['value']]];
            // }
        }
        //判断data是否为空
        if (empty($datas)) {
            return $datas;
        }
        //将xname,yname写入data
        $datas['xname'] = $xname;
        $datas['yname'] = $yname;
        return $datas;  
    } 

    //sankey
    protected static function sankey(array $data)
    {
        //定义会数组
        $datas = [];
        //定义name数组
        $name = [];
        //定义$links
        $links = [];
        //将name全部获取
        $name = self::getSanKeyName($data);
        if(empty($name)) {
            return $datas;
        }
        //将links取出
        $links = self::getSanKeyLinks($data , $name);
        //判断$name 及links 是否同时有值 
        if(empty($links)) {
            return $datas;
        }
        //将links加入datas
        $datas['series'][0]["links"] = $links;
        //将name循环加入datas
        for ($i=0; $i <count($name) ; $i++) { 
            $datas['series'][0]["data"][]['name'] = $name[$i]; 
        }
        return $datas;
    }

    //获取sankey中的name
    protected static function getSanKeyName($data)
    {
        $name = [];
        foreach($data as $value) {
            //判断是否有'xname','yname','value'字段
            if(self::required($value,['name'])) {
                continue;
            }
            //判断name是否重复
            if(in_array($value['name'],$name)) {
                continue;
            }
            //将name加入到name数组
            $name[] = $value['name'];
        }
        return $name;
    }

    //获取sankey中的links
    protected static function getSanKeyLinks($data , $name)
    {
        $links = [];
        //遍历data获取links
        foreach ($data as $value) {
            //判断是否有'source','target','value'字段
            if(self::required($value,['source','target','value'])) {
                continue;
            }
            // //判断name是否重复
            // if(in_array($value['name'],$name)) {
            //     continue;
            // }
            //判断source与target是否在name数组里面
            if(in_array($value['source'],$name) && in_array($value['target'],$name)) {
                $links[] = [ 'source' => $value['source'] , 'target' => $value['target'],'value' => $value['value']];
            }
        }
        return $links;
    }

    //rectangletree
    protected static function rectangletree(array $data)
    {   
        //定义存储数组
        $datas = [];
        //定义符合条件的数组
        $result = [];
        //将data中不符合 规格的剔除
        foreach($data as $key => $value ) {
            //判断是否有'name','barval','lineval'字段
            if(!self::required($value,['name','pid','value','cid'])) {
                $result[] = $value;
            }
        }
        //判断符合条件数据是否为空
        if(empty($result)) {
            return $datas;
        }
        //将data无限级分类
        $tree = self::tree($result);
        //判断递归是否成功
        if(empty($tree)) {
            return $datas;
        }
        //设置datas返回格式
        $datas['series'][]['data'] = $tree;
        return $datas;
    }

    //sunburst
    protected static function  sunburst(array $data)
    {
        //定义存储数组
        $datas = [];
        //定义符合条件的数组
        $result = [];
        //将data中不符合 规格的剔除
        foreach($data as $key => $value ) {
            //判断是否有'name','barval','lineval'字段
            if(!self::required($value,['name','pid','value','cid'])) {
                $result[] = $value;
            }
        }
        //判断符合条件数据是否为空
        if(empty($result)) {
            return $datas;
        }
        //将data无限级分类
        $tree = self::tree($result);
        //判断递归是否成功
        if(empty($tree)) {
            return $datas;
        }
        //设置datas返回格式
        $datas['series'][]['data'][] = $tree[0];
        return $datas;
    }

    //line5
    protected static function line5(array $data)
    {
        //定义存储数组
        $datas = [];
        //遍历data数组
        foreach($data as $value) {
            //判断是否有'xdata','ydata','value'字段
            if(self::required($value,['xdata','ydata','value'])) {
                // 不存在，跳出本次循环
                continue;
            }
            //判断系列是否存在
            if(!isset($value['series'])) {
                $value['series'] = '默认系列';
            }
            $datas['series'][$value['series']]['seriesName'] = $value['series'];
            $datas['series'][$value['series']]['data'][] = [$value['xdata'],$value['ydata'],$value['value']];
        }
        //判断datas是否为空
        if(empty($datas)) {
            return $datas;
        }
        //将datas[series]变为索引数组
        $datas['series'] = self::toIndexArr($datas['series']);
        return $datas;

    }


    //pie11
    protected static function pie11(array $data)
    {
        //定义存储数组
        $datas = [];
        //申明临时数组
        $tmp = [];
    
        //遍历data数组
        foreach($data as $value) {
             //判断是否有'value','name'字段
             if(self::required($value,['value','name'])) {
                // 不存在，跳出本次循环
                continue;
            }
            $tmp[$value['name']]['name'] = $value['name']; 
            $tmp[$value['name']]['value'] = $value['value']; 
            
        }
        //判断tmp是否为空
        if (empty($tmp)) {
            return $datas;
        }
        //将tmp转化为索引数组
        $tmp = self::toIndexArr($tmp);
        $datas['series'][0]['data'] = $tmp;
        return $datas;

    }

    //pie3
    protected static function pie3(array $data)
    {
        
        //定义返回数组
        $datas = [];
        //遍历数组
        foreach($data as $value) {
             //判断最后一个元素中是否有value,total
            if(self::required($value,['value','total'])) {
                continue;
            }
            $datas[0] = ['value' => $value['value'], 'total' => $value['total']];
        }
        return $datas;
    }


    //递归操作
    protected static function tree($list , $pid = 0)
    {
        //定义空数组
        $arr = [];
        foreach($list as $key => $value){
            //判断pid是否是value的父级ID
            if ($value['pid'] == $pid) {
                //递归
                $value["children"] = self::tree($list , $value['cid']);
                $arr[] = $value;
            }
        }
        //返回本次递归获得的arr
        return $arr;
    }

    //判断是否有数组内所有下标
    protected static function required( array $data,  array $arr) 
    {   

        //遍历需要判断的下标
        foreach($arr as $value) {
            //判断是否存在
            if(!isset($data[$value])) {
                //不存在返回true
                return true;
            }
        }
        //没有不存在的返回false
        return false;
    }

    //将datas的series 和datas['series']中的data转换为索引数组
    protected static function seriesToIndex( array $datas)
    {
        if(empty($datas)) {
            return $datas;
        }
        //将datas['series']变为索引数组
        $datas['series'] = self::toIndexArr($datas['series']);
        //遍历$datas['series'] 将其中data变为索引数组
        foreach($datas['series'] as $key => $value) {
            $datas['series'][$key]['data'] = self::toIndexArr($value['data']);
        }
        return $datas;
    }

    //传入一个关联数组  返回一个索引数组
    protected static function toIndexArr(array $arr)
    {
        $newArr = [];
        $i=0;
        foreach($arr as $key => $value){
            $newArr[$i] = $value;
            $i++;
        }
        return $newArr;
    }

    //百分比占比图
    protected static function baifenbiduibibintu(array $data)
    {
        //定义返回数组
        $datas = [];
        //设置遍历次数
        $i = 0;
        //遍历数组
        foreach ($data as $key => $value) {
            //判断最后一个元素中是否有value
            if(self::required($value,['value'])) {
                continue;
            }
            //百分比占比图只需要两个
            if($i > 2) {
                break;
            }
            //判断$value[value]是否是百分比
            $vali = strstr($value['value'] , "%");
            //定义临时数组
            $tmp = [];
            //确定为百分比
            if($vali) {
                //处理百分比
                $num = intval(str_replace("%" ,"" , $value['value']));
                if($num >= 100) {                //大于100时
                    $tmp = [100 , 0];
                }else if($num <=0) {  //小于0 时
                    $tmp = [0,100];
                }else { //其他情况
                    $tmp = [$num , 100 - $num];
                }
                if($i == 0) { //第一组数据
                    $datas["branch"]  = $tmp;
                }else { //第二组数组
                    $datas["total"]  = $tmp;
                }
                $i++;
            }
        }
        //判断是否有两条记录
        if(count($datas) == 2) {
            return $datas;
        }else {
            return [];
        }
    }

    //进度条形码
    protected static function jindutiaoxingma( array  $data)
    {
        //定义返回数组
        $datas = [];
        //遍历数组
        foreach ($data as $key => $value) {
            //判断最后一个元素中是否有value
            if(self::required($value,['value'])) {
                continue;
            }
            //判断$value[value]是否是百分比
            $vali = strstr($value['value'] , "%");
            if(!$vali) {
                unset($data[$key]);
            }else {
                $datas = $value;
                break;
            }

        }
        //判断是否有两条记录
        return $datas;

    }

    //进度条形码
    protected static function jianbianzhuzhuangtu( array  $data)
    {
        //定义返回数组
        $datas = [];
        //遍历数组
        foreach ($data as $key => $value) {
            //判断最后一个元素中是否有value,name
            if(self::required($value,['value',"name"])) {
                continue;
            }
            $datas['name'][] = $value['name'];
            $datas['seriesData'][] = $value['value'];
        }
        //判断是否有两条记录
        return $datas;
    }

    //3d柱状趋势变化图
    protected static function zhuzhuangqushibianhuatu3d(array  $data)
    {
        //定义返回数组
        $datas = [];
        $arr = ["title","value","standard"];
        //遍历数组
        foreach ($data as $key => $value) {
            //判断最后一个元素中是否有value,name
            if(self::required($value,$arr)) {
                continue;
            }
            $datas = $value;
            break;
        }

        //判断是否有两条记录
        return $datas;
    }

    //水平发光柱状图
    protected static function shuipingfaguangzhuzhuangtu( array  $data)
    {
        //定义返回数组
        $datas = [];
        //遍历数组
        foreach ($data as $key => $value) {
            //判断最后一个元素中是否有value,name
            if(self::required($value,['value',"name"])) {
                continue;
            }
            $datas['name'][] = $value['name'];
            $datas['seriesData'][] = $value['value'];
        }
        //判断是否有两条记录
        return $datas;
    }

    //图表类型linebar
    protected static function timelinebar(array $data) 
    {
        //声明数组,存储分组后的数据
        $datas = [];
        //存放季度
        $lengend = [];
        $name = [];
        $series = [];
        $val = [];
        //遍历数组
        foreach($data as $key => $value) {
        
            // dump($value);
            //判断value中是否有name 和value 键
            if (self::required($value,['value','name','lengend'])) {
                //不满足条件则不返回
                continue;
            } 
            if(!in_array($value['lengend'],$lengend)){
            $lengend[]   = $value['lengend'];    
            }
            if(!in_array($value['name'],$name)){
                $name[]   = $value['name'];    
            }
            if(!in_array($value['series'],$series)){
                $series[]   = $value['series'];    
            }
            $val[$value['lengend']][] = $value['value'];

        }
        //去掉$val的键，将其变为索引数组
        $val = array_values($val);
        
        $datas['lengend'] = $lengend;
        $datas['name'] = $name;
        $datas['series'] = $series;
        $datas['value'] = $val;
        //不需要进行下一步操作直接返回数组,无论是否为空
        return $datas;
    }

    //折线柱状图
    protected static function zhexianzhuzhuangtu(array $data)
    {
        //根据建将相同的放入一个数组
        foreach($data as $kk=>$vv){
            $result[$vv['series']][]  = $vv;
        }
        //删除健
        $data = array_values($result);
        $name = [];
        $datas = [];
        foreach($data as $key=>$value){
            foreach($value as $k=>$v){
                    //判断最后一个元素中是否有value,name
                if(self::required($v,['value',"name"])) {
                    continue;
                }
                if(!in_array($v['name'] , $name )) {
                $name[] = $v['name'];
                }
            $datas['series'][$key]['seriesName'] = $v['series'];
            $datas['series'][$key]['data'][] = $v['value'];
            } 
        }
        $datas['name'] = $name;
        return $datas;
    }

    //雷达bia变形图
    protected static function leidabianxingtu(array $data)
    {
        $datas = [];
        $target = [];
        $maxTargetValue = [];

            foreach($data as $value) {
            //判断键是否有name , value , 如果数据不全则跳过本次赋值
            if(self::required($value,['value','name','target'])) {
                continue;
            }
            //将name加入data中
            $datas['data'][$value['name']]['name'] = $value['name'];
            //将相同名字的value加数data中
            $datas['data'][$value['name']]['value'][] = $value['value'];
            //将相同名字的target加数data中
            //判断$value[name]是否加入到name数组中
            if(!in_array($value['target'] , $target )) {
                $target[] = $value['target'];
            }
            $maxTargetValue[$value['target']][] = $value['value'];
            
        }
        if(empty($datas)) {
            return $datas;
        }
        //将datas['data']转换为索引数组
        $datas['data'] = self::toIndexArr($datas['data']);
        //遍历指标数组
        foreach($target as $value){
            //求出该指标的最大值并与指标名一起存入datas
            $datas['target'][] = $value;
        }
        // $datas['maxVal'] = max($maxTargetValue[$value]); 
        $datas['maxVal'] = max(array_column($data,'value')); 
            
        return $datas;
    }

    //数据饼图
    protected static function shujubingtu(array $data)
    {
        //定义返回数组
        $datas = [];
        //遍历数组
        foreach ($data as $key => $value) {
            //判断最后一个元素中是否有value
            if(self::required($value,['value','name'])) {
                continue;
            }
            //判断$value[value]是否是百分比
            //  $vali = strstr($value['value'] , "%");
            //  if(!$vali) {
                //  unset($data[$key]);
            //  }else {
                $datas[] = $value;
                break;
            //  }

        }
        //判断是否有两条记录
        return $datas;
    }

    //环形数据图
    protected static function huanxingshujutu(array $data)
    {
        $datas = [];
        //遍历数组
        foreach ($data as $key => $value) {
            //判断最后一个元素中是否有value
            if(self::required($value,['value','name'])) {
                continue;
            }
        $datas[] = $value;
        }
        return $datas;
    }

    //环形比例图
    protected static function huanxingbilitu(array $data)
    {
        //定义返回数组
        $datas = [];
        //遍历数组
        foreach ($data as $key => $value) {
            //判断最后一个元素中是否有value
            if(self::required($value,['value','name'])) {
                continue;
            }
        $datas[] = $value;
        }
        //判断是否有两条记录
        return $datas;
    }

    //八边形图2
    protected static function babianxingtutwo(array $data)
    {
        //定义返回数组
        $datas = [];
        //遍历数组
        foreach ($data as $key => $value) {
            //判断最后一个元素中是否有value
            if(self::required($value,['value','name'])) {
                continue;
            }
        $datas[] = $value;
        }
        //判断是否有两条记录
        return $datas;
    }

    //四叶草图
    protected static function siyecaotu(array $data)
    {
        //定义返回数组
        $datas = [];
        //遍历数组
        foreach ($data as $key => $value) {
            //判断最后一个元素中是否有value
            if(self::required($value,['value','name'])) {
                continue;
            }
        $datas[] = $value;
        }
        //判断是否有两条记录
        return $datas;
    }
    public static function cloudComponent(array $data)
    {
        return $data;
        // $return_data = [];
        // foreach ($data as $key => $value) {
        //     if(!empty($value['x'])){
        //         $tmp_data['x'] = $value['x'];
        //     }else{
        //         $tmp_data['x'] = 0;
        //     }

        //     if(!empty($value['y'])){
        //         $tmp_data['y'] = $value['y'];
        //     }else{
        //         $tmp_data['y'] = 0;
        //     }
        //     $return_data[$key] = $tmp_data;
        // }
        // return $return_data;
    }
    
     
}






