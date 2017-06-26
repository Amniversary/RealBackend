<?php
/**
 * Created by PhpStorm.
 * User: hlq
 * Date: 16-06-28
 * Time: 16:36
 */

namespace frontend\zhiboapi\v1;

use common\components\SystemParamsUtil;
use common\models\OffUserLiving;
use Faker\Provider\Uuid;
use frontend\business\ApproveUtil;
use frontend\business\ClientUtil;
use frontend\business\RewardUtil;
use frontend\business\SaveRecordByransactions\SaveByTransaction\ModifyUserFaceByTrans;
use frontend\zhiboapi\IApiExcute;


/**
 * 人脸识别统计
 * Class ZhiBoFaceTotalStatistic
 * @package frontend\zhiboapi\v2
 */
class ZhiBoFaceTotalStatistic implements IApiExcute
{
    public function excute_action($dataProtocal, &$rstData,&$error, $extendData= array())
    {
        if(empty($dataProtocal['data']['unique_no']))
        {
            $error = '唯一ID不能为空';
            return false;
        }
        if(empty($dataProtocal['device_no']))
        {
            $error = '设备号不能为空';
            return false;
        }
        $user_info = ClientUtil::GetUserByUniqueId($dataProtocal['data']['unique_no']);
        if(!$user_info)
        {
            $error = '用户不存在';
            return false;
        }
        if($user_info['status'] == 0)
        {
            $error = '您的账号已经被禁用，请联系客服！';
            return false;
        }
        if($user_info['is_centification'] == 3)
        {
            $error = '您已经认证过了';
            $rstData['has_data'] = '1';
            $rstData['data_type'] = 'jsonarray';
            $rstData['data']['order_no'] = '';
            $rstData['data']['status'] = 1;
            $rstData['data']['url'] = '';
            return true;
        }
//        $white_user = OffUserLiving::findone(['client_no' => $user_info['client_no']]);
//
//        if(!isset($white_user))
//        {
//            $error = '开播请添加客服QQ';
//            return false;
//        }
        $week = date('Y').'-'.date('W');
        $week_key = 'user_face_'.$user_info['client_id'].'_'.$week;
        $user_face_id = \Yii::$app->cache->get($week_key);
        $user_face_id = json_decode($user_face_id,true);
        if(empty($user_face_id))
        {
            $user_face_id = ApproveUtil::GetUserFaceInfo($user_info['client_id'],$week,1);
        }
        $week_num = SystemParamsUtil::GetSystemParam('youdun_week_num',true,'value1'); //每个用户每周最多认证的次数
        if($user_face_id['request_num'] >= $week_num)
        {
            $error = '您的请求次数太多了，请稍候尝试';
            return false;
        }



        $month = date('Y').'-'.date('m');
        $month_key = 'user_face_'.$dataProtocal['device_no'].'_'.$month;   //缓存设备号
        $user_face_device = \Yii::$app->cache->get($month_key);
        $user_face_device = json_decode($user_face_device,true);
        if(empty($user_face_device))
        {
            $user_face_device = ApproveUtil::GetUserFaceInfo($dataProtocal['device_no'],$month,2);
        }
        $month_num = SystemParamsUtil::GetSystemParam('youdun_month_num',true,'value1'); //每台设备每月最多认证的次数
        if($user_face_device['request_num'] >= $month_num)
        {
            $error = '您的请求次数太多了，请稍候尝试';
            return false;
        }



        $extend_params = [
            'date_week' => $week,
            'user_id' => $user_info['client_id'],
            'date_month' => $month,
            'device_no' => $dataProtocal['device_no'],
            'week_request_num' =>  $user_face_id['request_num'] <= 0?0:$user_face_id['request_num'],
            'month_request_num' =>  $user_face_device['request_num'] <= 0?0:$user_face_device['request_num'],
        ];
        $transActions[] = new ModifyUserFaceByTrans($extend_params);
        if (!RewardUtil::RewardSaveByTransaction($transActions, $outInfo, $error))
        {
            return false;
        }

        $order_no = Uuid::uuid();  //唯一订单号
        $rstData['has_data'] = '1';
        $rstData['data_type'] = 'jsonarray';
        $rstData['data']['order_no'] = $order_no;
        $rstData['data']['status'] = 2;
        $rstData['data']['url'] = 'http://'.$_SERVER['HTTP_HOST'].'/mbliving/get_youdun_info';
        return true;
    }
}
