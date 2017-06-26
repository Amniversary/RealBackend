<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 15-12-13
 * Time: 下午9:36
 */

namespace frontend\zhiboapi\v3;

use frontend\business\JobUtil;
use frontend\zhiboapi\IApiExcute;
use common\components\UsualFunForStringHelper;
use yii\log\Logger;

/**
 * Class 登录协议，注册也包含在此
 * @package frontend\zhiboapi\v3
 */
class ZhiBoUpdateKey implements IApiExcute
{
    public function excute_action($dataProtocal, &$rstData,&$error, $extendData= array())
    {

        $token = sha1(UsualFunForStringHelper::CreateGUID());

        $str = '0123456789`~!@#$%^&*()_+=-|qwertyuiopasdfghjklzxcvbnmMNBVCXZLKJHGFDSAPOIUYTREWQ';
        $sign_key = UsualFunForStringHelper::mt_rand_str(32, $str);
        $crypt_key = UsualFunForStringHelper::mt_rand_str(32,$str);
        if(\Yii::$app->params['is_test'] === '1')
        {
            $ary = \Yii::$app->params['test_param_api'];
            $token = $ary['token'];
        }
        else
        {
            $ary = array(
                'token'=>$token,
                'sign_key' => $sign_key,
                'crypt_key' => $crypt_key
            );
        }

        $txt = serialize($ary);

        //24小时有效
        $key = 'my_api_key_'.$token;
        $rst = \Yii::$app->cache->set($key, $txt,24*3600);

        $txt = \Yii::$app->cache->get($key);
        if(!isset($txt) || empty($txt))
        {

            $error = '系统错误，缓存写入失败';
            return false;
        }

        //\Yii::error('date_update_key_V3:'.var_export($ary,true));
        $rstData['data_type'] = 'json';
        $rstData['data'] = $ary;

        return true;
    }
} 