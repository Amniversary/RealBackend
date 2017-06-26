<?php

namespace backend\controllers\CheckReportActions;


use backend\business\BackendBusinessCheckUtil;
use backend\business\UserUtil;
use backend\components\ExitUtil;
use common\models\User;
use frontend\business\BusinessCheckUtil;
use frontend\business\ClientUtil;
use frontend\business\ReportUtil;
use frontend\business\TicketToCashUtil;
use yii\base\Action;

/**
 * 举报审核详情
 * Class CreateAction
 * @package backend\controllers\UpdateAction
 */
class DetailAction extends Action
{
    public function run($report_id,$date_type)
    {
//        echo \Yii::$app->user->id;exit;
        $model = ReportUtil::GetReportId($report_id);
        if(!isset($model))
        {
            ExitUtil::ExitWithMessage('记录不存在');
        }
        $model['scene'] = ($model['scene'] == '2')?'群':(($model['scene'] == '3')?'好友':'其他');
        if($model['status'] == 2){
            $model['status'] = '已审核';
            $user_info = UserUtil::GetUserByUserId($model['remark4']);
            $model['remark4'] =  $user_info->username;
        }else{
            $model['status'] = '未审核';
        }
        $type = '';
        switch($model['report_type']){
            case 1:
                $type = '欺诈';
                break;
            case 2:
                $type = '色情';
                break;
            case 3:
                $type = '政治谣言';
                break;
            case 4:
                $type = '常识性谣言';
                break;
            case 5:
                $type = '恶意营销';
                break;
            case 6:
                $type = '其他侵权';
                break;
        }
        $model['report_type'] = $type;


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