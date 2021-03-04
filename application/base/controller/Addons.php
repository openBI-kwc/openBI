<?php
/**
 * 插件扩展
 */
namespace app\base\controller;
use think\Request;

class Addons
{
    public function __construct(Request $request = null)
    {
        $module =  strtolower(request()->module());
        $controller = strtolower(request()->controller());
        $action = strtolower(request()->action());
        $path = $module .'/'. $controller .'/'.$action;
        // 插件规则
        $addonConfig = config('addons');
        foreach ($addonConfig as $pluginName => $pluginConfig) {
            $pluginConfig['enabled'] = $pluginConfig['enabled'] ?? 'true';
            if (isset($pluginConfig['role'][$path]) && $pluginConfig['enabled']) {
                \think\Hook::add($pluginConfig['role'][$path][0], $pluginConfig['role'][$path][1]);
            }
        }   
    }
}