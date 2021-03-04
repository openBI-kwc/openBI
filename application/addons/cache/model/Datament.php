<?php

namespace app\addons\cache\model;

use think\Model;

class Datament extends Model
{
    public function getDataInfoAttr()
    {
        return $this->data['data'];
    }
}