<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/10
 * Time: 14:38
 */

namespace frontend\zhiboapi\v1;


use frontend\business\ApiCommon;
use frontend\business\DynamicUtil;
use frontend\business\SaveRecordByransactions\SaveByTransaction\CancelDynamicByTrans;
use frontend\zhiboapi\IApiExcute;

/**
 * 删除动态协议接口
 * Class ZhiBoCancelDynamic
 * @package frontend\zhiboapi\v3
 */
class ZhiBoCancelDynamic implements IApiExcute
{
    function excute_action($dataProtocal, &$rstData,&$error, $extendData= array())
    {
        if(!$this->check_param_ok($dataProtocal,$error))
        {
            return false;
        }

        $unique_no = $dataProtocal['data']['unique_no'];
        if(!ApiCommon::GetLoginInfo($unique_no,$LoginInfo,$error))
        {
            return false;
        }
        $dynamic_list = $dataProtocal['data']['dynamic_id'];
//        $dynamic = DynamicUtil::GetDynamicById($dynamic_id);
//        if(!isset($dynamic))
//        {
//            $error = '动态记录不存在';
//            return false;
//        }
        $params = [
            'user_id'=>$LoginInfo['user_id'],
        ];
        $transAction = new CancelDynamicByTrans($dynamic_list, $params);
        if(!$transAction->SaveRecordForTransaction($error, $out))
        {
            return false;
        }

        //\Yii::$app->cache->delete('get_dynamic_like_'.$dynamic->user_id.'_'.$dynamic_id.'_'.$LoginInfo['user_id']);

        $rstData['has_data'] = '0';
        $rstData['data_type'] = 'string';
        $rstData['data'] = '';
        return true;
    }

    private function check_param_ok($dataProtocal,&$error='')
    {

        $fields = ['unique_no','dynamic_id'];
        $fieldLabels = ['唯一号','动态id'];
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
} 