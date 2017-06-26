<?php

namespace backend\controllers\ApproveBusinessCheckActions;


use backend\components\ExitUtil;
use frontend\business\ApproveUtil;
use yii\base\Action;

/**
 * 审核详情
 * Class CreateAction
 * @package backend\controllers\UpdateAction
 */
class DetailAction extends Action
{
    public function run($approve_id,$date_type)
    {
        $status = 0;
        if($date_type == 'audited'){
            $status = 1;
        }
        $model = ApproveUtil::GetApproveBusinessCheckById($approve_id,$status);
        if(!isset($model))
        {
            ExitUtil::ExitWithMessage('记录不存在');
        }
        $model['status'] = ($model['status']==0?'未审核':($model['check_result_status']==0?'已拒绝':'已审核'));
        $this->controller->layout='main_empty';
        if($date_type == 'audited'){
            return $this->controller->render('detailaudited', [
                'model' => $model,
            ]);
        }else{
            return $this->controller->render('detail', [
                'model' => $model,
            ]);
        }

    }
}