<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/17
 * Time: 17:25
 */
namespace frontend\testcase\v2;

use common\models\ChatRoom;
use frontend\business\JobUtil;
use frontend\testcase\IApiExcute;
use yii\log\Logger;

/**
 * 弹幕
 * Class TestSendDanmaku
 * @package frontend\testcase\v2
 */
class TestSendDanmaku
{
    public static  function SendDanmaku($data_params)
    {
        $data = [
            "unique_no" => $data_params['unique_no'],
            "register_type" => $data_params['register_type'],
            "living_id" => $data_params['living_id']
        ];
        $rstData = Common::PackagingParams($data_params['token'],$data_params['sign_key'],$data_params['crypt_key'],'send_danmaku',$data,$data_params['device_no'],
            $data_params['device_type'],'json',$data_params['unique_no']);

        //显示消息
        $user_client = json_decode($data_params['info'],true);
        $client_info = TestGetClientInfo::GetClientInfo($data_params);
        $client_info = json_decode($client_info['info'],true);
        $test_send_danmaku_params =
            [
            'key_word' => 'test_im',
            'user_id' => $user_client['data']['user_id'],
            'type' => '504',
            'msg' => '弹幕消弹幕消息弹幕消息',
            'isAdministrator' => '1',
            'userinfo' => $client_info['data'],
            'other_id' => $data_params['other_id']
        ];

        if(!JobUtil::AddCustomJob('ImBeanstalk','tencent_im',$test_send_danmaku_params,$error))
        {
            \Yii::getLogger()->log('测试发送弹幕显示消息IM消息失败 error===:'.$error,Logger::LEVEL_ERROR);
            return false;
        }
        return $rstData;
    }
}