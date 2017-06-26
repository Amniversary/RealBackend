<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 15-12-13
 * Time: 下午9:36
 */

namespace frontend\zhiboapi\v3;

use frontend\business\OtherPayUtil;
use frontend\zhiboapi\IApiExcute;
use frontend\business\ApiCommon;

/**
 * Class 取消支付
 * @package frontend\meiyuanapi\v3
 */
class ZhiBoCancelOtherPay implements IApiExcute
{

    /**
     * 检查参数合法性
     * @param string $error
     */
    private function check_param_ok($dataProtocal,&$error='')
    {
       $fields = ['pay_type','pay_target', 'bill_no'];
       $fieldLabels = ['支付类型','支付目标','账单号'];
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
        $error = '';
        if(!$this->check_param_ok($dataProtocal, $error))
        {
            return false;
        }
        $deviceNo = '';
        $uniqueNo= '';
        $registerType='';
        $deviceType='';
        if(!ApiCommon::GetBaseInfoFromProtocol($dataProtocal, $deviceNo, $uniqueNo,$registerType,$deviceType,$error))
        {
            return false;
        }

        $loginInfo = null;
        if(!ApiCommon::GetLoginInfo($uniqueNo,$loginInfo, $error))
        {
            return false;
        }
        $passParams = $dataProtocal['data'];
        unset($passParams['device_no']);
        unset($passParams['phone_no']);
        $pay_type = $passParams['pay_type'];
        $pay_target = $passParams['pay_target'];
        if(!OtherPayUtil::CancelRewardByOtherPay($passParams,$pay_type,$pay_target,$error))
        {
            return false;
        }
        //根据经度、纬度获取地理信息
        return true;
    }
} 