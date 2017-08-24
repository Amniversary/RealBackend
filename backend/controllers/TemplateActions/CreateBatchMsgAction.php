<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/8/23
 * Time: 下午3:46
 */

namespace backend\controllers\TemplateActions;


use backend\business\WeChatUserUtil;
use common\models\AttentionEvent;
use yii\base\Action;

class CreateBatchMsgAction extends Action
{
    public function run()
    {
        $this->controller->getView()->title  = '客服群发';
        $cache = WeChatUserUtil::getCacheInfo();
        $model = new AttentionEvent();
        $model->msg_type = 0;
        return $this->controller->render('send_user_msg',[
            'cache' => $cache,
            'model' => $model
        ]) ;

    }
}