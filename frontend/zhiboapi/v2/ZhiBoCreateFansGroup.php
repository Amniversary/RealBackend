<?php
/**
 * Created by PhpStorm.
 * User: Zff
 * Date: 2016/9/10
 * Time: 15:00
 */

namespace frontend\zhiboapi\v2;

use common\models\FansGroup;
use frontend\business\ApiCommon;
use frontend\business\FansGroupUtil;
use frontend\zhiboapi\IApiExcute;
use yii\log\Logger;

class ZhiBoCreateFansGroup implements IApiExcute
{
    private function check_param_ok($dataProtocal,&$error='')
    {
        $fields = ['unique_no'];
        $fieldLabels = ['唯一号'];
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
        //根据unique_no获取user_id
        $unique_no = $dataProtocal['data']['unique_no'];
        if(!ApiCommon::GetLoginInfo($unique_no, $sysLoginInfo, $error))
        {
            return false;
        }
        $user_id = $sysLoginInfo['user_id'];

        if(!FansGroupUtil::CreateFansGroup($user_id, $group_info, $error))
        {
            return false;
        }
        $data = [
            'group_id' => $group_info['group_id'],
            'tx_group_id' => $group_info['tx_group_id'],
        ];

        $query = FansGroup::findOne(['group_id'=>$data['group_id']]);
        $data['pic'] = $query->pic;
        $data['group_name'] = $query->group_name;
        //var_dump($data);
        $rstData['has_data']='1';
        $rstData['data_type']='json';
        $rstData['data']  = $data;
        return true;
    }
} 