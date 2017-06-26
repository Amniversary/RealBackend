<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 15-12-13
 * Time: 下午9:36
 */

namespace frontend\zhiboapi\v1;

use frontend\zhiboapi\IApiExcute;
use frontend\business\ApiCommon;
use frontend\business\OtherPayUtil;
use yii\log\Logger;

/**
 * Class 获取第三方支付参数
 * @package frontend\meiyuanapi\v3
 */
class ZhiBoGetOtherPayParams implements IApiExcute
{

    /**
     * 检查参数合法性
     * @param string $error
     */
    private function check_param_ok($dataProtocal,&$error='')
    {
       $fields = ['pay_type','pay_target','params'];
       $fieldLabels = ['支付类型','支付目标','params参数'];
        $len =count($fields);
        for($i = 0; $i <$len; $i ++)
        {
            if (!isset($dataProtocal['data'][$fields[$i]])) {
                $error = $fieldLabels[$i] . '，不能为空';
                return false;
            }
        }
        return true;
    }

    public function excute_action($dataProtocal, &$rstData,&$error, $extendData= array())
    {
        $otherAppids = array('1171658345');
    	if (in_array($dataProtocal['app_id'], $otherAppids) && $dataProtocal['data']['pay_type'] == '4') {
            $dataProtocal['data']['pay_target'] = 'otherrecharge';
        }
        $error = '';
        \Yii::getLogger()->log('dada:'.var_export($dataProtocal,true),Logger::LEVEL_ERROR);
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

        $user_id  = $loginInfo['user_id'];
        $passParams = $dataProtocal['data'];
        unset($passParams['unique_no']);
        unset($passParams['register_type']);
        //unset($passParams['phone_no']);
        $passParams['user_id'] = $user_id;
        $passParams['device_type'] = $deviceType;
        $pay_type = $passParams['pay_type'];
        $pay_target = $passParams['pay_target'];
        unset($passParams['pay_type']);
        unset($passParams['pay_target']);
        if($pay_type == 3)
        {
            \Yii::getLogger()->log('支付类型参数:1  '.var_export($passParams,true).'  pay_type:'.$pay_type.'  pay_target:'.$pay_target,Logger::LEVEL_ERROR);
        }

        if(!OtherPayUtil::GetOtherPayParams($passParams,$pay_type,$pay_target,$out,$error))
        {
            return false;
        }
        $rstData['has_data'] = '1';
        $rstData['data_type']="json";
        $rstData['data']=$out;
        //根据经度、纬度获取地理信息
        return true;
    }
} 