<?php
/**
 * Created by PhpStorm.
 * User: hlq
 * Date: 16-04-21
 * Time: 下午5:30
 */

namespace frontend\zhiboapi\v2;

use common\components\GameRebotsHelper;
use common\components\SystemParamsUtil;
use common\models\OffUserLiving;
use frontend\business\EnterRoomNoteUtil;
use frontend\business\JobUtil;
use frontend\business\LivingUtil;
use frontend\business\RewardUtil;
use frontend\business\RobotUtil;
use frontend\business\SaveRecordByransactions\SaveByTransaction\CreateLivingSaveByTrans;
use frontend\zhiboapi\IApiExcute;
use yii\log\Logger;
use common\models\StopLiving;
use common\models\SystemParams;

/**
 * Class 七牛创建直播
 * @package frontend\zhiboapi\v2
 */
class ZhiBoQiNiuCreateLiving implements IApiExcute
{

    /**
     * 创建直播
     * @param string $error
     */
    public function excute_action($dataProtocal, &$rstData,&$error, $extendData= array())
    {

        $isOpenLive = \Yii::$app->cache->get("is_open_living");
        if( $isOpenLive ){
            if( $isOpenLive == 2 ){
                $error = '直播更新升级中，尽情期待';
                \Yii::getLogger()->log('living-msg:'.$error,Logger::LEVEL_ERROR);
                return false;
            }
        }else{
            $model =  SystemParams::findOne(['code'=>'is_open_living']);
            if( $model->value1 == 2 ){
                $error = '直播更新升级中，尽情期待';
                \Yii::getLogger()->log('living-msg:'.$error,Logger::LEVEL_ERROR);
                return false;
            }
        }
        $error = '';
        if($dataProtocal['api_version'] == 'v2')
        {
            $error = '开启直播失败，蜜播App版本过低，请更新到最新版本!';
            return false;
        }
        $livingUserInfo = LivingUtil::GetLivingAndUserInfoByUniqueId($dataProtocal['data']['unique_no']);
        //\Yii::getLogger()->log('living_user_info:'.var_export($livingUserInfo,true),Logger::LEVEL_ERROR);

        //判断直播间的状态 1为封播，0为正常
        $StopLivingModel = StopLiving::findOne(['living_id'=>$livingUserInfo['living_id']]);
        if(isset($StopLivingModel))
        {
            $isLivingStatus = $StopLivingModel->status;
            if( $isLivingStatus == 1 )
            {
                $error = '您的直播间已被禁用，如需解除禁用请联系相关客服人员。';
                \Yii::getLogger()->log($error.'  living_id:'.$livingUserInfo['living_id'],Logger::LEVEL_ERROR);
                return false;
            }
        }

        if($livingUserInfo === false)
        {
            $error = '直播用户信息异常';
            return false;
        }
        //$error = '直播更新升级中，尽情期待';
    	//return false;
        //签约主播必须认证
        //\Yii::getLogger()->log('lving_User_id:'.var_export($livingUserInfo,true),Logger::LEVEL_ERROR);
        $code_type = SystemParamsUtil::GetSystemParam('get_system_white_off',true);
        if($code_type == 1)
        {
            $is_off = OffUserLiving::findOne(['client_no'=>$livingUserInfo['client_no']]);
            if(!isset($is_off))
            {
                return false;
            }
        }
        if($code_type == 0)
        {
            if(($livingUserInfo['is_contract'] == 2) && (!in_array($livingUserInfo['is_centification'],[2])))
            {
                if($livingUserInfo['is_centification'] == 3)
                {
                    $rstData['errno'] = '1114';
                    $rstData['errmsg'] = '签约主播认证正在审核中';
                    return false;
                }
                $rstData['errno'] = '1113';
                //            \Yii::getLogger()->log('签约主播必须进行认证才能直播',Logger::LEVEL_ERROR);
                $rstData['errmsg'] = '签约主播必须进行认证才能直播！';
                return false;
            }
        }
        $op_unique_no = $livingUserInfo['op_unique_no'];
        $data = $dataProtocal;

        if($livingUserInfo['status'] == 0)
        {
            $error = '您已被禁止，请与管理员联系';
            return false;
        }

        if(!isset($dataProtocal['data']['is_continue']))
        {
            $error = '参数错误';
            return false;
        }
        if($livingUserInfo['living_status'] == 2)
        {
            if (!LivingUtil::SetFinishLiving($livingUserInfo['living_id'], $outinfomain, $error)) {
                return false;
            }
        }

        if(!isset($data['data']['living_type']) || empty($data['data']['living_type']))
        {
            $data['data']['living_type'] = 1;
        }
        //根据直播类型判断是否是密码直播，是密码直播则限制人数
        // living_type  1 ：正常直播  3：密码直播  4：门票直播 5:假直播
        if(in_array($data['data']['living_type'],[3,4,5]))
        {
            if(!isset($data['data']['room_no']) || empty($data['data']['room_no']))
            {
                $error = '房间号不能为空';
                return false;
            }
            if($data['data']['living_type'] == 4)
            {
                $tickets = SystemParamsUtil::GetSystemParam('living_ticket_min_num',true,'value1'); //门票直播票配置
                if($data['data']['tickets'] < $tickets)
                {
                    $error = '门票不能小于'.$tickets;
                    return false;
                }
            }

            $LivingLimitNum = LivingUtil::getLivingLimitNum();
            LivingUtil::UpdateLivingLimitNum($LivingLimitNum['value1'],$livingUserInfo['living_id']);
        }

        if(empty($livingUserInfo['other_id'])){
            $livingUserInfo['other_id'] = '';
        }
        if(empty($dataProtocal['data']['longitude'])){
            $dataProtocal['data']['longitude'] = 0;
        }
        if(empty($dataProtocal['data']['latitude'])){
            $dataProtocal['data']['latitude'] = 0;
        }
//        \Yii::getLogger()->log('-----living_id==='.$living_info->living_id,Logger::LEVEL_ERROR);

        $data['data']['living_master_id']=$livingUserInfo['living_master_id'];

        $transActions[] = new CreateLivingSaveByTrans($data);

        if(!RewardUtil::RewardSaveByTransaction($transActions,$outInfo, $error))
        {
            \Yii::getLogger()->log('errror:'.$error,Logger::LEVEL_ERROR);
            return false;
        }
        if(in_array($data['data']['living_type'],[3,4]))   //创建直播成功，将房间设置为已经使用过
        {
            if(!LivingUtil::SetRoomNoIsUse($data['data']['room_no'],$error))
            {
                return false;
            }
        }

        if($data['data']['living_type'] == 3)
        {
            /*********私密直播缓存***********/
            $cache_key = 'private_living_info_'.$outInfo['living_id'];
            $cache_data = [
                'living_before_id' => $outInfo['living_before_id'],
                'living_id' => $outInfo['living_id'],
                'password' => $data['data']['password'],
                'user_id' => $livingUserInfo['living_master_id']
            ];
            $cache_data = json_encode($cache_data);
            $cache = \Yii::$app->cache->set($cache_key,$cache_data,3600*24*2);
            \Yii::getLogger()->log('private_living_info_====:'.$cache_key,Logger::LEVEL_ERROR);
            if(!$cache)
            {
                $error = '私密直播缓存写入失败';
                \Yii::getLogger()->log($error,Logger::LEVEL_ERROR);
                return false;
            }
        }

        //\Yii::getLogger()->log('ffffffffffffff',Logger::LEVEL_ERROR);
        //查询是否获取机器人
        $robotInfo = [];
        if(!in_array($data['data']['living_type'],[3,4,5])) //私密直播、门票直播、假直播过滤机器人
        {
            $robotInfo = RobotUtil::GetRobotPeopleParams($livingUserInfo['living_master_id']);
        }
       \Yii::$app->cache->delete('niuniu_game_info_'.$outInfo['living_id']);  //清除游戏缓存

        $rstData['has_data'] = '1';
        $rstData['data_type'] = 'json';
//        $rstData['data'] = 'adsadwqadas';
        $rstData['data']['group_id'] = $livingUserInfo['other_id'];
        $rstData['data']['living_id'] = $outInfo['living_id'];
        $rstData['data']['living_master_id'] = $livingUserInfo['living_master_id'];
        $rstData['data']['op_unique_no'] = $op_unique_no;
        $rstData['data']['is_status'] = 3;
        $sysMsg = EnterRoomNoteUtil::GetSystemMsgToArray();
        $rstData['data']['system_msg'] = $sysMsg;
        $rstData['data']['is_robot']=(intval($robotInfo['create_robot_no'])> 0? 1 : 0);
        $rstData['data']['living_type'] = $dataProtocal['data']['living_type'];
        //放在登录后返回
        //$rstData['data']['qiniu_info']=$qiniu_info;

//       \Yii::getLogger()->log('create_living:'.var_export($rstData,true),Logger::LEVEL_ERROR);

        return true;
    }
}