<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/23
 * Time: 20:23
 */

namespace frontend\zhiboapi\v1\waistcoat;

use common\components\SystemParamsUtil;
use frontend\business\ApiCommon;
use frontend\business\AttentionUtil;
use frontend\business\LivingUtil;


class PrivateAttentionLiving implements IExcute
{
    function action($dataProtocal, &$rstData,&$error, $extendData= array())
    {
        $error = '';
        $unique_no = $dataProtocal['data']['unique_no'];
        $appid  =  CreateFilterCoat::GetFilterCoat( $dataProtocal['app_id'] );
        if(!ApiCommon::GetLoginInfo($unique_no,$LoginInfo,$error))
        {
            return false;
        }
        $user_id = $LoginInfo['user_id'];

        $pageNo = $dataProtocal['data']['page_no'];
        if(empty($pageNo) || ($pageNo <= 0))
        {
            $pageNo = 1;
        }
        $page_size = $dataProtocal['data']['page_size'];
        if(empty($page_size) || ($page_size <= 0))
        {
            $page_size = 5;
        }

        $living_type = null;
        if ($LoginInfo['client_type'] != 2) {
            $living_type = SystemParamsUtil::getSystemParamWithOne('private_living');
            empty($living_type) && $living_type = [3, 4];
        }


        $result = AttentionUtil::GetAttentionLivingByAppIDForLivingType($appid,$living_type,$user_id,$pageNo,$page_size);

        if(empty($result)){
            $result = [];
        }
        $is_police = ($LoginInfo['client_type'] == '2' ? 1 : 0);
        foreach($result as &$oneLiving)
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

        $rstData['has_data'] = '1';
        $rstData['errno'] = 0;
        $rstData['errmsg'] = '';
        $rstData['data_type'] = 'jsonarray';
        $rstData['data'] = $result;

        return true;

    }
}