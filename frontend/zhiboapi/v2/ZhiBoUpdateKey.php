<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 15-12-13
 * Time: 下午9:36
 */

namespace frontend\zhiboapi\v2;

use frontend\business\JobUtil;
use frontend\zhiboapi\IApiExcute;
use common\components\UsualFunForStringHelper;
use yii\log\Logger;

/**
 * Class 登录协议，注册也包含在此
 * @package frontend\zhiboapi\v2
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
        /*$jobServer = 'tokenBeanstalk';
        $data = [
            'key_word'=>'token_record',
            'token'=>$token,
            'token_key'=>$key,
            'txt'=>$txt,
            'succeed'=>'1',
            'no_save'=>'1',
        ];*/
        $txt = \Yii::$app->cache->get($key);
        if(!isset($txt) || empty($txt))
        {
            /*$data['succeed'] = '0';
            if(!JobUtil::AddCustomJob($jobServer,'token',$data,$error))
            {
                return false;
            }*/
            //$rstData['errno'] = '1';
            $error = '系统错误，缓存写入失败';
            return false;
        }
        /*if(!JobUtil::AddCustomJob($jobServer,'token',$data,$error))
        {
            return false;
        }*/

        $rstData['data_type'] = 'json';
        $rstData['data'] = $ary;
        //\Yii::getLogger()->log('update_key :'.var_export($rstData,true),Logger::LEVEL_ERROR);
        return true;
    }
} 