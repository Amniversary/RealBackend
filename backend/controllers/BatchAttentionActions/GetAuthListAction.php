<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/7/28
 * Time: 上午11:33
 */

namespace backend\controllers\BatchAttentionActions;


use backend\business\KeywordUtil;
use backend\components\ExitUtil;
use common\models\AttentionEvent;
use yii\base\Action;

class GetAuthListAction extends Action
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
        $selection = KeywordUtil::GetAttentionAuthById($msg_id);//TODO:公众号已有配置
        $rights = KeywordUtil::GetAuthParams();//TODO: 配置列表
        $params_one = KeywordUtil::GetAuthParamsByApprove();
        $params_two = KeywordUtil::GetAuthParamsNotApprove();
        $this->controller->layout='main_empty';
        return $this->controller->render('setauthlist',[
            'msgData'=>$msgData,
            'rights'=>$rights,
            'selections' =>$selection,
            'params_one' =>$params_one,
            'params_two' => $params_two,
        ]);
    }
}