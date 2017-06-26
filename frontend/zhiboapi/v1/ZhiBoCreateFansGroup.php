<?php
/**
 * Created by PhpStorm.
 * User: Zff
 * Date: 2016/9/10
 * Time: 15:00
 */

namespace frontend\zhiboapi\v1;

use common\models\FansGroup;
use frontend\business\ApiCommon;
use frontend\business\FansGroupUtil;
use frontend\zhiboapi\IApiExcute;
use yii\log\Logger;
use frontend\business\RongCloud\GroupUtil;

/**
 * 创建粉丝群
 * Class ZhiBoCreateFansGroup
 * @package frontend\zhiboapi\v1
 */
class ZhiBoCreateFansGroup implements IApiExcute
{
    private function check_param_ok($dataProtocal,&$error='')
    {
        $fields = ['user_id'];
        $fieldLabels = ['用户ID'];
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
        $unique_no = $dataProtocal['data']['unique_no'];
        if(!ApiCommon::GetLoginInfo($unique_no, $sysLoginInfo, $error))
        {
            return false;
        }
        $user_id = $sysLoginInfo['user_id'];

        $groupManager = new GroupUtil();

        try {
            $groupModel = $groupManager->create($user_id);
        } catch (\Exception $e) {
            $error = $e->getMessage();
            return false;
        }

        $result = [
            'group_id' => $groupModel->group_id,
            'tx_group_id' => $groupModel->tx_group_id,
            'pic' => $groupModel->pic,
            'group_name' => $groupModel->group_name
        ];
        //var_dump($data);
        $rstData['has_data']='1';
        $rstData['data_type']='json';
        $rstData['data']  = $result;
        return true;
    }
} 