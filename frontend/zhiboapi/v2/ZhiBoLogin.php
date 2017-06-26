<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 15-12-13
 * Time: 下午9:36
 */

namespace frontend\zhiboapi\v2;

use common\components\tenxunlivingsdk\TimRestApi;
use frontend\business\ApiCommon;
use frontend\business\ClientUtil;
use frontend\business\JobUtil;
use frontend\zhiboapi\IApiExcute;
use yii\log\Logger;

/**
 * Class 登录协议，注册也包含在此
 * @package frontend\zhiboapi\v2
 */
class ZhiBoLogin implements IApiExcute
{
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

    public function excute_action($dataProtocal, &$rstData,&$error, $extendData= array())
    {
        $error ='蜜播版本过低，请下载最新版本';
        return false;
        if(!$this->check_param_ok($dataProtocal,$error))
        {
            return false;
        }
        $error = '';
        $userFinish = 2;
        $verifyCode = 0;
        $data = [];
        $uniqueNo = $dataProtocal['data']['unique_no'];//type 1 手机  2微信  3 新浪  4 QQ
        $deviceNo = $dataProtocal['device_no']; // 设备号
        $deviceType = $dataProtocal['device_type'];
        $getTuiId =$dataProtocal['data']['getui_id'];
        $registerType = $dataProtocal['data']['register_type'];//登录类型
        if($registerType == 1)
        {
            $verifyCode = $dataProtocal['data']['validate_code'];//验证码
        }

        $data['pic'] = $dataProtocal['data']['pic'];
        $data['nick_name']= $dataProtocal['data']['nick_name'];
        $data['sex'] = $dataProtocal['data']['sex'];
        $data['register_type'] = $dataProtocal['data']['register_type'];
        $data['unique_no'] = $uniqueNo;
        $data['device_no'] = $deviceNo;
        $data['device_type'] = $deviceType;
        $otherUniqueNo = strval($dataProtocal['data']['other_unique_no']);
        $ac = null;
        if(!empty($otherUniqueNo))
        {
            if($registerType == 2)//微信登录
            {
                $ac = ClientUtil::GetClientByUniqueNo($uniqueNo);
                if(isset($ac))
                {
                    if($ac->unique_no != $otherUniqueNo)
                    {
                        $ac1 = ClientUtil::GetClientByUniqueNo($otherUniqueNo);
                        if(!isset($ac1))
                        {
                            //更新unique_no
                            ClientUtil::UpdateUniqueNo($otherUniqueNo,$ac->client_id);
                            $ac->unique_no=$otherUniqueNo;
                            //从新设置缓存
                            $ary = array(
                                'device_no'=>$deviceNo,
                                'user_id'=>$ac->client_id,
                                'client_type'=>$ac->client_type,
                                'unique_no' => $uniqueNo,
                                'nick_name' =>$ac->nick_name,
                                'is_inner' =>$ac->is_inner
                            );
                            $str = serialize($ary);
                            \Yii::$app->cache->set('mb_api_login_'.$otherUniqueNo, $str,30*24*3600);//保持一个月
                            $loginInfoStr = \Yii::$app->cache->get('mb_api_login_'.$otherUniqueNo);
                            if(!isset($loginInfoStr) || empty($loginInfoStr))
                            {
                                $error = '存储登录信息异常，登录失败';
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
                    if($ac->unique_no != $uniqueNo)
                    {
                        //更新unique_no
                        ClientUtil::UpdateUniqueNo($uniqueNo,$ac->client_id);
                        $ac->unique_no=$uniqueNo;
                        //从新设置缓存
                        $ary = array(
                            'device_no'=>$deviceNo,
                            'user_id'=>$ac->client_id,
                            'client_type'=>$ac->client_type,
                            'unique_no' => $uniqueNo,
                            'nick_name' =>$ac->nick_name,
                            'is_inner' =>$ac->is_inner
                        );
                        $str = serialize($ary);
                        \Yii::$app->cache->set('mb_api_login_'.$uniqueNo, $str,30*24*3600);//保持一个月
                        $loginInfoStr = \Yii::$app->cache->get('mb_api_login_'.$uniqueNo);
                        if(!isset($loginInfoStr) || empty($loginInfoStr))
                        {
                            $error = '存储登录信息异常，登录失败';
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
        $other = ClientUtil::GetClientOtherInfo($uniqueNo);
        $loginInfoStr = \Yii::$app->cache->get('mb_api_login_'.$uniqueNo); //从memcache 获取数据

        //\Yii::getLogger()->log('datainfo:'.var_export($dataProtocal,true),Logger::LEVEL_ERROR);
         //检查是否已经登录
        if(!empty($loginInfoStr))
        {
            if(!isset($ac))
            {
                $error = '系统错误，找不到用户';
                \Yii::getLogger()->log($error.':'.var_export($ac,true),Logger::LEVEL_ERROR);
                \Yii::$app->cache->delete('mb_api_login_'.$uniqueNo);
                return false;
            }
            if($ac->status == 0)
            {
                $error = '你已被禁用，请与管理员联系';
                return false;
            }
            $loginInfo = unserialize($loginInfoStr);
            if(!isset($loginInfo) || empty($loginInfo) || !is_array($loginInfo))
            {
                $error = '系统错误，登录信息解析异常';
                return false;
            }
            if($registerType == 1)//手机登录
            {
                $vCode = \Yii::$app->cache->get('mb_api_verifycode_1_'.$uniqueNo);//获取memcache验证码
                \Yii::getLogger()->log('缓存验证码:'.$vCode.'  收到验证码:'.$verifyCode.' 登录信息验证码:'.$loginInfo['verify_code'],Logger::LEVEL_ERROR);
                if(!empty($loginInfo['verify_code']))
                {
                    if($vCode != $verifyCode)//比较验证码
                    {
                        \Yii::getLogger()->log('缓存验证码:'.$vCode.'  收到验证码:'.$verifyCode,Logger::LEVEL_ERROR);
                        \Yii::$app->cache->delete('mb_api_verifycode_1_'.$uniqueNo);

                        $error = '验证码信息不匹配，登录失败';
                        return false;
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
                            \Yii::getLogger()->log('s:'.$loginInfo['verify_code'].' r:'.$verifyCode,Logger::LEVEL_ERROR);
                            $error = ['errno'=>'1106','errmsg'=>'验证码信息错误，登录失败'];
                            \Yii::$app->cache->delete('mb_api_verifycode_1_'.$uniqueNo);
                            return false;
                        }
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
                $ary = array(
                    'device_no'=>$deviceNo,
                    'user_id'=>$ac->client_id,
                    'client_type'=>$ac->client_type,
                    'unique_no' => $uniqueNo,
                    'nick_name' =>$ac->nick_name,
                    'is_inner' =>$ac->is_inner
                );
                $str = serialize($ary);
                \Yii::$app->cache->set('mb_api_login_'.$uniqueNo, $str,30*24*3600);//保持一个月
                $loginInfoStr = \Yii::$app->cache->get('mb_api_login_'.$uniqueNo);
                if(!isset($loginInfoStr) || empty($loginInfoStr))
                {
                    $error = '存储登录信息异常，登录失败';
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
                    if($vCode !== $verifyCode)
                    {
                        \Yii::$app->cache->delete('mb_api_verifycode_1_'.$uniqueNo);
                        $error = ['errno'=>'1106','errmsg'=>'验证码信息错误，登录失败'];
                        return false;
                    }
                    if(!isset($ac))
                    {
                        //过滤掉已经被禁用的设备
                        $deviceNoInfo = ClientUtil::GetClientDeviceNoIsRes($deviceNo);
                        if(isset($deviceNoInfo))
                        {
                            $error = '该设备已经被禁用，不能注册';
                            return false;
                        }

                        //注册信息
                        if(!ClientUtil::RegisterUser($uniqueNo,$deviceNo,$data,$getTuiId,$error)){
                            return false;
                        }

                        $ac = ClientUtil::GetUserByUniqueId($uniqueNo);
                        
                    }
                    $ary = array(
                        'device_no'=>$deviceNo,
                        'verify_code' => $verifyCode,
                        'user_id'=>$ac->client_id,
                        'client_type'=>$ac->client_type,
                        'unique_no' => $uniqueNo,
                        'nick_name' =>$ac->nick_name,
                        'is_inner' => $ac->is_inner
                    );
                    $str = serialize($ary);
                    \Yii::$app->cache->set('mb_api_login_'.$uniqueNo, $str,30*24*3600);//保持一个月
                    $loginInfoStr = \Yii::$app->cache->get('mb_api_login_'.$uniqueNo);
                    if(!isset($loginInfoStr) || empty($loginInfoStr))
                    {
                        $error = '存储登录信息异常，登录失败';
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

                if(!isset($ac)) //是否注册
                {
                    //注册信息

                    if(isset($other))
                    {
                        $error = '第三方信息已存在，不能重复注册!';
                        return false;
                    }

                    //过滤掉已经被禁用的设备
                    $deviceNoInfo = ClientUtil::GetClientDeviceNoIsRes($deviceNo);
                    if(isset($deviceNoInfo))
                    {
                        $error = '该设备已经被禁用，不能注册';
                        return false;
                    }

                    if(!ClientUtil::RegisterUser($uniqueNo,$deviceNo,$data,$getTuiId,$error)){
                        return false;
                    }
                    $ac = ClientUtil::GetUserByUniqueId($uniqueNo);

                }
                $ary = array(  //保存信息到memcache
                    'device_no'=>$deviceNo,
                    'user_id'=>$ac->client_id,
                    'client_type'=>$ac->client_type,
                    'unique_no' => $uniqueNo,
                    'nick_name' =>$ac->nick_name,
                    'is_inner' => $ac->is_inner
                );
                $str = serialize($ary);
                \Yii::$app->cache->set('mb_api_login_'.$uniqueNo, $str,30*24*3600);//保持一个月
                $loginInfoStr = \Yii::$app->cache->get('mb_api_login_'.$uniqueNo);
                if(!isset($loginInfoStr) || empty($loginInfoStr))
                {
                    $error = '存储登录信息异常，登录失败';
                    return false;
                }
            }
        }

        //\Yii::getLogger()->log('getuiid:'.$getTuiId,Logger::LEVEL_ERROR);
        if($getTuiId === 'null')
        {
            $error = '个推id不能为null';
            return false;
        }

        $ac->getui_id = $getTuiId;
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
        ];

        return true;
    }

} 