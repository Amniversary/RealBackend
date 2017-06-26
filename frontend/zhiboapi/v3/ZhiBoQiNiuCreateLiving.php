<?php
/**
 * Created by PhpStorm.
 * User: hlq
 * Date: 16-04-21
 * Time: 下午5:30
 */

namespace frontend\zhiboapi\v3;

use common\components\GameRebotsHelper;
use common\components\SystemParamsUtil;
use common\models\OffUserLiving;
use frontend\business\EnterRoomNoteUtil;
use frontend\business\JobUtil;
use frontend\business\LivingUtil;
use frontend\business\RewardUtil;
use frontend\business\RobotUtil;
use frontend\business\SaveByTransUtil;
use frontend\business\SaveRecordByransactions\SaveByTransaction\CreateLivingSaveByTrans;
use frontend\zhiboapi\IApiExcute;
use yii\log\Logger;
use common\models\StopLiving;
use common\models\SystemParams;

/**
 * Class 七牛创建直播
 * @package frontend\zhiboapi\v3
 */
class ZhiBoQiNiuCreateLiving implements IApiExcute
{
    public function excute_action($dataProtocal, &$rstData,&$error = '', $extendData= array())
    {
        \Yii::error('dataproTocal'.var_export($dataProtocal,true));
        //TODO: 是否开启直播
        if(!$this->openLive($error)) {
            return false;
        }
        //TODO: 参数验证
        if(!$this->check_params_ok($dataProtocal,$error)) {
            return false;
        }
        $uniqueNO = $dataProtocal['data']['unique_no'];
        $livingUserInfo = LivingUtil::GetLivingAndUserInfoByUniqueId($uniqueNO); //TODO: 获取直播间和用户信息
        if($livingUserInfo === false) {
            $error = '直播用户信息异常';
            return false;
        }
        // TODO:判断直播间的状态 1为封播，0为正常
        $StopLivingModel = StopLiving::findOne(['living_id'=>$livingUserInfo['living_id']]);
        if(!empty($StopLivingModel)) {
            if($StopLivingModel['status'] == 1) {
                $error = '您的直播间已被禁用，如需解除禁用请联系相关客服人员。';
                \Yii::error($error.'  living_id:'.$livingUserInfo['living_id']);
                return false;
            }
        }

        //TODO: 开播白名单用户
        /*$white_user = OffUserLiving::findone(['client_no' => $livingUserInfo['client_no']]);
        if(!isset($white_user)) {
            $error = '开播请添加客服QQ';
            return false;
        }*/
        //签约主播必须认证
        /*$code_type = SystemParamsUtil::GetSystemParam('get_system_white_off',true);
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
                //\Yii::getLogger()->log('签约主播必须进行认证才能直播',Logger::LEVEL_ERROR);
                $rstData['errmsg'] = '签约主播必须进行认证才能直播！';
                return false;
            }
        }*/
        $op_unique_no = $livingUserInfo['op_unique_no'];
        $data = $dataProtocal['data'];
        $data['device_type'] = $dataProtocal['device_type'];
        $data['app_id'] = $dataProtocal['app_id'];
        unset($dataProtocal);

        if($livingUserInfo['status'] == 0) {
            $error = '您的账号已被禁用，请与管理员联系';
            \Yii::error($error.' status:'.$livingUserInfo['status']);
            return false;
        }
        if($livingUserInfo['living_status'] == 2) {  //TODO:直播间未结束 结束直播间
            if (!LivingUtil::SetFinishLiving($livingUserInfo['living_id'], $outinfomain, $error)) {
                return false;
            }
        }
        if(!isset($data['living_type']) || //TODO:默认普通直播
            empty($data['living_type'])) {
            $data['living_type'] = 1;
        }
        //TODO:根据直播类型判断是否是密码直播，是密码直播则限制人数
        //TODO:living_type  1 ：正常直播  3：密码直播  4：门票直播  5:假直播
        if(in_array($data['living_type'],[3,4,5]))
        {
            if(!isset($data['room_no']) ||
                empty($data['room_no'])) {
                $error = '房间号不能为空';
                return false;
            }
            if($data['living_type'] == 4) {
                $tickets = SystemParamsUtil::GetSystemParam('living_ticket_min_num',true,'value1'); //门票直播票配置
                if($data['tickets'] < $tickets) {
                    $error = '门票金额不能小于'.$tickets.'鲜花';
                    return false;
                }
            }
            $LivingLimitNum = LivingUtil::getLivingLimitNum(); //TODO: 设置直播间限制人数
            LivingUtil::UpdateLivingLimitNum($LivingLimitNum['value1'],$livingUserInfo['living_id']);
        }

        if(empty($livingUserInfo['other_id'])){
            $livingUserInfo['other_id'] = '';
        }
        if(empty($data['longitude'])){ //TODO:重置经纬度
            $data['longitude'] = 0;
        }
        if(empty($data['latitude'])){
            $data['latitude'] = 0;
        }
        $data['living_master_id'] = $livingUserInfo['living_master_id'];
        $transActions[] = new CreateLivingSaveByTrans($data);
        if(!SaveByTransUtil::RewardSaveByTransaction($transActions, $error, $outInfo)) {
            \Yii::error('error: '.$error);
            return false;
        }
        if(in_array($data['living_type'],[3,4])) { //TODO:创建直播成功，将房间号设置为已经使用
            if(!LivingUtil::SetRoomNoIsUse($data['room_no'],$error)) {
                return false;
            }
        }
        if($data['living_type'] == 3) {
            //TODO:私密直播写入缓存
            if(!LivingUtil::privateLivingCache($outInfo['living_id'],$outInfo['living_before_id'],$data['password'],$livingUserInfo['living_master_id'],$error)) {
                return false;
            }
        }
        //TODO:查询是否获取机器人
        $robotInfo = [];
        if($data['living_type'] == 1) { //TODO: 私密直播、门票直播、假直播过滤机器人
            $robotInfo = RobotUtil::GetRobotPeopleParams($livingUserInfo['living_master_id']);
        }

        $sysMsg = EnterRoomNoteUtil::GetSystemMsgToArray();
        $rstData['has_data'] = '1';
        $rstData['data_type'] = 'jsonarray';
        $rstData['data'] = [
            'living_id'=>$outInfo['living_id'],
            'living_master_id'=>$livingUserInfo['living_master_id'],
            'op_unique_no' => $op_unique_no,
            'group_id' => $livingUserInfo['other_id'],
            'is_status' => 3,
            'system_msg' => $sysMsg,
            'is_robot' => (!empty($robotInfo['create_robot_no']) ? 1: 0),
            'living_type' => $data['living_type'],
        ];
        \Yii::error('rstDate:'.var_export($rstData,true));
        //$rstData['data']['group_id'] = $livingUserInfo['other_id'];
        return true;
    }


    private function openLive(&$error)
    {
        $isOpenLive = \Yii::$app->cache->get("is_open_living");
        if(!$isOpenLive) {
            $model = SystemParams::findOne(['code' => 'is_open_living']);
            if ($model['value1'] == 2) {
                $error = '直播更新升级中，尽情期待';
                return false;
            }
        }else {
            if($isOpenLive == 2) {
                $error = '直播更新升级中，尽情期待';
                return false;
            }
        }
        return true;
    }

    private function check_params_ok($dataProtocal,&$error = '')
    {
        $fields = ['unique_no'];
        $fieldLabels = ['唯一标识'];
        $len =count($fields);
        for($i = 0; $i <$len; $i ++)
        {
            if (!isset($dataProtocal['data'][$fields[$i]]) || empty($dataProtocal['data'][$fields[$i]])) {
                $error = $fieldLabels[$i] . '，不能为空';
                return false;
            }
        }
        if(!isset($dataProtocal['data']['is_continue']))
        {
            $error = '参数错误';
            return false;
        }
        return true;
    }
}