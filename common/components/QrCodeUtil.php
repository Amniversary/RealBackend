<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/2/25
 * Time: 13:10
 */

namespace common\components;


class QrCodeUtil
{
    /**
     * 获取生成二维码的url
     * @param $url
     * @return string|bool
     */
    public static function GetQrCodeUrl($content,&$error,$size=null,$margin=null)
    {
        if(empty($content))
        {
            $error = '生成二维码内容不能为空';
            return false;
        }
        $out = [];
        $time = time();
        $randStr = UsualFunForStringHelper::mt_rand_str(32);
        $input = [
            'url'=>$content,
            'time'=>$time,
            'rand_str'=>$randStr
        ];
        $sign = self::GetQrcodeSign($input);
        $params=[
            'mywish/getqrcodeimg',
            'url'=>$content,
            'time'=>$time,
            'rand_str'=>$randStr,
            'sign'=>$sign
        ];
        if(isset($size) && intval($size) > 0)
        {
            $params['size']=$size;
        }
        if(isset($margin) && intval($margin))
        {
            $params['margin']=$margin;
        }
        $url = \Yii::$app->urlManager->createAbsoluteUrl($params);
        return $url;
    }

    /**
     * 获取二维码签名
     * @param $input
     * input=[
     * 'url'=>'',
     * 'time'=>'',
     * 'rand_str'=>'',
     * ]
     * @return string
     */
    public static function GetQrcodeSign($input)
    {
        ksort($input);
        $str = '';
        foreach($input as $key=>$value)
        {
            $str .= $key.'='.$value.'&';
        }
        $str .= 'sowj02js0e=@#$&jsouf0wfsuf0w4645';
        return md5($str);
    }

    /**
     * 验证二维码签名
     * @param $input
     * input=[
     * 'url'=>'',
     * 'time'=>'',
     * 'rand_str'=>'',
     * 'sign'=>'',
     * ]
     * @return bool
     */
    public static function CheckQrcodeSign($input)
    {
        if(!isset($input) ||
            !is_array($input) ||
            empty($input['url']) ||
            empty($input['time']) ||
            empty($input['rand_str']) ||
            empty($input['sign']))
        {
            return false;
        }
        $sign = $input['sign'];
        unset($input['sign']);
        $sourceSign = self::GetQrcodeSign($input);
        return ($sign === $sourceSign);
    }
}