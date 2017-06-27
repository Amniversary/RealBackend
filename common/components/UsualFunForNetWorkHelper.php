<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 15-12-13
 * Time: 下午7:15
 */

namespace common\components;


use yii\log\Logger;

class UsualFunForNetWorkHelper
{
    /***
     * post请求数据
     */
    public static function HttpsPost($url, $data = null, $headers = null)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (!empty($data)){
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }

    /***
     * http的get访问方法
     */
    public static function HttpGet($url, $headers = null)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer

        curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
        //curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
        $output = curl_exec($curl);
        //echo '['.$output.']';
        return $output;
    }

    /**
     * 获取图片
     * @param $url
     * @param $content_type
     * @return bool|int|mixed
     */
    public static function HttpGetImg($url,&$content_type,&$error='')
    {
        $content_type = '';
        $count = 0;
        $e_no = 28;
        while($count < 10 && $e_no === 28)//网络超时，10次重复请求
        {
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
            curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器
            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
            curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer

            curl_setopt($curl, CURLOPT_TIMEOUT, 5); // 设置超时限制防止死循环
            //curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
            $output = curl_exec($curl);
            $content_type = curl_getinfo($curl,CURLINFO_CONTENT_TYPE);

            $e_no = curl_errno($curl);
            curl_close($curl);
            $count ++;
        }
        if($e_no !== 0)
        {
            $error = $e_no;
            if($e_no === 28)
            {
                \Yii::getLogger()->log('请求图片超时10次，放弃,url:'.$url,Logger::LEVEL_ERROR);
            }
            return false;
        }
        $content_type = strval($content_type);
        $ok_type=[
            'application/octet-stream',
            'image/bmp',
            'image/gif',
            'image/jpeg',
            'image/png'
        ];
        if(!in_array($content_type,$ok_type))
        {
            return false;
        }
        return $output;
    }

} 