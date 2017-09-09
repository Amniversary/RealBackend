<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/9/8
 * Time: 上午9:32
 */

namespace backend\controllers\TemplateActions;


use backend\business\KeywordUtil;
use backend\components\ExitUtil;
use common\models\BatchCustomer;
use yii\base\Action;

class GetAuthAction extends Action
{
    public function run($id)
    {
        if(empty($id)) {
            ExitUtil::ExitWithMessage('任务id , 不能为空');
        }
        $task = BatchCustomer::findOne(['id'=> $id]);
        if(empty($task) || !isset($task)) {
            ExitUtil::ExitWithMessage('找不到对应记录信息');
        }
        $selections = json_decode($task['app_list'],true);
        if(empty($selections)) $selections = [];
        $rights = KeywordUtil::GetAuthParamsByApprove();
        $this->controller->layout = 'main_empty';
        return $this->controller->render('get_auth', [
            'selections' => $selections,
            'rights' => $rights,
            'task' => $task,
        ]);
    }
}