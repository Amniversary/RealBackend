<?php
/**
 * Created by PhpStorm.
 * User: hlq
 * Date: 16-04-22
 * Time: 中午11:30
 */

namespace frontend\zhiboapi\v2;


use frontend\business\ClientUtil;
use frontend\business\JobUtil;
use frontend\business\LivingUtil;
use frontend\business\RewardUtil;
use frontend\business\SaveRecordByransactions\SaveByTransaction\QiNiuUpdateLivingSaveByTrans;
use frontend\zhiboapi\IApiExcute;
use Pili\Stream;

use frontend\business\ApiCommon;
use frontend\business\ChatPersonGroupUtil;
use frontend\business\RobotUtil;
use yii\log\Logger;
use frontend\business\AttentionUtil;
use frontend\business\SendMsgUtil;
/**
 * Class 完善直播信息
 * @package frontend\zhiboapi\v2
 */
class ZhiBoQiNiuUpdateLiving implements IApiExcute
{

    /**
     * 完善直播信息
     * @param string $error
     */
    public function excute_action($dataProtocal, &$rstData,&$error, $extendData= array())
    {
        $error = '';
        $user = ClientUtil::GetUserByUniqueId($dataProtocal['data']['unique_no']);
        if (!$user) {
            $error = '用户不存在';
            return false;
        }
        $dataProtocal['data']['room_master_id'] = $user->client_id;
        $dataProtocal['data']['nick_name'] = $user->nick_name;
        //$living_query = new Query();
        //\Yii::getLogger()->log('info22:'.var_export($dataProtocal['data'],true),Logger::LEVEL_ERROR);
        $living_info = LivingUtil::GetLivingById($dataProtocal['data']['living_id']);// $living_query->from('mb_living')->select(['living_id','status'])->where(['living_id'=>$dataProtocal['data']['living_id']])->one();
        if (!isset($living_info)) {
            $error = '直播不存在';
            return false;
        }

        if (empty($dataProtocal['data']['group_id'])) {
            $error = '群id不存在';
            return false;
        }

        //获取直播流信息并更新
        //获取七牛直播信息
        $key = 'qiniu_living_' . strval($user->client_id);
        $qiniu_info = \Yii::$app->cache->get($key);
        if($qiniu_info === false)
        {
            $error = '七牛直播信息丢失';
            return false;
        }
        $qnStream = new Stream(NULL, json_decode($qiniu_info, true));
        //\Yii::getLogger()->log('user_id:'.strval($user->client_id).' info:'.$qiniu_info,Logger::LEVEL_ERROR);
        $dataProtocal['data']['push_url'] = $qnStream->rtmpPublishUrl();
        $dataProtocal['data']['pull_http_url'] = $qnStream->httpFlvLiveUrls()['ORIGIN'];
        $dataProtocal['data']['pull_rtmp_url'] = $qnStream->rtmpLiveUrls()['ORIGIN'];
        $dataProtocal['data']['pull_hls_url'] = $qnStream->hlsLiveUrls()['ORIGIN'];
        $dataProtocal['data']['living_pic_url'] = $qnStream->GetLivingSnapUrl();
        //\Yii::getLogger()->log('user_id:'.strval($user->client_id).' info:'.var_export($dataProtocal['data'],true),Logger::LEVEL_ERROR);
        $data = $dataProtocal;
        $transActions[] = new QiNiuUpdateLivingSaveByTrans($data);
        if(!RewardUtil::RewardSaveByTransaction($transActions,$outInfo,$error))
        {
            return false;
        }

        //更新直播的同时，获取机器人的信息
        $robotinfo = RobotUtil::GetRobotInfoForUpdateLiving($dataProtocal['data']['living_id'], $user->client_id);
        if ($robotinfo) {
            $rstData['has_data'] = '1';
            $rstRobot['robotinfo'] = $robotinfo;
        } else {
            $rstData['has_data'] = '0';
            $rstRobot['robotinfo'] = [];
        }

        //获取当前关注主播的人数
        if ($living_info->status <= 2) {
            //创建成功向粉丝发送消息,批量发文本消息接口支持一次性针对最多500个用户进行单发消息；
            $data_info = [
                'key_word' => 'create_living_im',
                'user_id' => $data['data']['room_master_id'],
                'group_id' => $outInfo['group_id'],
                'pic' => $user->pic,
                'living_id' => $data['data']['living_id'],
                'nick_name' => $data['data']['nick_name'],
                'app_id' => $living_info->app_id,
            ];


            if(!JobUtil::AddImJob('tencent_im',$data_info,$error))
            {
                return false;
            }
           // if ($data['data']['room_master_id'] == '277937' || $data['data']['room_master_id'] == '215833') {
             //   \Yii::$app->sendmsgBeanstalk->putInTube("sendmsg",$data_info);
           // }
        }

        //SendMsgUtil::CreateLivingToSendGutui($data['data']['room_master_id'], $data['data']['nick_name'],$outInfo['group_id'],$data['data']['living_id']);

        //设置只增不减人数的缓存时间 20分钟
        $key = 'enter_room_no_sub_person_'.$dataProtocal['data']['living_id'];
        \Yii::$app->cache->set($key,'1',60*20);
        $rstData['data_type'] = 'string';
        $rstData['data'] = $rstRobot;
        return true;
    }
}