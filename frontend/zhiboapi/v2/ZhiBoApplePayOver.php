<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 15-12-13
 * Time: 下午9:36
 */

namespace frontend\zhiboapi\v2;

use common\components\SystemParamsUtil;
use frontend\business\ChatFriendsUtil;
use frontend\business\OtherPayUtil;
use frontend\zhiboapi\IApiExcute;
use frontend\business\ApiCommon;
use yii\log\Logger;

/**
 * Class ZhiBoApplePayOver  获取苹果内购是否超额
 * @package frontend\zhiboapi\v2
 */
class ZhiBoApplePayOver implements IApiExcute
{
    /**
     * 检查参数合法性
     * @param string $error
     */
    private function check_param_ok($dataProtocal,&$error='')
    {
        $fields = ['unique_no','register_type','pay_money'];//'wish_type_id',
        $fieldLabels = ['唯一id','登录类型','本次支付金额'];//'愿望类别id',
        $len =count($fields);
        for($i = 0; $i <$len; $i ++)
        {
            if (!isset($dataProtocal['data'][$fields[$i]]) || empty($dataProtocal['data'][$fields[$i]]))
            {
                $error = $fieldLabels[$i] . '，不能为空';
                return false;
            }
        }
        if(doubleval($dataProtocal['data']['pay_money']) <= 0)
        {
            $error = '支付金额必须大于零';
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
        $payMoney = $passParams['pay_money'];
        $rst = OtherPayUtil::IsOverPay($user_id,$payMoney);
        //$out = json_encode($friendsList);
        $rstData['has_data'] = '1';
        $rstData['data_type']="json";
        $rstData['data']=$rst;
        return true;
    }
} 