<?php
/**
 * Created by PhpStorm.
 * User: hlq
 * Date: 16-04-22
 * Time: 中午11:30
 */

namespace frontend\zhiboapi\v1;


use frontend\business\ClientUtil;
use frontend\business\JobUtil;
use frontend\business\LivingUtil;
use frontend\business\RewardUtil;
use frontend\business\SaveRecordByransactions\SaveByTransaction\QiNiuRcUpdateLivingSaveByTrans;
use frontend\business\SaveRecordByransactions\SaveByTransaction\QiNiuUpdateLivingSaveByTrans;
use frontend\zhiboapi\IApiExcute;
use Pili\Stream;

use frontend\business\ApiCommon;
use frontend\business\ChatPersonGroupUtil;
use frontend\business\RobotUtil;
use yii\log\Logger;
use frontend\business\AttentionUtil;

/**
 * Class 完善直播信息
 * @package frontend\zhiboapi\v3
 */
class ZhiBoQiNiuUpdateLiving implements IApiExcute
{
    public function excute_action($dataProtocal, &$rstData,&$error = '', $extendData= array())
    {
        \Yii::error('调用update_living:'.var_export($dataProtocal,true));
        //TODO:验证参数
        if(!$this->check_params_ok($dataProtocal,$error)) {
            return false;
        }
        $uniqueNo = $dataProtocal['data']['unique_no'];
        $user = ClientUtil::GetUserByUniqueId($uniqueNo);
        if(!$user){
            $error = '用户不存在';
            return false;
        }
        $data = $dataProtocal['data'];
        $data['room_master_id'] = $user['client_id'];
        $data['nick_name'] = $user['nick_name'];
        $living_info =LivingUtil::GetLivingById($data['living_id']);
        if(!isset($living_info)){
            $error = '直播不存在';
            return false;
        }
        //TODO:获取七牛流直播信息并更新
        $qiniu_info = \Yii::$app->cache->get('qiniu_living_'.strval($user['client_id']));
        if($qiniu_info === false) {
            $error = '七牛直播信息丢失';
            return false;
        }

        $qnStream = new Stream(NULL,json_decode($qiniu_info,true));
        $data['push_url'] = $qnStream->rtmpPublishUrl();
        $data['pull_http_url'] =$qnStream->httpFlvLiveUrls()['ORIGIN'];
        $data['pull_rtmp_url'] = $qnStream->rtmpLiveUrls()['ORIGIN'];
        $data['pull_hls_url'] = $qnStream->hlsLiveUrls()['ORIGIN'];
        $data['living_pic_url'] =$qnStream->GetLivingSnapUrl();
        $transActions[] = new QiNiuRcUpdateLivingSaveByTrans($data);
        if(!RewardUtil::RewardSaveByTransaction($transActions,$outInfo,$error))
        {
            return false;
        }

        //更新直播的同时，获取机器人的信息
        $robotinfo = RobotUtil::GetRobotInfoForUpdateLiving($data['living_id'],$user->client_id);
        if( $robotinfo ){
            $rstData['has_data'] = '1';
            $rstRobot['robotinfo'] = $robotinfo;
        }else{
            $rstData['has_data'] = '0';
            $rstRobot['robotinfo'] = [];
        }

        //获取当前关注主播的人数
//        $count =  AttentionUtil::GetAttentionFriendsToGetTui( $living_info->living_master_id );
//
//        if( $living_info->status <= 2 && $count>0 )
//        {
//            //创建成功向粉丝发送消息,批量发文本消息接口支持一次性针对最多500个用户进行单发消息；
//            $data_info = [
//                'key_word'=>'create_living_im',
//                'user_id' => $living_info->living_master_id,
//                'group_id' => $outInfo['group_id'],
//                'pic'=>$user->pic,
//                'living_id' => $data['data']['living_id'],
//                'nick_name' => $data['data']['nick_name'],
//                'app_id' => $living_info->app_id,
//                'count'  =>$count //获取当前关注主播的人数
//            ];
//            if(!JobUtil::AddImJob('tencent_im',$data_info,$error))
//            {
//                return false;
//            }
//        }
        //设置只增不减人数的缓存时间 20分钟
        $key = 'enter_room_no_sub_person_'.$data['living_id'];
        \Yii::$app->cache->set($key,'1',60*20);
        $rstData['data_type'] = 'string';
        $rstData['data'] = $rstRobot;
        \Yii::error('返回结果update_living:'.var_export($rstData,true));
        return true;

    }

    private function check_params_ok($dataProtocal,&$error)
    {
        $fields = ['unique_no','living_id'];
        $fieldLabels = ['唯一标识','直播间ID'];
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