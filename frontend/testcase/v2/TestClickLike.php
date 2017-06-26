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
use yii\log\Logger;

/**
 * 点赞
 * Class TestClickLike
 * @package frontend\testcase\v2
 */
class TestClickLike
{
    public static  function ClickLike($data_params)
    {
        $data = [
            "unique_no" => $data_params['unique_no'],
            "register_type" => $data_params['register_type'],
            "living_id" => $data_params['living_id']
        ];
        $rstData = Common::PackagingParams($data_params['token'],$data_params['sign_key'],$data_params['crypt_key'],'click_like',$data,$data_params['device_no'],
            $data_params['device_type'],'json',$data_params['unique_no']);

        //显示消息
        $user_client = json_decode($data_params['info'],true);
        $client_info = TestGetClientInfo::GetClientInfo($data_params);
        $client_info = json_decode($client_info['info'],true);
        $test_click_like_params = [
            'key_word' => 'test_im',
            'user_id' => $user_client['data']['user_id'],
            'type' => '503',
            'msg' => '0',
            'isAdministrator' => '1',
            'userinfo' => $client_info['data'],
            'other_id' => $data_params['other_id']
        ];
        \Yii::getLogger()->log('$test_click_like_params===:'.var_export($test_click_like_params,true),Logger::LEVEL_ERROR);
        if(!JobUtil::AddCustomJob('ImBeanstalk','tencent_im',$test_click_like_params,$error))
        {
            \Yii::getLogger()->log('测试发送点赞显示消息IM消息失败 error===:'.$error,Logger::LEVEL_ERROR);
            \Yii::getLogger()->flush(true);
            return false;
        }

        return $rstData;
    }
}