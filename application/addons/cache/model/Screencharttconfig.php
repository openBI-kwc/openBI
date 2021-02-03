<?php

namespace app\addons\cache\model;

use think\Model;

class Screencharttconfig extends Model
{
    public function getDataOptAttr($value)
    {
        return json_decode($value, true);
    }
    public function getMapsAttr($value)
    {
        return isset($this->dataOpt['map']) ? array_filter(array_combine(array_column($this->dataOpt['map'], 0), array_column($this->dataOpt['map'], 1))) : [];
    }
}