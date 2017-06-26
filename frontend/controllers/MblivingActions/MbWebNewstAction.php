<?php
/**
 * 热门直播
 * User: hlq
 * Date: 2016/5/3
 * Time: 14:58
 */
namespace frontend\controllers\MblivingActions;
use frontend\business\ClientUtil;
use frontend\business\LivingHotUtil;
use yii\base\Action;

class MbWebNewstAction extends Action
{
    public function run()
    {
        $error_msg = 'ok';

        $hot_living_info = LivingHotUtil::GetHotLivingList(1,100);
        if(empty($hot_living_info)){
            $hot_living_info = [];
        }else{
            foreach($hot_living_info as &$v)
            {

                $length = 40;
                $rand_str = null;
                $strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
                $max = strlen($strPol)-1;

                for($i=0;$i<$length;$i++){
                    $rand_str.=$strPol[rand(0,$max)];
                }

                $time = time();
                $params= [
                    'unique_no' => $v['unique_no'],
                    'living_id' => $v['living_id'],
                    'rand_str' => $rand_str,
                    'time' => $time
                ];

                $sign = ClientUtil::GetClientSign($params);

                $v['url'] = 'https://'.$_SERVER['HTTP_HOST']."/mibo/livingshare.html?unique_no={$params['unique_no']}&rand_str={$rand_str}&time={$time}&p_sign={$sign}&living_id={$params['living_id']}";
            }
        }

        $arr_data = [
            'error_msg' => $error_msg,
            'living_info_one' => [],
            'living_info_list' => $hot_living_info
        ];
        echo  json_encode($arr_data);
        exit;
    }
}




