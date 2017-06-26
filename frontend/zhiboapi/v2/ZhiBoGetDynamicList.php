<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/11
 * Time: 10:51
 */

namespace frontend\zhiboapi\v2;


use frontend\business\ApiCommon;
use frontend\business\DynamicUtil;
use frontend\zhiboapi\IApiExcute;
use yii\log\Logger;

/**
 * 获取个人动态列表协议接口
 * Class ZhiBoGetDynamicList
 * @package frontend\zhiboapi\v2
 */
class ZhiBoGetDynamicList implements IApiExcute
{
    function excute_action($dataProtocal, &$rstData,&$error, $extendData= array())
    {
        if(!$this->check_param_ok($dataProtocal, $error))
        {
            return false;
        }
        //\Yii::getLogger()->log('list:'.var_export($dataProtocal,true),Logger::LEVEL_ERROR);
        $unique_no = $dataProtocal['data']['unique_no'];
        if(!ApiCommon::GetLoginInfo($unique_no, $LoginInfo, $error))
        {
            return false;
        }

        $page_no = intval($dataProtocal['data']['page_no']);
        if(empty($page_no) || ($page_no <= 0))
        {
            $page_no = 1;
        }

        $page_size = intval($dataProtocal['data']['page_size']);
        if(empty($page_size) || ($page_size <= 0))
        {
            $page_size = 10;
        }
        if($page_size > 50)
        {
            $page_size = 50;
        }
        $user_id = $LoginInfo['user_id'];
        if(isset($dataProtocal['data']['user_id']) && !empty($dataProtocal['data']['user_id']))
        {
            $user_id = $dataProtocal['data']['user_id'];
        }
        $Dynamic_test = DynamicUtil::GetDynamicListInfo($user_id,$LoginInfo['user_id'],$page_no,$page_size);

        $Dynamic_list = [];
        foreach($Dynamic_test as $list)
        {
            $list['is_click'] = '0';
            $list['is_reward'] = '1';
            $click_info = \Yii::$app->cache->get('get_dynamic_like_'.$list['user_id'].'_'.$list['dynamic_id'].'_'.$LoginInfo['user_id']);
            if($LoginInfo['client_type'] != 2)
            {
                if(empty($list['record_id']))
                {
                    $list['is_reward'] = '0';
                }
            }
            if($click_info !== false)
            {
                $list['is_click'] = '1';
            }
            $time = date('Y-m',strtotime($list['create_time']));
            $Dynamic_list[$time][] = $list;
        }

        //\Yii::getLogger()->log('back::'.var_export($Dynamic_list,true),Logger::LEVEL_ERROR);
        $rstData['has_data'] = '1';
        $rstData['data_type'] = 'jsonarray';
        $rstData['data'] = [
            'list'=>(!empty($Dynamic_list) ? $Dynamic_list : ''),
        ];
        return true;
    }

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
} 