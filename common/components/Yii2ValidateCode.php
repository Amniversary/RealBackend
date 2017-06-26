<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/5/18
 * Time: 10:44
 */

namespace common\components;


use yii\base\Exception;

class Yii2ValidateCode
{
    //获取验证码链接
    //http://front.zhibo.cn/mbliving/piccode
    /**
     * 返回当前验证码
     * @return string
     * @throws Exception
     */
    public static function GetPicValidateCode()
    {
        list($controller,$route) = \Yii::$app->createController('mbliving');
        if(!($controller instanceof \yii\web\Controller))
        {
            throw new Exception('不是yii\web\Controller对象');
        }
        $action = $controller->createAction('piccode');
        return $action->getVerifyCode(false);
    }
    //获取验证码链接
    //http://front.zhibo.cn/mbliving/piccode
    /**
     * 返回当前验证码
     * @return string
     * @throws Exception
     */
    public static function GetPicValidateCode2()
    {
        list($controller,$route) = \Yii::$app->createController('verifycode');
        if(!($controller instanceof \yii\web\Controller))
        {
            throw new Exception('不是yii\web\Controller对象');
        }
        $action = $controller->createAction('piccode');
        return $action->getVerifyCode(false);
    }
    /**
     * 验证码验证
     * @param $inputCode
     * @return bool
     * @throws Exception
     */
    public static function ValidatePicCode($inputCode)
    {
        $vCode = self::GetPicValidateCode();
        return strtolower($vCode) === strtolower($inputCode);
    }
    /**
     * 验证码验证
     * @param $inputCode
     * @return bool
     * @throws Exception
     */
    public static function ValidatePicCode2($inputCode)
    {
        $vCode = self::GetPicValidateCode2();
        return strtolower($vCode) === strtolower($inputCode);
    }
}