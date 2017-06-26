<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/23
 * Time: 14:37
 */

namespace frontend\zhiboapi\v1\waistcoat;

use common\components\PhpLock;
use frontend\business\ApiCommon;
use frontend\business\LivingHotUtil;
use frontend\business\LivingUtil;
use common\components\SystemParamsUtil;

class PrivateHotLiving implements IExcute
{
    function action($dataProtocal, &$rstData,&$error, $extendData= array())
    {
        $error = '';
        $uniqueNo = $dataProtocal['data']['unique_no'];
        $pageNo = intval($dataProtocal['data']['page_no']);
        $appid  =  CreateFilterCoat::GetFilterCoat( $dataProtocal['app_id'] );
        if(!ApiCommon::GetLoginInfo($uniqueNo,$LoginInfo,$error))
        {
            return false;
        }
        if(empty($pageNo) || ($pageNo <= 0))
        {
            $pageNo = 1;
        }
        //如果传的是第一页，从缓存取200条数据
        if( is_array( $appid ) )
        {
            $appidmd5 = md5( implode(",",$appid) );
        }
        $rst = \Yii::$app->cache->get("mb_api_hot_living_list_".$appidmd5."_".$pageNo);
        if($rst === false)
        {
            $phpLock = new PhpLock('mb_api_hot_living_list_'.$appidmd5."_".$uniqueNo);
            $phpLock->lock();
            $rst = \Yii::$app->cache->get('mb_api_hot_living_list_'.$appidmd5."_".$pageNo);
            if($rst === false)
            {
                $livingType = null;
                if ($LoginInfo['client_type'] != 2) {
                    $livingType = SystemParamsUtil::getSystemParamWithOne('private_living');
                    empty($livingType) && $livingType = [3, 4];
                }

                $cache_info =  LivingHotUtil::SetCacheHotLivingListOtherByAppID($error,$outInfo,$appid,$livingType,$LoginInfo['user_id'],$pageNo,200);
                if(!$cache_info)
                {
                    $phpLock->unlock();
                    return false;
                }
                $rst = $outInfo;
            }else{
                $rst = json_decode($rst,true);
            }

            $phpLock->unlock();
        }
        else
        {
            $rst = json_decode($rst,true);
        }

        if(empty($rst))
        {
            $rst = [];
        }
        else
        {
            $is_police = ($LoginInfo['client_type'] == '2' ? 1 : 0);
            foreach($rst as &$oneLiving)
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
        }

        $rstData['has_data'] = '1';
        $rstData['errno'] = 0;
	    $rstData['errmsg'] = '';
        $rstData['data_type'] = 'jsonarray';
        $rstData['data'] = $rst;
        return true;
    }
}