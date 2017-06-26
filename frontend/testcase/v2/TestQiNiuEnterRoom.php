<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/17
 * Time: 17:25
 */
namespace frontend\testcase\v2;
use common\models\ChatRoom;
use yii\log\Logger;


/**
 * 进和直播间
 * Class TestQiNiuEnterRoom
 * @package frontend\testcase\v2
 */
class TestQiNiuEnterRoom
{
    public static function  EnterRoom($data_params)
    {
        $data = [
            "unique_no" => $data_params['unique_no'],
            "register_type" => $data_params['register_type'],
            "living_id" => $data_params['living_id']
        ];
        $rstData = Common::PackagingParams($data_params['token'],$data_params['sign_key'],$data_params['crypt_key'],'qiniu_enter_room',$data,$data_params['device_no'],
            $data_params['device_type'],'json');
        $room_info = ChatRoom::findOne(['living_id' => $data_params['living_id']]);
        $rstData['other_id'] = $room_info->other_id;
        return $rstData;
    }
}