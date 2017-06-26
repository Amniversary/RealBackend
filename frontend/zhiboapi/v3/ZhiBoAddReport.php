<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/11
 * Time: 15:39
 */

namespace frontend\zhiboapi\v3;


use frontend\business\ApiCommon;
use frontend\business\ClientUtil;
use frontend\business\LivingUtil;
use frontend\business\ReportUtil;
use frontend\zhiboapi\IApiExcute;
use yii\log\Logger;

/**
 * 举报协议接口
 * Class ZhiBoAddReport
 * @package frontend\zhiboapi\v3
 */
class ZhiBoAddReport implements IApiExcute
{
    private function check_param_ok($dataProtocal,&$error='')
    {
        $fields = ['scene','report_type','report_id','report_content'];
        $fieldLabels = ['场景类型','举报类型','举报id','举报内容'];//'愿望类别id',
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

    function excute_action($dataProtocal, &$rstData,&$error, $extendData= array())
    {
        if(!$this->check_param_ok($dataProtocal,$error))
        {
            return false;
        }

        $uniqueNo = $dataProtocal['data']['unique_no'];
        if(!ApiCommon::GetLoginInfo($uniqueNo,$LoginInfo,$error))
        {
            return false;
        }

        $params = $dataProtocal['data'];

        $scene = (isset($params['scene']) ? $params['scene']:'1');
        switch(intval($scene))
        {
            case 1:
                $client_id = $params['report_id'];
                $livingInfo = LivingUtil::GetLivingUserInfo($client_id);
                if(!isset($livingInfo))
                {
                    $error = '直播间不存在';
                    \Yii::getLogger()->log('client_id=:'.$client_id,Logger::LEVEL_ERROR);
                    return false;
                }
                if($LoginInfo['user_id'] == $livingInfo->living_master_id)
                {
                    $error = '不能举报自己!';
                    return false;
                }
                break;
            case 2:
                $friend_id = $params['report_id'];
                $ClientInfo = ClientUtil::GetClientById($friend_id);
                if(!isset($ClientInfo))
                {
                    $error = '用户不存在';
                    return false;
                }
                if($LoginInfo['user_id'] == $ClientInfo->client_id)
                {
                    $error = '不能举报自己!';
                    return false;
                }
                break;
        }
        //\Yii::getLogger()->log('举报信息:'.var_export($params,true),Logger::LEVEL_ERROR);
        $model = ReportUtil::GetReportNewModel($LoginInfo,$params,$error);
        //\Yii::getLogger()->log('Modd :'.var_export($model,true),Logger::LEVEL_ERROR);
        if(!ReportUtil::SaveReport($model,$error))
        {
            return false;
        }

        $rstData['has_data'] = '1';
        $rstData['data_type'] = 'string';
        $rstData['data'] = '感谢您的建议!';

        return true;
    }
} 