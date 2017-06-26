<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/2/26
 * Time: 11:49
 */

namespace common\components;



use common\models\AccountInfo;
use frontend\business\ClientUtil;
use frontend\business\PersonalUserUtil;
use yii\log\Logger;

class ValidateCodeUtil
{
    /**
     * 检测验证码
     * @param $phone_no
     * @param $code_type
     * @param $error
     */
    public static function CheckValidateCode($phone_no,$code_type,$vcode)
    {
        $key = 'mb_api_verifycode_'.$code_type.'_'.$phone_no;
        $tmpVcode = \Yii::$app->cache->get($key);
        \Yii::$app->cache->delete($key);
        if($tmpVcode !== $vcode)
        {
            \Yii::getLogger()->log('验证码错误：'.$tmpVcode.' send:'.$vcode,Logger::LEVEL_ERROR);
            return false;
        }
        return true;
    }

    /**
     * 发送验证码
     * @param $phoneNo
     * @param $code_type
     * @param $error
     * @return bool
     */
    public static function SendValidate($phoneNo,$code_type,&$error,$device_no)
    {
        $vCode  =UsualFunForStringHelper::mt_rand_str(4,'0123456789');
        //发送验证码
        $pars = array(
            'param1'=>$vCode,//验证码
            'param2'=>SystemParamsUtil::GetSystemParam('system_customer_call',true)//客服电话
        );
        $error = '';

        $is_test = \Yii::$app->params['is_test'];
        if($is_test == '1')
        {
            $vCode = '8888';
        }
        $testUserList = require(__DIR__.'/../../frontend/config/TestUserConfig.php');
        if(in_array($phoneNo,$testUserList))
        {
            $vCode = '8888';
        }
        $lock = new PhpLock('mb_api_verifycode_'.$code_type.'_'.$phoneNo.'_'.date('Y-m-d'));
        $lock->lock();
        $ts = \Yii::$app->cache->get('mb_api_verifycode_'.$code_type.'_'.$phoneNo.'_'.date('Y-m-d'));

        $ts = intval($ts);
        $maxNum = SystemParamsUtil::GetSystemParam('system_phone_message_no',true);
        //$ts = 1;
        if($vCode == '8888')
        {
            $ts = 0;
        }
        if($ts > $maxNum)
        {
            $error = '发送次数过多';
            \Yii::$app->cache->delete('mb_api_verifycode_'.$code_type.'_'.$phoneNo.'_'.date('Y-m-d'));
            \Yii::getLogger()->log($error.' type:'.$code_type,Logger::LEVEL_ERROR);
            $lock->unlock();
            return false;
        }
        else
        {
            //检测是否注册，如果是，检测同一个设备号最多注册三个手机
            if($code_type == '1' || $code_type == '2')
            {
                if(!ClientUtil::CouldReciveShortmsg($phoneNo,$device_no,$error))
                {
                    \Yii::getLogger()->log($error.' device_no:'.$device_no.' phone:'.$phoneNo,Logger::LEVEL_ERROR);
                    $lock->unlock();
                    return false;
                }
            }
            $deviceSendNum = 0;
            if($vCode != '8888')
            {
                if(!self::IsCouldSendMsg($phoneNo,$deviceSendNum,$device_no,$error))
                {
                    \Yii::getLogger()->log($error.' type:'.$code_type,Logger::LEVEL_ERROR);
                    $lock->unlock();
                    return false;
                }
            }
            \Yii::getLogger()->log('vcode:['.$vCode.']验证码类型：'.$code_type.'发送验证码IP地址：'.DeviceUtil::GetClientRealIp(),Logger::LEVEL_ERROR);
            if($is_test == '1' || $vCode == '8888')
            {

            }
            else
            {
                if(!SendShortMessage::SendMessageDaHanSanTong($phoneNo,$code_type,$pars, $error))
                {
                    \Yii::getLogger()->log($error.':发送验证码错误',Logger::LEVEL_ERROR);
                    $lock->unlock();
                    return false;
                }
            }
            $ts ++;
            $deviceSendNum ++;
            \Yii::$app->cache->set('mb_api_verifycode_'.$code_type.'_'.$phoneNo.'_'.date('Y-m-d'),strval($ts),60*60*24);

            \Yii::$app->cache->set('mb_api_verifycode_device_'.$device_no.'_'.date('Y-m-d'),strval($deviceSendNum),60*60*24);
        }
        $lock->unlock();

        \Yii::getLogger()->log('vcode:'.$vCode,Logger::LEVEL_ERROR);
        //20分钟内生效
        \Yii::$app->cache->set('mb_api_verifycode_'.$code_type.'_'.$phoneNo, $vCode, 60*20);
        //\Yii::getLogger()->log('设置结果:'.$rst,Logger::LEVEL_ERROR);
        //\Yii::getLogger()->log('设置缓存的结果:'.\Yii::$app->cache->get('mb_api_verifycode_'.$code_type.'_'.$phoneNo),Logger::LEVEL_ERROR);
        //\Yii::getLogger()->log('设置验证码为:'.$vCode, Logger::LEVEL_ERROR);
        \Yii::getLogger()->log('mb_api_verifycode_'.$code_type.'_'.$phoneNo, Logger::LEVEL_ERROR);

        $txt = \Yii::$app->cache->get('mb_api_verifycode_'.$code_type.'_'.$phoneNo);
        if(!isset($txt) || empty($txt))
        {
            $error = '系统错误，验证码写入失败';
            return false;
        }
        $ac = ClientUtil::GetClientByUniqueNo($phoneNo);
        if(isset($ac))
        {
            $ac->vcode = $vCode;
            if(!$ac->save())
            {
                $error = '更新用户验证码信息失败';
                \Yii::getLogger()->log($error.' '.var_export($ac->getError(),true),Logger::LEVEL_ERROR);
                return false;
            }
        }

        return true;
    }

    /**
     * 同一设备号，一天不能超过9条短信
     * @param $phone_no 手机号
     * @param $deviceNum 设备短信发送次数
     * @param $device_no 设备号
     * @param $error 错误信息
     * @return bool
     */
    public static function IsCouldSendMsg($phone_no,&$deviceNum,$device_no,&$error)
    {
        if(empty($device_no))
        {
            $error = '设备号不存在，发送短信失败';
            return false;
        }
        /*if($phone_no === 'YUYUE')//暂时不做过滤
        {
            return true;
        }*/
        $maxNum = SystemParamsUtil::GetSystemParam('system_device_message_no',true);
        $ts = \Yii::$app->cache->get('mb_api_verifycode_device_'.$device_no.'_'.date('Y-m-d'));
        $ts = intval($ts);
        if($ts > $maxNum)
        {
            $error = '短信发送次数过多';
            \Yii::getLogger()->log('同一设备号发送次数过多 deviceno:'.$device_no,Logger::LEVEL_ERROR);
            return false;
        }
        $deviceNum = $ts;
        return true;
    }
} 