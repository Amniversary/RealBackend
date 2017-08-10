<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/8/9
 * Time: 下午4:17
 */

namespace backend\controllers\TemplateActions;


use backend\components\ExitUtil;
use common\models\Client;
use yii\base\Action;

class MsgTemplateAction extends Action
{
    public function run($user_id)
    {
        if(!isset($user_id) || empty($user_id)) {
            ExitUtil::ExitWithMessage('用户Id不能为空');
        }
        $User = Client::findOne(['client_id'=>$user_id]);
        return $this->controller->render('msg_template',[
            'User'=>$User,
        ]);
    }
}