<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2015/12/17
 * Time: 15:11
 */

namespace common\components;
//include  __DIR__.'/oss/sdk.class.php';
use common\components\oss\ALIOSS;
use Qiniu\Auth;
use Qiniu\Storage\UploadManager;
use yii\log\Logger;

class OssUtil
{
    /**
     * 上传图片到七牛oss
     * @param $fileName  //上传成功后返回到图片名 加后缀
     * @param $file   //上传图片的物理路径
     * @param $picUrl
     * @param $error
     * @return bool
     */
    public static function UploadQiniuFile($fileName,$file,&$picUrl,&$error)
    {
        $params = \Yii::$app->params['QiNiuOss'];
        $auth = new Auth($params['ak'],$params['sk']);
        $bucket = $params['bucket'];
        $token = $auth->uploadToken($bucket);
        $uploadMgr = new UploadManager();
        list($picUrl, $error) = $uploadMgr->putFile($token, $fileName, $file);
        if($error !== null) {
            $error = '上传失败';
            return false;
        }
        $picUrl = $params['link'].$picUrl['key'];
        return true;
    }


    /**
     * 上传图片到oss
     * @param $picName  图片名称不包含后缀名
     * @param $suffix  图片后缀名
     * @param $fileName 图片物理路径，包含图片名称和后缀名
     * @param $picUrl 返回图片链接
     * @param $error 返回异常
     * @return bool
     * @throws oss\OSS_Exception
     */
    public static function UploadFile($picName,$suffix,$preFolder, $fileName, &$picUrl, &$error)
    {
       // 'BUCKET_NAME' => 'meiyuandemo',  //oss桶名称
	   //'BASE_URL' => 'http://meiyuan.oss-cn-hangzhou.aliyuncs.com/', //oss存储根路径
        //上传到阿里云的oss
        $oss_sdk_service = new ALIOSS();
        $bucket = \Yii::$app->params['oss']['bucket'];// 'meiyuandemo';
        $osspathname = $preFolder. '/' . $picName.'.'.$suffix;
        $response = $oss_sdk_service->upload_file_by_file($bucket,$osspathname,$fileName);
        if (!$response)
        {
            //oss上传失败
            $error = '上传失败';
            return false;
        }
        $picUrl = $response->header['_info']['url'];
        $picUrl = str_replace('oss-cn-hangzhou.aliyuncs.com/mblive','mbpic.mblive.cn',$picUrl);
        return true;
    }

    /**
     * 从oos获取图片高度和宽度信息
     * @param $url
     */
    public static function GetPicInfoFromOos($url, &$outUrl,&$error)
    {
        $tempUrl = 'http://image.matewish.cn/';
        if(strpos($url,$tempUrl) !== 0)
        {
            $error = '不是oos链接';
            return false;
        }
        $outUrl = $url;
        if(strpos($url,'@info') !== (strlen($url) -5))
        {
            $url .= '@info';
        }
        $rst = UsualFunForNetWorkHelper::HttpGet($url);
        if(!isset($rst) || empty($rst))
        {
            $error = '网络请求异常';
            return false;
        }
        $jsoninfo = json_decode($rst,true);
        if(!isset($jsoninfo) || !is_array($jsoninfo))
        {
            $error = '返回数据异常';
            \Yii::getLogger()->log($error.' detail:'.$rst, Logger::LEVEL_ERROR);
            return false;
        }
        $outUrl .= sprintf('?width=%s&height=%s',$jsoninfo['width'],$jsoninfo['height']);
        return true;
    }

    /**
     * 生成授权签名
     * @param $object
     * @param int $timeout
     * @param null $options
     * @return string
     */
    public static function GetSignUrl($object, $timeout = 60,$method=ALIOSS::OSS_HTTP_PUT, $options = NULL)
    {
        $oss_sdk_service = new ALIOSS();
        $bucket = \Yii::$app->params['oss']['bucket'];// 'meiyuandemo';
        return $oss_sdk_service->get_sign_url_new($bucket,$object,$timeout,$method,$options);
    }
} 