<?php
namespace app\plugins\controller;

class Upload
{
    // 支持的格式
    protected $ext = 'zip';
    // 支持的大小
    protected $size = 15678000;
    // 插件目录
    protected $pluginDir = ROOT_PATH . 'public/plugins' . DS;


    public function testUpload()
    {
        echo '<html>

        <body>
            <form action="/plugins/upload" enctype="multipart/form-data" method="post">
                <input type="file" name="file">
                <input type="submit" value="上传">
            </form>
        </body>
        
        </html>';
    }

    /**
     * 从链接上传插件
     *
     * @return void
     */
    public function url()
    {
        $url = input('get.url');
        if (!$this->checkUrl($url)) {
            return get_status(1 , '插件地址不正确！');
        }
        $fileInfo = pathinfo($url);
        $fileName = $fileInfo['basename'];
        $extension = $fileInfo['extension'];
        $savePath = $this->pluginDir . DS . $fileName;
        if (!in_array($extension, explode(',', $this->ext))) {
            return get_status(1 , '文件格式错误');
        }
        ob_start();
        readfile($url); 
        $file = ob_get_contents();
        ob_end_clean(); //清除输出并关闭
        file_put_contents($savePath, $file);
        $result = $this->decompression($savePath, $extension,$fileName);
        @unlink($savePath);
        if (!$result) return get_status(1 , '失败');
        return get_status(0 , 'ok');
    }

    /**
     * 本地上传
     *
     * @return void
     */
    public function index()
    {
        // 获取表单上传文件 例如上传了001.jpg
        $file = request()->file('file');
        // 验证文件
        if($file){
            $info = $file->validate(['size' => $this->size,'ext' => $this->ext])->move($this->pluginDir, '', false);
            if($info){
                // 成功上传后 获取上传信息
                // 输出 jpg
                $extension = $info->getExtension();
                // 输出 20160820/42a79759f284b767dfcb2a0197904287.jpg
                $filePath = $this->pluginDir . DS . $info->getSaveName();
                $result = $this->decompression($filePath, $extension,$info->getSaveName());

                @unlink($filePath);
                if (!$result) return get_status(1 , '失败');
                // $result为路径
                return \app\plugins\controller\Index::upSinglePlugin($result);
                // 输出 42a79759f284b767dfcb2a0197904287.jpg
                // $fileName = $info->getFilename();
            }else{
                // 上传失败获取错误信息
                return get_status(1 , $file->getError()); 
            }
        }
    }

    /**
     * 链接检查
     *
     * @param [type] $url
     * @return void
     */
    protected function checkUrl($url)
    {
        return true;
    }

    /**
     * 解压
     *
     * @param [type] $file
     * @param [type] $extension
     * @return void
     */
    // public function decompression($file, $extension,$filename)
    // {
    //     $zip = new \ZipArchive;
    //     if ($zip->open($file) === TRUE) {
    //         $zip->extractTo($this->pluginDir);
    //         $zip->close();
    //         return true;
    //     } else {
    //         return false;
    //     }
    // }
    // 解压
    public function decompression($file, $extension,$filename)
    {
        $filename = substr($filename,0, -4);
        //每次解压前，先将之前同名的给删除
        // $this->removeDir($path);
        $filenameTmp = $filename."_tmp";
        $path = $this->pluginDir.$filename;
        $pathTmp = $this->pluginDir.$filenameTmp;
        // 已存在则更新
        if (file_exists($path) && is_dir($path)) return $path;
        $zip = new \ZipArchive;
        if ($zip->open($file) === TRUE) {
            $zip->extractTo($path);
            $zip->close();

            $dirs = scandir($path);
            foreach ($dirs as $key => $dir) {
                if($dir == $filename) {
                    //说明里面还有一层，需要将里面的内容放到本层，然后删除该文件夹

                    rename($path."/".$dir."/", $pathTmp);
                    rmdir($path);
                    rename($pathTmp, $path);
                }
            }
            return $path;
        } else {
            return false;
        }
    }
    public function removeDir($dirName) {
        if(!is_dir($dirName)){
            return false;
        }
        $handle = @opendir($dirName);
        while (($file = @readdir($handle)) !== false) {
            if($file != '.' && $file != '..') {
                $dir = $dirName.'/'.$file;
                is_dir($dir)?$this->removeDir($dir):@unlink($dir);
            }
        }
        closedir($handle);
        return rmdir($dirName); 
    }
}