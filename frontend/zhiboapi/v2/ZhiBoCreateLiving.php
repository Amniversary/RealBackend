<?php
/**
 * Created by PhpStorm.
 * User: hlq
 * Date: 16-04-21
 * Time: 下午5:30
 */

namespace frontend\zhiboapi\v2;

use frontend\business\ClientUtil;
use frontend\business\EnterRoomNoteUtil;
use frontend\business\LivingUtil;
use frontend\business\RewardUtil;
use frontend\business\SaveRecordByransactions\SaveByTransaction\CreateLivingSaveByTrans;
use frontend\zhiboapi\IApiExcute;
use yii\log\Logger;


/**
 * Class 创建直播
 * @package frontend\zhiboapi\v2
 */
class ZhiBoCreateLiving implements IApiExcute
{

    /**
     * 创建直播
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

        $living_info = LivingUtil::GetLivingUserInfo($user->client_id);
        $living_room_info = LivingUtil::GetRoomInfoLiving($living_info->living_id);

        $op_unique_no = $living_info->op_unique_no;
        if(empty($living_info)){
            $op_unique_no = $dataProtocal['data']['op_unique_no'];
        }

        if($living_info['status'] == 3){
            $error = '直播已被禁止，请与管理员联系';
            return false;
        }

        if(!isset($dataProtocal['data']['is_continue'])){
            $error = '参数错误';
            return false;
        }
        switch($dataProtocal['data']['is_continue']){
            case 0 :
                if($living_info->status === 2)//提示app是否继续直播
                {
                    $rstData['has_data'] = '1';
                    $rstData['data_type'] = 'json';
                    $rstData['data']['is_status'] = 1;  //用户正在直播
                    $rstData['data']['group_id'] = $living_room_info['other_id'];
                    $rstData['data']['living_id'] = $living_info->living_id;
                    $rstData['data']['living_master_id'] = $user->client_id;
                    $rstData['data']['op_unique_no'] = $living_info->op_unique_no;
//\Yii::getLogger()->log('is_status = 1 ----$dataProtocal===:'.$dataProtocal,Logger::LEVEL_ERROR);

                    return true;
                }
                break;
            case 1 :      //继续直播
                $rstData['has_data'] = '1';
                $rstData['data_type'] = 'json';
                $rstData['data']['is_status'] = 2;
                $rstData['data']['group_id'] = $living_room_info['other_id'];
                $rstData['data']['living_id'] = $living_info->living_id;
                $rstData['data']['living_master_id'] = $user->client_id;
                $rstData['data']['op_unique_no'] = $living_info->op_unique_no;
                $sysMsg = EnterRoomNoteUtil::GetSystemMsgToArray();
                $rstData['data']['system_msg'] = $sysMsg;
//                \Yii::getLogger()->log('is_status = 2 ----$dataProtocal===:'.$dataProtocal,Logger::LEVEL_ERROR);
                return true;
            case 2:
                if(!LivingUtil::SetFinishLiving($living_info->living_id,$outinfomain,$error))
                {
                    return false;
                }
                break;
            default :
                $error = '参数错误';
                return false;
        }

        if(empty($living_room_info['other_id'])){
            $living_room_info['other_id'] = '';
        }
        if(empty($dataProtocal['data']['longitude'])){
            $dataProtocal['data']['longitude'] = 0;
        }
        if(empty($dataProtocal['data']['latitude'])){
            $dataProtocal['data']['latitude'] = 0;
        }
        $dataProtocal['data']['living_master_id'] = $user->client_id;
        $dataProtocal['data']['op_unique_no'] = $op_unique_no;
        $dataProtocal['data']['living_id'] = $living_info->living_id;
//        \Yii::getLogger()->log('-----living_id==='.$living_info->living_id,Logger::LEVEL_ERROR);
        $data = $dataProtocal;
        $transActions[] = new CreateLivingSaveByTrans($data);

        if(!RewardUtil::RewardSaveByTransaction($transActions,$outInfo, $error))
        {
            return false;
        }


        $rstData['has_data'] = '1';
        $rstData['data_type'] = 'json';
        $rstData['data']['group_id'] = $living_room_info['other_id'];
        $rstData['data']['living_id'] = $outInfo['living_id'];
        $rstData['data']['living_master_id'] = $user->client_id;
        $rstData['data']['op_unique_no'] = $op_unique_no;
        $rstData['data']['is_status'] = 3;
        $sysMsg = EnterRoomNoteUtil::GetSystemMsgToArray();
        $rstData['data']['system_msg'] = $sysMsg;
//        \Yii::getLogger()->log('is_status = 3 ----$dataProtocal===:'.$dataProtocal,Logger::LEVEL_ERROR);
        return true;
    }
}