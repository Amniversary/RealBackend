<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/1
 * Time: 17:10
 */
namespace frontend\zhiboapi\v2;

use frontend\business\ApiCommon;
use frontend\business\StopLivingUtil;
use frontend\business\ClientUtil;
use common\models\StopLiving;
use frontend\zhiboapi\IApiExcute;
use yii\log\Logger;
use common\components\ClearCacheHelper;
use frontend\business\LivingUtil;
use yii\db\Query;
use common\components\tenxunlivingsdk\TimRestApi;

class ZhiBoStopLiving implements IApiExcute
{

    /**
     * 封播
     * @param string $error
     */
    public function excute_action($dataProtocal, &$rstData, &$error, $extendData = array())
    {

        $error = '';
        $rstData['has_data'] = 1;
        $rstData['data_type'] = 'json';

        $unique_no = $dataProtocal['data']['unique_no'];

        if(!ApiCommon::GetLoginInfo($unique_no,$LoginInfo,$error))
        {
            \Yii::getLogger()->log('stop-living=====>'.$error,Logger::LEVEL_ERROR);
            return false;
        }
        $user = ClientUtil::GetUserByUniqueId($dataProtocal['data']['unique_no']);
        if(!$user)
        {
            $error = '用户不存在';
            return false;
        }

        $liveInfo = LivingUtil::GetLivingById( $dataProtocal['data']['living_id'] );
        if( !$liveInfo ){
            $error = '直播不存在';
            return false;
        }

        if( $liveInfo->living_master_id == $user->client_id ){
            $error = '不能自己禁用自己';
            return false;
        }

        if( !StopLivingUtil::StopLiving($dataProtocal['data']['living_id'],$user->client_id,1,$error) )
        {
            ClearCacheHelper::ClearHotLivingDataCache();
            $rstData['errno']     = 1;
            $rstData['errmsg']    = $error;
            \Yii::getLogger()->log('操作封播时发生了错误：详情请看日记:'.$error,Logger::LEVEL_ERROR);
            return false;

        }

        $finishInfo = null;
        $qurey = new Query();
        $qurey->from('mb_living li')->select(['li.living_before_id','li.living_id','living_master_id','cr.other_id','finish_time'])
            ->innerJoin('mb_chat_room cr','li.living_id = cr.living_id')
            ->where(['li.living_id'=>$dataProtocal['data']['living_id'] ]);
        $living = $qurey->one();


        //发送im消息
        if( !LivingUtil::SetBanClientFinishLivingToStopLiving( $dataProtocal['data']['living_id'],$finishInfo,$living['living_master_id'],$living['other_id'],$outInfo,$error) )
        {
            ClearCacheHelper::ClearHotLivingDataCache();
            $rstData['errno']     = 1;
            $rstData['errmsg']    = $error;
            \Yii::getLogger()->log('操作封播时发生了错误：详情请看日记:'.$error,Logger::LEVEL_ERROR);
            return false;
        }

        $rstData['errno']     = 0;
        $rstData['errmsg']    = '封播成功';

        return true;
    }
}