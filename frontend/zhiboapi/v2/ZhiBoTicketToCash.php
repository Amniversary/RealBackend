<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 15-12-13
 * Time: 下午9:36
 */

namespace frontend\zhiboapi\v2;


use frontend\business\TicketToCashUtil;
use frontend\zhiboapi\IApiExcute;
use frontend\business\ApiCommon;
use yii\log\Logger;

/**
 * Class 票提现
 * @package frontend\zhiboapi\v2
 */
class ZhiBoTicketToCash implements IApiExcute
{

    /**
     * 检查参数合法性
     * @param string $error
     */
    private function check_param_ok($dataProtocal,&$error='')
    {
       $fields = ['unique_no','register_type','money_value','cash_type','op_unique_no'];//'wish_type_id',
       $fieldLabels = ['唯一id','登录类型','金额','提现类型','唯一操作码'];//'愿望类别id',
        $len =count($fields);
        for($i = 0; $i <$len; $i ++)
        {
            if (!isset($dataProtocal['data'][$fields[$i]]) || empty($dataProtocal['data'][$fields[$i]])) {
                $error = $fieldLabels[$i] . '，不能为空';
                return false;
            }
        }
        if($dataProtocal['data']['cash_type'] != '2')
        {
            $error = '提现类型暂时只能是2';
            return false;
        }
        if(doubleval($dataProtocal['data']['money_value']) <= 0)
        {
            $error = '金额必须大于0';
            \Yii::getLogger()->log('into:'.var_export($dataProtocal['data'],true),Logger::LEVEL_ERROR);
            return false;
        }
        return true;
    }

    public function excute_action($dataProtocal, &$rstData,&$error, $extendData= array())
    {
        //\Yii::getLogger()->log(var_export($dataProtocal, true),Logger::LEVEL_ERROR);
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
        $user_id  = $loginInfo['user_id'];
        $passParams = $dataProtocal['data'];
        unset($passParams['unique_no']);
        unset($passParams['register_type']);
        $passParams['device_type'] = $deviceType;
        if(!TicketToCashUtil::TicketToCash($passParams,$user_id,$error))
        {
            return false;
        }
        //根据经度、纬度获取地理信息
        return true;
    }
} 