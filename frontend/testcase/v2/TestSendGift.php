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
 * 送礼物
 * Class TestSendGift
 * @package frontend\testcase\v2
 */
class TestSendGift
{
    public static function  SendGift($data_params)
    {
        $randSQL = "SELECT *  FROM mb_gift WHERE remark2=0 and gift_id!=22  ORDER BY RAND() LIMIT 1";
        $gift_model = Gift::findBySql($randSQL)->one();
        $data = [
            "unique_no" => $data_params['unique_no'],
            "register_type" => $data_params['register_type'],
            "money_type" => $data_params['money_type'],
            "gift_id" => $gift_model['gift_id'],
            "living_id" => $data_params['living_id'],
            "op_unique_no" => Uuid::uuid()
        ];
        $rstData = Common::PackagingParams($data_params['token'],$data_params['sign_key'],$data_params['crypt_key'],'send_gift',$data,$data_params['device_no'],
            $data_params['device_type'],'json');

        //显示消息
        $user_client = json_decode($data_params['info'],true);
        $client_info = TestGetClientInfo::GetClientInfo($data_params);
        $client_info = json_decode($client_info['info'],true);

        $test_send_gift_params = [
            'key_word' => 'test_im',
            'user_id' => $user_client['data']['user_id'],
            'type' => '505',
            'msg' => strval($gift_model['gift_id']),
            'isAdministrator' => '1',
            'userinfo' => $client_info['data'],
            'other_id' => $data_params['other_id']
        ];
        \Yii::getLogger()->log('$test_send_gift_params_error===:'.var_export($test_send_gift_params,true),Logger::LEVEL_ERROR);
        if(!JobUtil::AddCustomJob('ImBeanstalk','tencent_im',$test_send_gift_params,$error))
        {
            \Yii::getLogger()->log('测试发送礼物显示消息IM消息失败 error===:'.$error,Logger::LEVEL_ERROR);
            return false;
        }

        return $rstData;
    }
}