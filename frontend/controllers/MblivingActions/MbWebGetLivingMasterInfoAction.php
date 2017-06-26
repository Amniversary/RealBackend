<?php
/**
 * WEB直播页面所需要主播，观众信息
 * User: hlq
 * Date: 2016/5/3
 * Time: 14:58
 */
namespace frontend\controllers\MblivingActions;
use frontend\business\LivingUtil;
use yii\base\Action;

class MbWebGetLivingMasterInfoAction extends Action
{
    public function run()
    {
        $error_msg = 'ok';
        $datas = \Yii::$app->request->post();
//        $rand_str = $datas['rand_str'];
//        $time = $datas['time'];
//        $post_sign = $datas['p_sign'];
//        $unique_no = $datas['unique_no'];
        $living_id = $datas['living_id'];
//        $params = [
//            'unique_no' => $unique_no,
//            'living_id' =>$living_id,
//            'time' => $time,
//            'rand_str' =>$rand_str,
//        ];
//        $sign = ClientUtil::GetClientSign($params);  //签名验证
//        if($post_sign !== $sign){
//            $arr_data = ['error_msg' => '签名不正确'];
//            echo  json_encode($arr_data);
//            exit;
//        }
        if(empty($living_id)){
            $arr_data = ['error_msg' => '直播ID不能为空'];
            echo  json_encode($arr_data);
            exit;
        }
        $cache_key = 'private_living_info_'.$living_id;
        $info = \Yii::$app->cache->get($cache_key);
        if($info !== false)
        {
            //私密直播
            $arr_data = ['error_msg' => '该直播不允许分享，请下载蜜播'];
            echo  json_encode($arr_data);
            exit;
        }
        $living_info = LivingUtil::GetLivingById($living_id);
        if(empty($living_info)){
            $arr_data = ['error_msg' => '直播不存在'];
            echo  json_encode($arr_data);
            exit;
        }

        $living_master_info = LivingUtil::GetLivingMasterInfo($living_id);
        //$person_pic_list = LivingUtil::GetLivingPersonInfo($living_master_info['room_id']);

        $arr_data = [
            'error_msg' => '0',
            'living_info_one' => $living_master_info,
            //'person_pic_list' => $person_pic_list
        ];
        echo  json_encode($arr_data);
        exit;
    }
}




