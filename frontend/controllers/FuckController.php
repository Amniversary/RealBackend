<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2015/12/12
 * Time: 16:11
 */

namespace frontend\controllers;


use common\components\OssUtil;
use yii\web\Controller;

class FuckController extends Controller
{

    public $enableCsrfValidation = false;

    public function actions()
    {
        return require(__DIR__.'/FuckActions/FuckActionConfig.php');
    }

    public function behaviors()
    {
        return require(__DIR__.'/FuckActions/FuckBehaviors.php');
    }

    public function actionUpload()
    {
        echo "<pre>";
        $picUrl = '';
        $error = '';
        $dir = \Yii::$app->getBasePath().'/web/tttt';
        $picList = [];
        $files = scandir($dir);
        $picStrList = '';
        foreach($files as $file)
        {
            $items = explode('\\', $file);
            $len = count($items);
            $file_name = $items[$len - 1];
            //验证上传文件格式类型
            if (strpos($file_name, '.js') === false) {
                continue;
            }
            $file = $dir.'/'.$file;
            $fName = basename($file_name);
            // 初始化 UploadManager 对象并进行文件的上传
            if (!OssUtil::UploadQiniuFile($fName, $file, $picUrl, $error)) {
                print_r($error);
                exit;
            }
            $picStrList .= $picUrl."\r\n";
            $picList[]=$picUrl;
        }
        var_dump($picList);
        $fileStore = $dir.'/picurl.txt';
        file_put_contents($fileStore,$picStrList);
        exit;
    }
}