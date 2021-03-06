<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/7/17
 * Time: 上午2:06
 */

namespace backend\controllers\BatchAttentionActions;


use backend\business\KeywordUtil;
use backend\components\ExitUtil;
use common\models\AttentionEvent;
use common\models\BatchAttention;
use common\models\Keywords;
use yii\base\Action;

class SetAuthListAction extends Action
{
    public function run($msg_id)
    {
        if(empty($msg_id)) {
            ExitUtil::ExitWithMessage('消息id不能为空');
        }
        $msgData = AttentionEvent::findOne(['record_id'=>$msg_id]);
        if(!isset($msgData)){
            ExitUtil::ExitWithMessage('消息记录不存在');
        }
        $params = \Yii::$app->request->post('title');
        if(isset($params))
        {
            $rst = ['code' => '1', 'msg' => ''];
            $error = '';
            if(!KeywordUtil::SaveAttentionAuthParams($params,$msg_id,$error)) {
                $rst['msg'] = $error;
                echo json_encode($rst);
                exit;
            }
            $rst['code'] = '0';
            echo json_encode($rst);
            exit;
        } else {
            BatchAttention::deleteAll(['msg_id'=>$msg_id]);
            $rst['code'] = '0';
            echo json_encode($rst);
            exit;
        }

    }
}