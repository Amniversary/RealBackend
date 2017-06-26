<?php
/**
 * 活动报名用户信息写入
 * User: hlq
 * Date: 2016/5/3
 * Time: 14:58
 */
namespace frontend\controllers\MblivingActions;
use common\models\ActivityInfo;
use common\models\EnrollInfo;
use frontend\business\ActivityUtil;
use frontend\business\ClientUtil;
use yii\base\Action;
use yii\log\Logger;

class MbWebAddEnrollAction extends Action
{
    public function run()
    {
        $datas = \Yii::$app->request->post();
        $rand_str = $datas['rand_str'];
        $time = $datas['time'];
        $post_sign = $datas['p_sign'];
        $client_no = $datas['client_no'];
        $sex = $datas['sex'];
        $phone_number = $datas['phone_number'];
        $name = $datas['name'];
        $activity_id = $datas['activity_id'];
        $sign_params = [
            'activity_id' => $activity_id,
            'time' => $time,
            'rand_str' =>$rand_str,
        ];
        $sign = ActivityUtil::GetActivitySign($sign_params);  //签名验证
        if($post_sign !== $sign){
            \Yii::getLogger()->log('活动报名面签名不正确===['.$post_sign.']===['.$sign.']',Logger::LEVEL_ERROR);
            \Yii::getLogger()->log('活动报名面签名不正确$params==='.var_export($sign_params,true),Logger::LEVEL_ERROR);
            $arr_data = ['error_msg' => '签名不正确'];
            echo  json_encode($arr_data);
            exit;
        }

        $params = [
            'client_no' => $client_no,
            'sex' =>$sex,
            'phone_number' =>$phone_number,
            'name' =>$name,
        ];
        $fields = ['client_no','sex','phone_number','name'];
        $fields_name = ['蜜播ID','姓别','手机号码','姓名'];
        $str_len = count($params);
        for($i=0;$i<$str_len;$i++)
        {
            if(!isset($params[$fields[$i]]) || empty($params[$fields[$i]]))
            {
                $arr_data = ['error_msg' => $fields_name[$i].'不能为空'];
                echo  json_encode($arr_data);
                exit;
            }
        }

        $activity_info = ActivityInfo::GetActivityInfoById($activity_id);  //活动信息
        if(!isset($activity_info) || empty($activity_info->activity_id))
        {
            $arr_data = ['error_msg' => '活动不存在'];
            echo  json_encode($arr_data);
            exit;
        }

        $time  = date('Y-m-d H:i:s');
        if(($activity_info->status == 0) || ($activity_info->end_time < $time))
        {
            $arr_data = ['error_msg' => '活动已结束'];
            echo  json_encode($arr_data);
            exit;
        }

        if(($activity_info->status == 1) || ($activity_info->start_time > $time))
        {
            $arr_data = ['error_msg' => '活动未开始'];
            echo  json_encode($arr_data);
            exit;
        }

        $client_info = ClientUtil::GetClientNo($client_no);
        if(!isset($client_info) || empty($client_info->client_id))
        {
            $arr_data = ['error_msg' => '蜜播用户不存在'];
            echo  json_encode($arr_data);
            exit;
        }

        $enorll_info = EnrollInfo::GetEnrollInfo($client_no,$activity_id);
        if(!empty($enorll_info->enroll_id))
        {
            \Yii::getLogger()->log('用户已经报过名了   $client_info===:'.var_export($enorll_info,true),Logger::LEVEL_ERROR);
            $arr_data = ['error_msg' => '用户已经报过名了'];
            echo  json_encode($arr_data);
            exit;
        }

        $params['user_id'] = $client_info->client_id;
        $params['activity_id'] = $activity_info->activity_id;
        $params['create_time'] = date('Y-m-d H:i:s');

        if(!EnrollInfo::InsertEnrollData($params,$error))
        {
            $arr_data = ['error_msg' => $error];
            echo  json_encode($arr_data);
            exit;
        }
        $arr_data = ['error_msg' => 'ok'];
        echo  json_encode($arr_data);
        exit;
    }
}




