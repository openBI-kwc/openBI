<?php

function get_Log($username,$operation,$state){
    Db::name('log')->insert(['username'=>$username,'operation'=>$operation,'state'=>$state]);
}


function get_types($filename){
    $type = substr($filename, strrpos($filename, ".")+1);
    return $type;
  }