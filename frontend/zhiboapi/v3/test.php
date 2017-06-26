<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 15-12-13
 * Time: 下午9:36
 */

namespace frontend\zhiboapi\v3;

use common\components\DeviceUtil;
use common\components\tenxunlivingsdk\TimRestApi;
use frontend\business\ApiCommon;
use frontend\business\ClientUtil;
use frontend\zhiboapi\IApiExcute;
use Pili\Stream;
use yii\log\Logger;

/**
 * Class 七牛登录协议，注册也包含在此
 * @package frontend\zhiboapi\v2
 */
class test implements IApiExcute
{

    public function excute_action($dataProtocal, &$rstData,&$error = '', $extendData= array())
    {
        if(!$this->check_param_ok($dataProtocal,$error))
        {
            return false;
        }
        $userFinish = 2;
        $verifyCode = 0;

        $uniqueNo = $dataProtocal['data']['unique_no'];
        $deviceNo = $dataProtocal['device_no']; //TODO:设备号
        $deviceType = $dataProtocal['device_type']; //TODO: type ios or 安卓
        $getTuiId = $dataProtocal['data']['getui_id']; //TODO:个推Id
        $registerType = intval($dataProtocal['data']['register_type']);//TODO: type 1 手机  2微信  3 微博  4 QQ
        if  ($registerType == 3)
        {
            $error = '微博登录开发中，敬请期待！';
            return false;
        }

        if($registerType == 1)
        {
            $verifyCode = $dataProtocal['data']['validate_code'];//验证码
        }

        $otherUniqueNo = strval($dataProtocal['data']['other_unique_no']);
        $ac = null;
        if(!empty($otherUniqueNo))
        {
            if($registerType == 2)//微信登录
            {
                $ac = ClientUtil::GetClientByUniqueNo($uniqueNo);
                if(isset($ac))
                {
                    if($ac['unique_no'] != $otherUniqueNo)
                    {
                        $ac1 = ClientUtil::GetClientByUniqueNo($otherUniqueNo);
                        if(!isset($ac1))
                        {
                            //TODO: 更新 unique_no 替换原有的openid 统一使用 unionid
                            ClientUtil::UpdateUniqueNo($otherUniqueNo,$ac->client_id);
                            $ac['unique_no'] = $otherUniqueNo;
                            //重新设置缓存
                            $ary = [
                                'device_no'=>$deviceNo,
                                'user_id'=>$ac['client_id'],
                                'client_type'=>$ac['client_type'],
                                'unique_no' => $ac['unique_no'],
                                'nick_name' =>$ac['nick_name'],
                                'is_inner' =>$ac['is_inner']];
                            if(!ApiCommon::setLoginCache($ary,$error))
                            {
                                return false;
                            }
                        }
                        else
                        {
                            $ac = $ac1;
                        }
                    }
                }
                $uniqueNo = $otherUniqueNo;
            }
            else if($registerType == 3)//微博登录
            {
                $ac = ClientUtil::GetClientInfoByXinLangUid($uniqueNo);
                if(isset($ac))
                {
                    if($ac['unique_no'] != $uniqueNo)
                    {
                        //更新unique_no
                        ClientUtil::UpdateUniqueNo($uniqueNo,$ac['client_id']);
                        $ac['unique_no'] = $uniqueNo;
                        //从新设置缓存
                        $ary = array(
                            'device_no'=>$deviceNo,
                            'user_id'=>$ac['client_id'],
                            'client_type'=>$ac['client_type'],
                            'unique_no' => $uniqueNo,
                            'nick_name' =>$ac['nick_name'],
                            'is_inner' =>$ac->is_inner
                        );
                        if(!ApiCommon::setLoginCache($ary,$error))
                        {
                            return false;
                        }
                    }
                }
            }
        }
        if(!isset($ac))
        {
            $ac = ClientUtil::GetUserByUniqueId($uniqueNo); //根据标识 获取数据
        }
        //检查是否已经登录
        if(ApiCommon::GetLoginInfo($uniqueNo,$loginInfo,$error))
        {
            $deviceNoInfo = ClientUtil::GetClientDeviceNoIsRes($deviceNo);
            if(isset($deviceNoInfo))
            {
                $error = '该设备已经被禁用，无法登录';
                \Yii::error($error.':  device_no:'.$deviceNo,Logger::LEVEL_ERROR);
                return false;
            }
            if(!isset($ac))
            {
                $error = '系统错误，找不到用户';
                \Yii::error($error.': '.var_export($ac,true),Logger::LEVEL_ERROR);
                \Yii::$app->cache->delete('mb_api_login_'.$uniqueNo);
                return false;
            }
            if($ac->status == 0)
            {
                $error = '你已被禁用，请与管理员联系';
                \Yii::error($error. ':  unique_no:'.$uniqueNo,Logger::LEVEL_ERROR);
                return false;
            }

            if($registerType == 1)//手机登录
            {
                $vCode = \Yii::$app->cache->get('mb_api_verifycode_1_'.$uniqueNo);//获取memcache验证码
                \Yii::error('缓存验证码:'.$vCode.'  收到验证码:'.$verifyCode,Logger::LEVEL_ERROR);
                \Yii::error('loggin:'.var_export($loginInfo,true),Logger::LEVEL_ERROR);
                if(!empty($loginInfo['verify_code']))
                {
                    if($vCode != $verifyCode)//比较验证码
                    {
                        \Yii::error('缓存验证码:'.$vCode.'  收到验证码:'.$verifyCode,Logger::LEVEL_ERROR);
                        \Yii::$app->cache->delete('mb_api_verifycode_1_'.$uniqueNo);
                        $error = '验证码信息不匹配，登录失败，请重新获取验证码！';
                        return false;
                    }
                }
                else
                {
                    if($loginInfo['verify_code'] != $verifyCode)
                    {
                        if($loginInfo['device_no'] !== $deviceNo)
                        {
                            $error = ['errno'=>'1101','errmsg'=>'您的账号已经在其他设备上登录'];//'您的账户已经在其他设备上登录';
                            return false;
                        }
                        else
                        {
                            $error = ['errno'=>'1106','errmsg'=>'验证码信息错误，登录失败，请重新获取验证码！'];
                            \Yii::$app->cache->delete('mb_api_verifycode_1_'.$uniqueNo);
                            return false;
                        }
                    } else {
                        $error = ['errno'=>'1106','errmsg'=>'验证码已过期，请重新获取验证码！'];
                        return false;
                    }
                }
            }
            else
            {
                $len = strlen($uniqueNo);
                if($len < 10)
                {
                    $error = '第三方ID，不正确';
                    return false;
                }
            }
        }
        else //未登录
        {
            if(empty($dataProtocal['data']['getui_id']))
            {
                $error = '个推id不能为空';
                return false;
            }

            if($registerType == 1)//是不是手机登录注册
            {
                $vCode = \Yii::$app->cache->get('mb_api_verifycode_1_'.$uniqueNo);
                if(!empty($vCode))
                {
                    if($vCode != $verifyCode)
                    {
                        \Yii::$app->cache->delete('mb_api_verifycode_1_'.$uniqueNo);
                        $error = ['errno'=>'1106','errmsg'=>'验证码信息错误，登录失败，请重新获取验证码！'];
                        return false;
                    }
                }
                else
                {
                    $error = ['errno'=>'1107','errmsg'=>'无法获取验证码，登录失败'];
                    return false;
                }
            }
            else //不是手机登录
            {
                $len = strlen($uniqueNo);
                if($len < 10)
                {
                    $error = '第三方id，不正确';
                    return false;
                }
            }
            $data = [
                'pic' => $dataProtocal['data']['pic'],
                'nick_name' => $dataProtocal['data']['nick_name'],
                'sex'=> $dataProtocal['data']['sex'],
                'register_type' => $registerType,
                'unique_no' => $uniqueNo,
                'device_no' => $deviceNo,
                'device_type' => $deviceType,
            ];
            if(!isset($ac))
            {
                //过滤掉已经被禁用的设备
                $deviceNoInfo = ClientUtil::GetClientDeviceNoIsRes($deviceNo);
                if(isset($deviceNoInfo))
                {
                    \Yii::getLogger()->log('device_no1:'.$deviceNo,Logger::LEVEL_ERROR);
                    $error = '该设备已经被禁用，不能注册';
                    return false;
                }
                if(DeviceUtil::IsErrorDevice($deviceNo))
                {
                    \Yii::getLogger()->log('设备不正确   $deviceNo===:'.$deviceNo,Logger::LEVEL_ERROR);
                    $error = '设备不正确';
                    return false;
                }
                //注册信息
                if(!ClientUtil::RegisterUserQiNiu($uniqueNo,$deviceNo,$data,$getTuiId,$error,true)){
                    return false;
                }

                $ac = ClientUtil::GetUserByUniqueId($uniqueNo);
            }
        }

        $ary = array(
            'device_no'=>$deviceNo,
            'verify_code' => $verifyCode,
            'user_id'=>$ac->client_id,
            'client_type'=>$ac->client_type,
            'unique_no' => $uniqueNo,
            'nick_name' =>$ac->nick_name,
            'is_inner'=>$ac->is_inner
        );
        $str = serialize($ary);
        \Yii::$app->cache->set('mb_api_login_'.$uniqueNo, $str,30*24*3600);//保持一个月
        $loginInfoStr = \Yii::$app->cache->get('mb_api_login_'.$uniqueNo);
        if(!isset($loginInfoStr) || empty($loginInfoStr))
        {
            $error = '存储登录信息异常，登录失败';
            return false;
        }
        //\Yii::getLogger()->log('getuiid:'.$getTuiId,Logger::LEVEL_ERROR);
        if($getTuiId === 'null')
        {
            $error = '个推id不能为null';
            return false;
        }
        if($registerType == 1)
        {
            $ac->vcode = $vCode;
        }

        $ac->getui_id = $getTuiId;
        $ac->modify_time = date('Y-m-d H:i:s');
        //\Yii::getLogger()->log('ac :'.var_export($ac,true),Logger::LEVEL_ERROR);
        if(!$ac->save())
        {
            $error = '更新个推id失败';
            \Yii::getLogger()->log($error.' :'.var_export($ac->getErrors(),true),Logger::LEVEL_ERROR);
            return false;
        }

        if(empty($ac->nick_name) || empty($ac->pic) || empty($ac->sex))
        {
            $userFinish = 1;
        }
        //获取七牛直播信息
        $key = 'qiniu_living_'.strval($ac->client_id);
        $qiniu_info = \Yii::$app->cache->get($key);
        if($qiniu_info === false)
        {
            if(!ClientUtil::GenQiNiuInfoForClient($uniqueNo,$ac->client_id,$qiniu_info,$error))
            {
                return false;
            }
            /*            $error = '七牛直播信息丢失';
                        return false;*/
        }
        $sm = new Stream(null,json_decode($qiniu_info,true));
        //TODO: getToken 融云 修改腾讯im
        TimRestApi::init();
        $sign = TimRestApi::generate_user_sig(strval($ac->client_id));


        $rstData['has_data']='1';
        $rstData['data_type']="json";
        $rstData['data']=[
            'user_id'=>strval($ac->client_id),
            'user_sign'=> $sign[0],
            'user_finish'=>$userFinish,
            'user_info'=>[
                'nick_name'=>$ac->nick_name,
                'pic'=>$ac->pic,
                'city'=>$ac->city,
                'sex'=>$ac->sex,
                'age'=>$ac->age,
                'sign_name'=>$ac->sign_name,
                'is_contract'=>$ac->is_contract,
                'real_validate'=>$ac->is_centification,
            ],
            'qiniu_info'=>$qiniu_info,
            'living_pic_url'=> $sm->GetLivingSnapUrl()
        ];

        $retVal = ClientUtil::UpdateClientAppIDToLogin( $ac->client_id,$dataProtocal['app_id'] );
        if( !$retVal )
        {
            \Yii::getLogger()->log("用户登陆时更新用户的app_id发生成错误",Logger::LEVEL_ERROR);
        }

        return true;
    }


    private function check_param_ok($dataProtocal,&$error='')
    {
        $fields = ['unique_no','getui_id'];
        $fieldLabels = ['唯一标识','个推id'];
        $len =count($fields);
        for($i = 0; $i <$len; $i ++)
        {
            if (!isset($dataProtocal['data'][$fields[$i]]) || empty($dataProtocal['data'][$fields[$i]])) {
                $error = $fieldLabels[$i] . '，不能为空';
                return false;
            }
        }

        return true;
    }
} 