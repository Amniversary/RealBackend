<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/11
 * Time: 17:05
 */

namespace frontend\zhiboapi\v1;


use frontend\business\ApiCommon;
use frontend\business\DynamicUtil;
use frontend\zhiboapi\IApiExcute;
use yii\log\Logger;

class ZhiBoGetNewDynamicList implements IApiExcute
{
    function excute_action($dataProtocal, &$rstData,&$error, $extendData= array())
    {
        if(!$this->check_param_ok($dataProtocal, $error))
        {
            return false;
        }

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
        if(empty($page_no) || ($page_size <= 0))
        {
            $page_size = 10;
        }
        if($page_size > 50)
        {
            $page_size = 50;
        }

        $new_dynamic = DynamicUtil::GetNewDynamicListInfo($LoginInfo, $page_no, $page_size);
        foreach($new_dynamic as $list)
        {
            $s = array_search($list,$new_dynamic);
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
            $new_dynamic[$s] = $list;
        }

        //\Yii::getLogger()->log('dada::'.var_export($new_dynamic,true),Logger::LEVEL_ERROR);
        $rstData['has_data'] = '1';
        $rstData['data_type'] = 'jsonarray';
        $rstData['data'] = $new_dynamic;
        return true;
    }

    private function check_param_ok($dataProtocal,&$error='')
    {

        $fields = ['unique_no','page_no','page_size'];
        $fieldLabels = ['唯一号','页数','每页记录数'];
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