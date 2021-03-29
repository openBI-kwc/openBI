<?php

function removeDir($dirName) {
    if(!is_dir($dirName)){
        return false;
    }
    $handle = @opendir($dirName);
    while (($file = @readdir($handle)) !== false) {
        if($file != '.' && $file != '..') {
            $dir = $dirName.'/'.$file;
            is_dir($dir) ? removeDir($dir) : @unlink($dir);
        }
    }
    closedir($handle);
    return rmdir($dirName); 
}