<?php
namespace frontend\zhiboapi\v1;

use common\components\GameRebotsHelper;
use common\components\SystemParamsUtil;
use common\models\RoomNoList;
use frontend\business\LivingUtil;
use frontend\zhiboapi\IApiExcute;


/**
 * Class 获取创建直播参数
 * @package frontend\zhiboapi\v3
 */
class ZhiBoGetCreateLivingParams implements IApiExcute
{
    /**
     * 获取创建直播参数接口
     * @param string $error
     */
    public function excute_action($dataProtocal, &$rstData,&$error, $extendData= array())
    {
        $error = '';
//        do{
//            $room_no = GameRebotsHelper::GetJobDates('roomNoBeanstalk','room_no');   //获取唯一房间号
//            if(!empty($room_no['room_no']))
//            {
//                $room_model = RoomNoList::findOne(['room_no' => $room_no['room_no']]);
//            }
//        }while(!empty($room_no) && ($room_model->is_use != 0));
        $room_no = LivingUtil::GetRoomNoOne($error);
        if(!$room_no)
        {
            return false;
        }
        $tickets = SystemParamsUtil::GetSystemParam('living_ticket_min_num',true,'value1'); //票
        $notice = LivingUtil::GetCreateLivingNotice();
        $rstData['has_data'] = '1';
        $rstData['data_type'] = 'json';
        $rstData['data']['room_no'] = $room_no;
        $rstData['data']['tickets'] = $tickets;
        $rstData['data']['notice'] = $notice['system_message'];
        return true;
    }
}
