<?php
/**
 * Created by PhpStorm.
 * User: hlq
 * Date: 16-04-22
 * Time: 中午11:30
 */

namespace frontend\zhiboapi\v3;


use frontend\business\ClientUtil;
use frontend\business\JobUtil;
use frontend\business\LivingUtil;
use frontend\business\RewardUtil;
use frontend\business\SaveRecordByransactions\SaveByTransaction\UpdateLivingSaveByTrans;
use frontend\zhiboapi\IApiExcute;
use yii\db\Query;
use yii\log\Logger;


/**
 * Class 完善直播信息
 * @package frontend\zhiboapi\v3
 */
class ZhiBoUpdateLiving implements IApiExcute
{

    /**
     * 完善直播信息
     * @param string $error
     */
    public function excute_action($dataProtocal, &$rstData,&$error, $extendData= array())
    {
        $error = '';
        $user = ClientUtil::GetUserByUniqueId($dataProtocal['data']['unique_no']);
        if(!$user)
        {
            $error = '用户不存在';
            return false;
        }
        $dataProtocal['data']['room_master_id'] = $user->client_id;
        $dataProtocal['data']['nick_name'] = $user->nick_name;
        //$living_query = new Query();
        $living_info =LivingUtil::GetLivingById($dataProtocal['data']['living_id']);// $living_query->from('mb_living')->select(['living_id','status'])->where(['living_id'=>$dataProtocal['data']['living_id']])->one();
        if(!isset($living_info)){
            $error = '直播不存在';
            return false;
        }

        if(empty($dataProtocal['data']['group_id'])){
            $error = '群id不存在';
            return false;
        }

        $data = $dataProtocal;
        \Yii::getLogger()->log('living_status:'.$living_info->status,Logger::LEVEL_ERROR);
        \Yii::getLogger()->log('流信息:'.var_export($data['data'],true),Logger::LEVEL_ERROR);
        /*$transActions[] = new UpdateLivingSaveByTrans($data);
        if(!RewardUtil::RewardSaveByTransaction($transActions,$outInfo,$error))
        {
            return false;
        }*/
        exit;
        if($living_info->status < 2)
        {
            //创建成功向粉丝发送消息,批量发文本消息接口支持一次性针对最多500个用户进行单发消息；
            $data_info = [
                'key_word'=>'create_living_im',
                'user_id' => $data['data']['room_master_id'],
                'group_id' => $outInfo['group_id'],
                'pic'=>$user->pic,
                'living_id' => $data['data']['living_id'],
                'nick_name' => $data['data']['nick_name'],
            ];
            //$key = 'create_living_send_info';
            \Yii::getLogger()->log('调用:',Logger::LEVEL_ERROR);
            if(!JobUtil::AddImJob('tencent_im',$data_info,$error))
            {
                return false;
            }
        }
        //设置只增不减人数的缓存时间 20分钟
        $key = 'enter_room_no_sub_person_'.$dataProtocal['data']['living_id'];
        \Yii::$app->cache->set($key,'1',60*20);
        $rstData['has_data'] = '0';
        $rstData['data_type'] = 'string';
        $rstData['data'] = [];

        return true;
    }
}