<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/21
 * Time: 16:27
 */

namespace frontend\zhiboapi\v3;


use frontend\business\ApiCommon;
use frontend\business\DynamicUtil;
use frontend\zhiboapi\IApiExcute;
use yii\log\Logger;

/**
 * 获取动态信息协议接口 hbh
 * Class ZhiBoGetDynamicInfo
 * @package frontend\zhiboapi\v3
 */
class ZhiBoGetDynamicInfo implements IApiExcute
{
    function excute_action($dataProtocal, &$rstData,&$error, $extendData= array())
    {
        if(!$this->check_param_ok($dataProtocal, $error))
        {
            return false;
        }
        //\Yii::getLogger()->log('dafoooo:'.var_export($dataProtocal,true),Logger::LEVEL_ERROR);
        $unique_no = $dataProtocal['data']['unique_no'];

        if(!ApiCommon::GetLoginInfo($unique_no, $LoginInfo, $error))
        {
            return false;
        }

        $dynamic_id = $dataProtocal['data']['dynamic_id'];
        $Dynamic = DynamicUtil::GetDynamicById($dynamic_id);
        if(!isset($Dynamic))
        {
            $error = '动态记录不存在';
            return false;
        }
        $dynamic_info = DynamicUtil::GetUserByDynamicInfo($dynamic_id);
        $dynamic_like = \Yii::$app->cache->get('get_dynamic_like_'.$Dynamic->user_id.'_'.$dynamic_id.'_'.$LoginInfo['user_id']);

        if($dynamic_like !== false) {
            $dynamic_info['is_click'] = '1';
        }

        $rstData['has_data'] = '1';
        $rstData['data_type'] = 'json';
        $rstData['data'] = $dynamic_info;

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