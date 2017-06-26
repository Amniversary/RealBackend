<?php
/**
 * Created by PhpStorm.
 * User: hlq
 * Date: 16-04-26
 * Time: 下午5:30
 */

namespace frontend\zhiboapi\v3;

use common\components\QiNiuUtil;
use frontend\business\ApiCommon;
use frontend\business\ChatGroupUtil;
use frontend\business\ClientQiNiuUtil;
use frontend\business\HeartBeatUtil;
use frontend\business\JobUtil;
use frontend\business\LivingUtil;
use frontend\zhiboapi\IApiExcute;
use yii\log\Logger;


/**
 * Class 心跳
 * @package frontend\zhiboapi\v3
 */
class ZhiBoHeartBeat implements IApiExcute
{
    /**
     * 检查参数合法性
     * @param string $error
     */
    private function check_param_ok($dataProtocal,&$error='')
    {
        $fields = ['living_id'];//'wish_type_id',
        $fieldLabels = ['直播id'];//'愿望类别id',
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

    public function excute_action($dataProtocal, &$rstData,&$error, $extendData= array())
    {
        $error = '';
        if(!$this->check_param_ok($dataProtocal, $error))
        {
            return false;
        }
        $deviceNo = '';
        $uniqueNo= '';
        $registerType='';
        $deviceType='';
        if(!ApiCommon::GetBaseInfoFromProtocol($dataProtocal, $deviceNo, $uniqueNo,$registerType,$deviceType,$error))
        {
            return false;
        }
        $loginInfo = null;
        if(!ApiCommon::GetLoginInfo($uniqueNo,$loginInfo, $error))
        {
            return false;
        }
        $user_id  = $loginInfo['user_id'];
        $passParams = $dataProtocal['data'];
        unset($passParams['unique_no']);
        unset($passParams['register_type']);
        $passParams['device_type'] = $deviceType;
        $passParams['device_no'] = $deviceNo;
/*        $key = 'client_forbid_flag_'.strval($user_id);
        $flag = \Yii::$app->cache->get($key);
        $rstData['has_data']='1';
        $rstData['data_type']="jsonarray";
        $rstData['data']=['is_forbid'=>'0'];
        if($flag !== false)
        {
            $rstData['data']=['is_forbid'=>'1'];
            return true;
        }*/

        $data=[
            'user_id'=>$user_id,
            'living_id'=>$passParams['living_id'],
            'heart_time'=>date('Y-m-d H:i:s')
        ];

        if(!HeartBeatUtil::DealHeartBeat($data,$error))
        {
            return false;
        }
/*
        //检测七牛流是否断开，断开后结束直播
            $qiniuInfo = ClientQiNiuUtil::GetQiNiuInfoByClientId($user_id);
            $qInfo = json_decode($qiniuInfo->qiniu_info,true);
            $stream_id= $qInfo['id'];
            $statusRst = QiNiuUtil::QueryStatus($stream_id,$error);
            if($statusRst === false)
            {
                return true;//不做处理
            }
            //推流已经结束，结果直播
            if($statusRst['status'] === 'disconnected')
            {
                $chatRoom = ChatGroupUtil::GetChatGroupByLivingId($passParams['living_id']);
                if(isset($chatRoom))
                {
                    $finishInfo = null;
                    if(!LivingUtil::SetBanClientFinishLiving($passParams['living_id'],$finishInfo,$user_id,$chatRoom->other_id,$outInfo,$error))
                    {
                        \Yii::getLogger()->log('结束异常直播异常：'.$error,Logger::LEVEL_ERROR);
                    }
                }
                else
                {
                    \Yii::getLogger()->log('结束异常直播失败，找不到群信息，living_id：'.$passParams['living_id'],Logger::LEVEL_ERROR);
                }
            }
*/
        //\Yii::getLogger()->log('发送心跳，living_id:'.$passParams['living_id'],Logger::LEVEL_ERROR);
//        if(!JobUtil::AddJob('living_heart',$data,$error))
//        {
//            //$error = '心跳失败';
//            return false;
//        }
        return true;
    }



}


