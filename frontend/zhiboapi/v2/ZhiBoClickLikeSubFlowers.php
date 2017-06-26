<?php
/**
 * Created by PhpStorm.
 * User: hlq
 * Date: 16-04-26
 * Time: 上午11:30
 */

namespace frontend\zhiboapi\v2;

use common\components\UsualFunForStringHelper;
use frontend\business\ApiCommon;
use frontend\business\BalanceUtil;
use frontend\business\RewardUtil;
use frontend\business\SaveRecordByransactions\SaveByTransaction\CreateUserBalanceLogByTrans;
use frontend\business\SaveRecordByransactions\SaveByTransaction\ModifyBalanceBySubRealBean;
use frontend\zhiboapi\IApiExcute;
use yii\log\Logger;


/**
 * Class 点赞扣鲜花
 * @package frontend\zhiboapi\v2
 */
class ZhiBoClickLikeSubFlowers implements IApiExcute
{

    /**
     * 点赞
     * @param string $error
     */
    public function excute_action($dataProtocal, &$rstData,&$error, $extendData= array())
    {
        if(!ApiCommon::GetLoginInfo($dataProtocal['data']['unique_no'],$LoginInfo,$error))
        {
            return false;
        }
        $flower_num = $dataProtocal['data']['flower_num'];
        if(!isset($flower_num) || empty($flower_num) || $flower_num <= 0)
        {
            $error = '鲜花数不能为空';
            return false;
        }
//获取用户账户信息
        $banlances_object = BalanceUtil::GetUserBalanceByUserId($LoginInfo['user_id']);
        if(empty($banlances_object))
        {
            \Yii::getLogger()->log('$LoginInfo===:'.var_export($LoginInfo,true),Logger::LEVEL_ERROR);
            $error = '用户账户信息不存在';
            return false;
        }
        if($banlances_object->bean_balance < intval($flower_num))
        {
            $error = '鲜花余额不足';
            return false;
        }
        $money_params = [
            'bean_num' => intval($flower_num),
            'user_id' => $LoginInfo['user_id'],
        ];
        $extend_params = [
            'unique_id' => UsualFunForStringHelper::CreateGUID(),
            'op_value' => intval($flower_num),
            'relate_id' => '',
            'money_type' => 1,
        ];
        $extend_params['device_type'] = $dataProtocal['device_type'];
        $transActions[] = new ModifyBalanceBySubRealBean($banlances_object,$money_params);
        $extend_params['field'] = 'bean_balance';
        $extend_params['operate_type'] = 26;
        $transActions[] = new CreateUserBalanceLogByTrans($banlances_object,$extend_params);
        if (!RewardUtil::RewardSaveByTransaction($transActions, $outInfo, $error))
        {
            return false;
        }
        $rstData['has_data'] = '0';
        $rstData['data_type'] = 'string';
        $rstData['data'] = [];

        return true;
    }
}


