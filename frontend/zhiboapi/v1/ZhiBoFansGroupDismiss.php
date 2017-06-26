<?php
/**
 * Created by PhpStorm.
 * User: Zff
 * Date: 2016/9/10
 * Time: 15:00
 */

namespace frontend\zhiboapi\v1;

use common\models\FansGroup;
use common\models\FansGroupMember;
use frontend\business\ApiCommon;
use frontend\business\FansGroupUtil;
use frontend\business\JobUtil;
use frontend\zhiboapi\IApiExcute;
use yii\log\Logger;
use frontend\business\SaveRecordByransactions\SaveByTransaction\FansGroupDismissSaveByTrans;

class ZhiBoFansGroupDismiss implements IApiExcute
{
    private function check_param_ok($dataProtocal,&$error='')
    {
        $fields = ['group_id'];
        $fieldLabels = ['粉丝群ID'];
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

    public function excute_action($dataProtocal, &$rstData, &$error, $extendData= array())
    {
        $error = '';
        if(!$this->check_param_ok($dataProtocal,$error))
        {
            return false;
        }

        $unique_no = $dataProtocal['data']['unique_no'];
        if(!ApiCommon::GetLoginInfo($unique_no, $sysLoginInfo, $error))
        {
            return false;
        }
        //获得用户的id
        $user_id = $sysLoginInfo['user_id'];
        $group_id = $dataProtocal['data']['group_id'];


        $data = [
            'user_id'=>$user_id,
            'group_id'=>$group_id
        ];

        //添加成员
        $transAction = new FansGroupDismissSaveByTrans($data);
        $info = null;
        if(!$transAction->SaveRecordForTransaction($error, $info))
        {
            return false;
        }
        $tx_group_id = $info['tx_group_id'];
        $groupManager = \Yii::$app->im->Group();
        if (!$groupManager->dismiss($user_id, $tx_group_id)) {
            $error = $groupManager->getErrorMessage();
            return false;
        }

        $rstData['has_data']='1';
        $rstData['data_type']='string';
        $rstData['data']  = '解散成功';
        return true;
    }
} 