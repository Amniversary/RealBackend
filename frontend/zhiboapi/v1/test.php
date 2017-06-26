<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 15-12-13
 * Time: 下午9:36
 */

namespace frontend\zhiboapi\v1;

use common\components\DeviceUtil;
use common\components\tenxunlivingsdk\TimRestApi;
use common\models\Client;
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
        $uniqueNo = $dataProtocal['data']['unique_no']; //TODO: 用户唯一号
        $deviceNo = $dataProtocal['device_no']; //TODO:设备号
        $deviceType = $dataProtocal['device_type']; //TODO: type ios or 安卓
        $getTuiId = $dataProtocal['data']['getui_id']; //TODO:个推Id
        $registerType = intval($dataProtocal['data']['register_type']);//TODO: type 1 手机  2微信  3 微博  4 QQ
        if  ($registerType == 3) {
            $error = '微博登录开发中，敬请期待！';
            return false;
        }
        if($registerType == 1 && isset($dataProtocal['data']['validate_code'])) {
            $verifyCode = $dataProtocal['data']['validate_code'];//验证码
        }
        $otherUniqueNo = strval($dataProtocal['data']['other_unique_no']);
        $ac = null;
        if(!empty($otherUniqueNo))
        {
            switch($registerType) //TODO: 微信登录 微博登录  更新第三方unionid  openid  新浪
            {
                case 2:
                    $ac = ClientUtil::GetClientByUniqueNo($uniqueNo);
                    $uniqueNo = $otherUniqueNo;
                    break;
                case 3:
                    $ac = ClientUtil::GetClientInfoByXinLangUid($uniqueNo);
                    break;
            }
            if(isset($ac)) {
                if($ac['unique_no'] != $uniqueNo) {
                    $ac1 = ClientUtil::GetClientByUniqueNo($uniqueNo);
                    if(!isset($ac1)) {
                        ClientUtil::UpdateUniqueNo($uniqueNo,$ac->client_id);
                        $ac['unique_no'] = $uniqueNo;
                        //TODO: 重新设置缓存
                        $ary = [
                            'device_no'=>$deviceNo,
                            'user_id'=>$ac['client_id'],
                            'client_type'=>$ac['client_type'],
                            'unique_no' => $ac['unique_no'],
                            'nick_name' =>$ac['nick_name'],
                            'is_inner' =>$ac['is_inner']];
                        if(!ApiCommon::setLoginCache($ary,$error)) {
                            return false;
                        }
                    } else {
                        $ac = $ac1;
                    }
                    unset($ac1);
                }
            }
        }
        $len = strlen($uniqueNo);
        if($len < 10) {
            $error = 'UniqueNo参数格式，不正确';
            return false;
        }
        if($getTuiId === 'null')
        {
            $error = '个推id不能为null';
            return false;
        }
        if(!isset($ac)) {
            $ac = ClientUtil::GetUserByUniqueId($uniqueNo); //根据标识 获取数据
        }
        //TODO: 检查是否已经登录
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
                \Yii::error($error.': '.var_export($ac->attributes,true),Logger::LEVEL_ERROR);
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
                $vCode = \Yii::$app->cache->get('mb_api_verifycode_1_'.$uniqueNo); //获取memcache验证码
                \Yii::error('缓存验证码:'.$vCode.'  收到验证码:'.$verifyCode,Logger::LEVEL_ERROR);
                \Yii::error('loggin:'.var_export($loginInfo,true),Logger::LEVEL_ERROR);
                if(empty($vCode)) {
                    $error = ['errno'=>'1106','errmsg'=>'验证码已过期，请重新获取验证码！'];
                    return false;
                }
                if($vCode != $verifyCode) {
                    \Yii::error('缓存验证码:'.$vCode.'  收到验证码:'.$verifyCode,Logger::LEVEL_ERROR);
                    $error = '验证码信息不匹配，登录失败！'; //，请重新获取验证码
                    return false;
                }
            }
        }
        else //未登录
        {
            if($registerType == 1)//是不是手机登录注册
            {
                $vCode = \Yii::$app->cache->get('mb_api_verifycode_1_'.$uniqueNo);
                if(empty($vCode)){
                    $error = ['errno'=>'1107','errmsg'=>'无法获取验证码，登录失败'];
                    return false;
                }
                if($vCode != $verifyCode)
                {
                    //\Yii::$app->cache->delete('mb_api_verifycode_1_'.$uniqueNo);
                    $error = ['errno'=>'1106','errmsg'=>'验证码信息错误，登录失败！']; //，请重新获取验证码
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
                //TODO: 过滤掉已经被禁用的设备
                $deviceNoInfo = ClientUtil::GetClientDeviceNoIsRes($deviceNo);
                if(isset($deviceNoInfo))
                {
                    $error = '该设备已经被禁用，不能注册';
                    \Yii::error($error.':  device_no1:'.$deviceNo,Logger::LEVEL_ERROR);
                    return false;
                }
                if(DeviceUtil::IsErrorDevice($deviceNo))
                {
                    $error = '设备不正确';
                    \Yii::error($error.':   deviceNo:'.$deviceNo,Logger::LEVEL_ERROR);
                    return false;
                }
                //注册信息
                if(!ClientUtil::RegisterUserQiNiu($uniqueNo,$deviceNo,$data,$getTuiId,$error,true)){
                    return false;
                }
                $ac = ClientUtil::GetUserByUniqueId($uniqueNo);
            }
        }

        $ary = [
            'device_no'=>$deviceNo,
            'user_id'=>$ac->client_id,
            'client_type'=>$ac->client_type,
            'unique_no' => $uniqueNo,
            'nick_name' =>$ac->nick_name,
            'is_inner'=>$ac->is_inner,
        ];
        if(!ApiCommon::setLoginCache($ary,$error)) {
            return false;
        }
        $ac->app_id = $dataProtocal['app_id'];
        $ac->getui_id = $getTuiId;
        $ac->modify_time = date('Y-m-d H:i:s');
        if(!$ac->save()) {
            $error = '更新用户登录信息失败';
            \Yii::error($error.' :'.var_export($ac->getErrors(),true));
            return false;
        }
        if(empty($ac->nick_name) ||
            empty($ac->pic) ||
            empty($ac->sex)) {
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
        }
        $sm = new Stream(null,json_decode($qiniu_info,true));

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