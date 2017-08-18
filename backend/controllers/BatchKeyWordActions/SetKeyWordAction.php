<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/8/17
 * Time: 下午4:09
 */

namespace backend\controllers\BatchKeyWordActions;


use backend\business\KeywordUtil;
use backend\business\WeChatUserUtil;
use backend\components\ExitUtil;
use common\models\AttentionEvent;
use common\models\KeywordParams;
use yii\base\Action;

class SetKeyWordAction extends Action
{
    public function run($msg_id)
    {
        if(empty($msg_id)) {
            ExitUtil::ExitWithMessage('消息id不能为空');
        }
        $msg = AttentionEvent::findOne(['record_id'=>$msg_id]);
        if(!isset($msg)){
            ExitUtil::ExitWithMessage('消息记录不存在');
        }
        $params = \Yii::$app->request->post('title');
        if(isset($params))
        {
            $rst = ['code' => '1', 'msg' => ''];
            $error = '';
            if(!KeywordUtil::SaveGlobalKeyWordParams($params,$msg_id,$error)) {
                $rst['msg'] = $error;
                echo json_encode($rst);
                exit;
            }
            $rst['code'] = '0';
            echo json_encode($rst);
            exit;
        }else{
            (new KeywordParams())->deleteAll(['msg_id'=>$msg_id]);//TODO: 删除用户原有权限数据
            $rst['code'] = '0';
            echo json_encode($rst);
            exit;
        }
    }
}