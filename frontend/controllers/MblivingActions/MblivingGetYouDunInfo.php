<?php
namespace frontend\controllers\MblivingActions;

use common\components\SystemParamsUtil;
use frontend\business\ApproveUtil;
use frontend\business\ClientUtil;
use frontend\business\RewardUtil;
use frontend\business\SaveByTransUtil;
use frontend\business\SaveRecordByransactions\SaveByTransaction\AddUserFaceApproveSaveByTrans;
use frontend\business\SaveRecordByransactions\SaveByTransaction\AddYouDunWhiteListSaveByTrans;
use yii\base\Action;
use yii\log\Logger;

/**
 *接收有盾返回参数，回复响应
 * Class GetYouDunInfo
 * @package frontend\controllers\MbapiActions
 */
class MblivingGetYouDunInfo extends Action
{
    const securityKey = 'b2dcfc1d-71ea-4e6b-9551-143eeb3e1784';

    public function run()
    {
        $methodType = $_SERVER['REQUEST_METHOD'];
        if ($methodType == 'POST')
        {
            $post_data = file_get_contents('php://input');
        }
        else
        {
            $respData = array('code' => '0', 'message' => '非post方法提交');
            echo json_encode($respData);
            exit;
        }
        $data = json_decode($post_data, true);
        //\Yii::error('有盾回调:'.var_export($data,true));
        //TODO 获取商户开户的 SecurityKey 签名验证
        $signMD5= ApproveUtil::GetYouDunSignMD5($data,self::securityKey);
        if ($data['sign'] == $signMD5)
        {
            if($data['result_auth'] === 'T')   //TODO: T认证成功 F认证失败
            {
                //TODO 商户补充：使用数据的业务逻辑
                $User = ClientUtil::GetClientById($data['user_id']);
                $params = [
                    'client_id' => $data['user_id'],
                    'id_card' => $data['id_no'],
                    'actual_name' => $data['id_name'],
                    'result'=> $data['result_auth'],
                    'client_no'=> $User->client_no,
                ];
                $open = SystemParamsUtil::GetSystemParam('is_open_face',true,'value1');
                if($open == 1) {
                    $transActions[] = new AddYouDunWhiteListSaveByTrans($params);
                }else{
                    $transActions[] = new AddUserFaceApproveSaveByTrans($params);
                }

                if(!SaveByTransUtil::RewardSaveByTransaction($transActions, $error, $outInfo)) {
                    \Yii::error($error);
                }
            }
            $respData = array('code' => '1', 'message' => '通知接收成功');  //TODO: 收到商户异步通知;
        }
        else
        {
            \Yii::getLogger()->log('人脸识别签名信息  get_sign==:'.$data['sign'].'  my_sign==:'.$signMD5,Logger::LEVEL_ERROR);
            $respData = array('code' => '0', 'message' => '签名错误');   //TODO: 异步通知签名错误;

        }

        echo json_encode($respData);
    }
}