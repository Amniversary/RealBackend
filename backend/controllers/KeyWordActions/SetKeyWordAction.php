<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/8/17
 * Time: 下午4:09
 */

namespace backend\controllers\KeyWordActions;


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
        $rst = ['code' => '1', 'msg' => ''];
        $cache = WeChatUserUtil::getCacheInfo();
        if(empty($msg_id)) {
            $rst['msg'] = '消息id 不能为空';
            echo json_encode($rst);exit;
        }
        $msg = AttentionEvent::findOne(['record_id'=>$msg_id]);
        if(!isset($msg)){
            $rst['msg'] = '消息记录不存在';
            echo json_encode($rst);exit;
        }
        $params = \Yii::$app->request->post('title');
        if(isset($params))
        {
            $error = '';
            if(!KeywordUtil::SaveKeyWordParams($params,$cache['record_id'],$msg_id,$error)) {
                $rst['msg'] = $error;
                echo json_encode($rst);
                exit;
            }
            $rst['code'] = '0';
            echo json_encode($rst);
            exit;
        }else{
            (new KeywordParams())->deleteAll(['app_id'=>$cache['record_id'], 'msg_id'=>$msg_id]);//TODO: 删除用户原有权限数据
            $rst['code'] = '0';
            echo json_encode($rst);
            exit;
        }
    }
}