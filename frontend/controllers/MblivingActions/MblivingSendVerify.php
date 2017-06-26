<?php
/**
 * 手机发送验证码
 * User: hlq
 * Date: 2016/5/3
 * Time: 14:58
 */
namespace frontend\controllers\MblivingActions;
use common\components\ValidateCodeUtil;
use frontend\business\ApproveUtil;
use yii\base\Action;

/**
 * 直播认证发送验证码
 * Class MblivingSendVerify
 * @package frontend\controllers\MblivingActions
 */
class MblivingSendVerify extends Action
{
    public function run()
    {
        $phone_num = \Yii::$app->request->post('phone_num'); //手机号
        if(!ApproveUtil::PregMatchPhoneNum($phone_num)){
            $arr_data = ['error_msg' => '手机号码不正确'];
            echo  json_encode($arr_data);
            exit;
        }
        if(!ValidateCodeUtil::SendValidate($phone_num,6,$error,$phone_num)){
            $arr_data = ['error_msg' => '发送失败'];
            echo  json_encode($arr_data);
            exit;
        }
        $arr_data = ['error_msg' => 'ok'];
        echo  json_encode($arr_data);
        exit;
    }
}




