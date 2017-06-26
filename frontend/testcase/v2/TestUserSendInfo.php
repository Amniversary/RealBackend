<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/17
 * Time: 17:25
 */
namespace frontend\testcase\v2;

use common\models\ChatRoom;
use common\models\Gift;
use Faker\Provider\Uuid;
use frontend\business\JobUtil;
use yii\log\Logger;

/**
 * 直播间左下角用户发送的消息
 * Class TestSendGift
 * @package frontend\testcase\v2
 */
class TestUserSendInfo
{
    public static function  SendUserInfo($data_params)
    {
        //显示消息
        $user_client = json_decode($data_params['info'],true);
        $client_info = TestGetClientInfo::GetClientInfo($data_params);
        $client_info = json_decode($client_info['info'],true);

        $info = [
            'aaaaaaaaaaaaaaaaaaaaaaa',
            'bbbbbbbbbbbbbbbbbbbbbbb',
            'ccccccccccccccccccccccc',
            '用户发送的消息',
            '这是测试消息',
        ];
        $msg = $info[intval(rand(0,count($info)-1))];
        $test_send_gift_params = [
            'key_word' => 'test_im',
            'user_id' => $user_client['data']['user_id'],
            'type' => '500',
            'msg' => strval($msg),
            'isAdministrator' => '0',
            'userinfo' => $client_info['data'],
            'other_id' => $data_params['other_id']
        ];
        if(!JobUtil::AddCustomJob('ImBeanstalk','tencent_im',$test_send_gift_params,$error))
        {
            \Yii::getLogger()->log('测试直播间左下角用户发送的IM消息失败 error===:'.$error,Logger::LEVEL_ERROR);
            return false;
        }

        return true;
    }
}