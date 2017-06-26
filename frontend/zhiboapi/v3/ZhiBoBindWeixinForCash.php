<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/4/26
 * Time: 14:50
 */

namespace frontend\zhiboapi\v3;


use frontend\business\ApiCommon;
use frontend\business\ClientInfoUtil;
use frontend\zhiboapi\IApiExcute;
use yii\log\Logger;

/**
 * 绑定微信
 * Class ZhiBoBindWeixinForCash
 * @package frontend\zhiboapi\v3
 */
class ZhiBoBindWeixinForCash implements IApiExcute
{
    private function check_param_ok($dataProtocal,&$error='')
    {
        $fields = ['openid','nick_name','pic','sex'];
        $fieldLabels = ['微信openid','用户昵称','头像信息','性别信息'];
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

    function excute_action($dataProtocal, &$rstData,&$error, $extendData= array())
    {
       
        if(!$this->check_param_ok($dataProtocal,$error))
        {
            return false;
        }
        $uniqueNo = $dataProtocal['data']['unique_no'];
        $openId = $dataProtocal['data']['openid'];
        //$registerType = $dataProtocal['data']['register_type'];
        if(!ApiCommon::GetLoginInfo($uniqueNo,$LoginInfo,$error))
        {
            return false;
        }

        $registerType = 2;
        if(!ClientInfoUtil::GetBindInfo($LoginInfo,$openId,$registerType,$error))
        {
            return false;
        }

        $rstData['has_data'] = '0';
        $rstData['data_type'] = 'string';
        $rstData['data'] = [];

        return true;
    }
} 