<?php
/**
 * Created by PhpStorm.
 * User: Q2239366700
 * Date: 16-10-14
 * Time: 上午9:36
 */

namespace frontend\zhiboapi\v3;

use frontend\zhiboapi\IApiExcute;
use frontend\business\ApiCommon;
use frontend\business\GoldsPayUtil;
use yii\log\Logger;
/**
 * Class 获取第三方支付参数
 * @package frontend\meiyuanapi\v3
 */
class ZhiBoGetGoldsPayParams implements IApiExcute
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
            $dataProtocal['data']['pay_target'] = 'otherprestore';
        }
        //\Yii::getLogger()->log('支付方式参数======>'.var_export($dataProtocal,true),Logger::LEVEL_ERROR);
        $error = '';
        if(!$this->check_param_ok($dataProtocal, $error))
        {
            return false;
        }
//        $error = '金币充值功能开发中，敬请期待！';
//        return false;
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
        //\Yii::getLogger()->log('wxpaytttttttttttttttt:55555555',Logger::LEVEL_ERROR);
        if(!GoldsPayUtil::GetGoldPayParams($passParams,$pay_type,$pay_target,$out,$error))
        {
            return false;
        }   
        $rstData['has_data'] = '1';
        $rstData['data_type']="json";
        $rstData['data']=$out;
        //\Yii::getLogger()->log('wxpaytttttttttttttttt:'.var_export($rstData,true),Logger::LEVEL_ERROR);
        //根据经度、纬度获取地理信息
        return true;
    }
} 