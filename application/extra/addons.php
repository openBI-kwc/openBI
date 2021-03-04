<?php
return [
    // 缓存插件
    'cachePlugin' => [
        // 是否开启缓存
        'enabled' => true,
        // 图表数据（api，静态，sql等）
        'config' => [
            'tidChartData' => 'tiddata',
            'screenidChartData' => 'screeniddata',
        ],
        // 增加监听
        'role'=>[
            'index/screen/updatechart' => ['app_end', 'app\\addons\\cache\\hook\\CacheChartInfo'],
            'index/screen/addchart' => ['app_end', 'app\\addons\\cache\\hook\\CacheChartInfo'],
            'index/screen/chartcopy' => ['app_end', 'app\\addons\\cache\\hook\\CacheChartInfo'],
        ],
    ],
];