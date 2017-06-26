<?php
/**
 * Created by PhpStorm.
 * User: wangzi
 * Date: 16-10-12
 * Time: 上午9:36
 */

namespace frontend\zhiboapi\v3;

use common\components\tenxunlivingsdk\TimRestApi;
use frontend\business\ApiCommon;
use frontend\business\ClientUtil;
use frontend\business\GoldsAccount;
use frontend\business\GoldsAccountUtil;
use frontend\business\GoldsAccountLogUtil;
use frontend\business\JobUtil;
use frontend\zhiboapi\IApiExcute;
use yii\log\Logger;
use common\components\SystemParamsUtil;
use common\models\SystemParams;

/**
 * 
 * @package frontend\zhiboapi\v3
 */
class ZhiBoDonateGoldsForUserLogin implements IApiExcute
{
    private function check_param_ok($dataProtocal,&$error='')
    {
        $fields = ['unique_no','register_type'];
        $fieldLabels = ['唯一标识','登录类型'];
        $len =count($fields);
        for($i = 0; $i <$len; $i ++){
            if (!isset($dataProtocal['data'][$fields[$i]]) || empty($dataProtocal['data'][$fields[$i]])) {
                $error = $fieldLabels[$i] . '，不能为空';
                return false;
            }
        }

        return true;
    }

    public function excute_action($dataProtocal, &$rstData,&$error, $extendData= array()){
        if(!$this->check_param_ok($dataProtocal,$error)){
            return false;
        }

        $unique_no = $dataProtocal['data']['unique_no'];
        if(!ApiCommon::GetLoginInfo($unique_no,$LoginInfo,$error)){
            return false;
        }
        $user_id = $LoginInfo['user_id'];

        $isOpenParams = SystemParams::findOne(['code'=>'is_open_golds_for_login']);
        $isOpen = intval($isOpenParams->value1);
        if( $isOpen  ) {
            $keyPresent = 'get_present_golds_for_login_to_golds_value';
            $presentGoldsNum = \Yii::$app->cache->get($keyPresent);
            if (!$presentGoldsNum) {
                $sysParamModel = SystemParams::findOne(['code' => 'present_golds_for_login']);
                $presentGoldsNum = intval($sysParamModel->value1);
                \Yii::$app->cache->set($keyPresent, $presentGoldsNum);
            }

            if ($presentGoldsNum) {
                $key = 'present_golds_for_login_' . $user_id . '_' . date('Y-m-d');
                $is_present = \Yii::$app->cache->get($key);
                if ($is_present != "Yes") {
                    $model = GoldsAccountUtil::GetGoldsAccountModleByUserId($user_id);
                    if ($model) {
                        $goldsAccountLogModel = GoldsAccountLogUtil::GetGoldsAccountLogModelByOneDayOneTime($model->gold_account_id, $user_id);
                        if (!isset($goldsAccountLogModel) && empty($goldsAccountLogModel)) {
                            $gold_account_id = $model->gold_account_id;
                            $device_type = $dataProtocal['device_type'];
                            $operateType = 5;
                            $operateValue = $presentGoldsNum;
                            if (!GoldsAccountUtil::UpdateGoldsAccountToAdd($gold_account_id, $user_id, $device_type, $operateType, $operateValue, $error)) {
                                \Yii::getLogger()->log('调用赠送金币接口时，用户: ' . $user_id . "在" . date('Y-m-d') . "登陆，回错误原因，无法赠送金币，详情记录在日记", Logger::LEVEL_ERROR);
                                return FALSE;
                            } else {
                                $rstData['data'] = ['operate_value' => $presentGoldsNum];
                            }
                        }
                    } else {
                        \Yii::getLogger()->log('调用赠送金币接口时，用户: ' . $user_id . '没有开设金户帐户', Logger::LEVEL_ERROR);
                        return FALSE;
                    }
                } else {

                    $rstData['data'] = ['operate_value' => 0];
                }
            } else {
                \Yii::getLogger()->log('调用赠送金币接口时，无法读取系统设置的赠送金币参数', Logger::LEVEL_ERROR);
                return FALSE;
            }

            \Yii::$app->cache->set($key, "Yes", 60 * 60 * 10);
        }
        else
        {
            $rstData['data'] = ['operate_value' => 0];
        }
        $rstData['errno']    = 0;
        $rstData['has_data'] = '1';
        $rstData['data_type'] = 'json';
        return TRUE;
    }

} 