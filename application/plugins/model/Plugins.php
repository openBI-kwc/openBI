<?php
namespace app\plugins\model;
use think\Model;

class plugins extends Model
{
    protected $dateFormat = false;

    public function getInfoAttr($value)
    {
        return json_decode($value, true);
    }
}