<?php
/**
 * 直播分享
 * User: hlq
 * Date: 2016/5/3
 * Time: 14:58
 */
namespace frontend\controllers\MblivingActions;
use frontend\business\ApiCommon;
use frontend\business\AttentionUtil;
use frontend\business\ClientUtil;
use frontend\business\LivingHotUtil;
use frontend\business\LivingUtil;
use yii\base\Action;
use yii\log\Logger;

class MbWebShareAction extends Action
{
    public function run()
    {
        $error_msg = 'ok';
        $datas = \Yii::$app->request->post();
        $rand_str = $datas['rand_str'];
        $time = $datas['time'];
        $post_sign = $datas['p_sign'];
        $unique_no = $datas['unique_no'];
        $living_id = $datas['living_id'];
        $params = [
            'unique_no' => $unique_no,
            'living_id' =>$living_id,
            'time' => $time,
            'rand_str' =>$rand_str,
        ];
        $sign = ClientUtil::GetClientSign($params);  //签名验证
        if($post_sign !== $sign){
            //\Yii::getLogger()->log('===['.$post_sign.']===['.$sign.']',Logger::LEVEL_ERROR);
            $arr_data = ['error_msg' => '签名不正确'];
            echo  json_encode($arr_data);
            exit;
        }

        //\Yii::getLogger()->log('sign==='.$sign.'share_post==='.var_export($datas),Logger::LEVEL_ERROR);
//        $cache_key = 'private_living_info_'.$living_id;
//        $info = \Yii::$app->cache->get($cache_key);
//        if($info !== false)
//        {
//            //私密直播
//            $arr_data = ['error_msg' => '该直播不允许分享，请下载蜜播'];
//            echo  json_encode($arr_data);
//            exit;
//        }

        $living_master_info = LivingUtil::GetUserInfo($living_id);

        $is_attention = 0; //未关注
        if(ApiCommon::GetLoginInfo($unique_no,$LoginInfo,$error)){ //是否登录
            $client_info = ClientUtil::GetClientByUniqueNo($unique_no);
            $record_info = AttentionUtil::GetFriendOne($living_master_info['living_master_id'],$client_info->client_id); //是否关注
            if($record_info->record_id){
                $is_attention = 1; //已关注
            }else{
                $is_attention = 0; //未关注
            }
        }
        if(!empty($living_master_info)){
            $living_master_info['attention'] = $is_attention;
        }else{
            $living_master_info = [];
        }

//        $hot_living_info = LivingHotUtil::GetLivingList();
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
            'living_info_one' => $living_master_info,
            'living_info_list' => $hot_living_info
        ];
        echo  json_encode($arr_data);
        exit;
    }
}




