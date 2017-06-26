<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/23
 * Time: 18:55
 */

namespace frontend\zhiboapi\v1\waistcoat;

use frontend\business\ApiCommon;
use frontend\business\LivingUtil;
use common\components\SystemParamsUtil;


class PublicNewestLiving implements IExcute
{
    function action($dataProtocal, &$rstData,&$error, $extendData= array())
    {
        $error = '';
        $pageNo = intval($dataProtocal['data']['page_no']);
        $appid  =  CreateFilterCoat::GetFilterCoat( $dataProtocal['app_id'] );
        //\Yii::getLogger()->log('appid:'.var_export($appid,true),Logger::LEVEL_ERROR);
        if(empty($pageNo) || ($pageNo <= 0)){
            $pageNo = 1;
        }
        $page_size = intval($dataProtocal['data']['page_size']);
        if(empty($page_size) || ($page_size <= 0)){
            $page_size = 5;
        }
        $unique_no = $dataProtocal['data']['unique_no'];
        if(!ApiCommon::GetLoginInfo($unique_no, $sysLoginInfo, $error))
        {
            return false;
        }
        //获得用户的id
        $user_id = $sysLoginInfo['user_id'];


        $livingType = null;
        if ($sysLoginInfo['client_type'] != 2) {
            $livingType = SystemParamsUtil::getSystemParamWithOne('public_living');
            empty($livingType) && $livingType = [1, 2];
        }

        /*
        $hot_users_cache = \yii::$app->cache->get('hot_users');

//        \Yii::getLogger()->log('hot_users_cache:'.$hot_users_cache,Logger::LEVEL_ERROR);

        if(empty($hot_users_cache))
        {
//            \Yii::getLogger()->log('hot_users_cache:1111111111111',Logger::LEVEL_ERROR);
            //日人气直播前四位
            $hot_users = StatisticActiveUserUtil::WeekFiveLivingMasterByLivingType($livingType);

//            \Yii::getLogger()->log('hot_users_cache:'.var_export($hot_users,true),Logger::LEVEL_ERROR);

            \yii::$app->cache->set('hot_users',json_encode($hot_users),600);
        }else
        {
            $hot_users = json_decode($hot_users_cache);
        }



        if(empty($hot_users)){
            $hot_users = StatisticActiveUserUtil::TotalFiveLivingMasterByLivingType($livingType);
        }
         */
        if(!isset($hot_users))
        {
            $hot_users = [];
        }

        $living_list =  LivingUtil::GetNewestLivingListByAppIDForLivingType($appid,$livingType,$pageNo,$user_id,$page_size);

        if ($livingType !== null && !in_array(5, $livingType)) {
            $living_list = array_filter($living_list, function($row) {
                return ($row['living_type'] != 5);
            });
        }
	
        if(!isset($living_list))
        {
            $living_list = [];
        }
        $is_police = ($sysLoginInfo['client_type'] == '2' ? 1 : 0);
        foreach($living_list as &$oneLiving)
        {
            if($oneLiving['living_type'] == 5)
            {
                $guess_conf_array = LivingUtil::GetLivingConf(3);
            }
            else
            {
                $guess_conf_array = LivingUtil::GetLivingConf($oneLiving['living_type']);
            }
            $guess_living_conf_no = intval(array_count_values($guess_conf_array)[0]);
            if($oneLiving['guess_num'] == -1)
            {
                $oneLiving['over_guess_num'] = strval(empty($guess_living_conf_no)?'0':$guess_living_conf_no);  //免费的次数
                $count = count($guess_conf_array);
                $oneLiving['guess_num'] = strval(empty($count)?'0':$count);  //剩余的次数
                $oneLiving['flowers_num'] = strval(empty($guess_conf_array[0])?'0':$guess_conf_array[0]);
            }
            else
            {
                if(($oneLiving['living_type'] == 3) || ($oneLiving['living_type'] == 5))
                {
                    $oneLiving['flowers_num'] = strval($guess_conf_array[$oneLiving['guess_num']]);
                }
                elseif($oneLiving['living_type'] == 4)
                {
                    $oneLiving['flowers_num'] = strval(ceil($guess_conf_array[$oneLiving['guess_num']]*$oneLiving['tickets_num']));
                }
                $oneLiving['guess_num'] = strval(count($guess_conf_array)-$oneLiving['guess_num']) ;  //剩余竞猜的次数
            }
            $oneLiving['is_police'] = strval($is_police);
            $oneLiving['flowers_num'] = empty($oneLiving['flowers_num'])?'0':$oneLiving['flowers_num'];
        }
        $out_data = [
            'hot_users' => $hot_users,
            'living_list' => $living_list
        ];

        $rstData['has_data'] = '1';
	    $rstData['errno'] = 0;
	    $rstData['errmsg'] = '';
        $rstData['data_type'] = 'jsonarray';
        $rstData['data'] = $out_data;

        return true;
    }

}