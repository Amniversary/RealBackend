<?php

namespace backend\controllers\CheckMoneyGoodsActions;


use backend\business\BackendBusinessCheckUtil;
use backend\components\ExitUtil;
use frontend\business\TicketToCashUtil;
use yii\base\Action;

/**
 * 审核通过
 * Class CreateAction
 * @package backend\controllers\UpdateAction
 */
class CheckPassAction extends Action
{
    public function run()
    {
        $error = '';
        $record_id = \Yii::$app->request->post('check_id');
        if(!TicketToCashUtil::CheckBaseInfo($record_id,$outinfo,$error)){
            $rst['msg']=$error;
            echo json_encode($rst);
            exit;
        }

        if(in_array($outinfo['status'],[2,3,4]))
        {
            $rst['msg']='该记录已经审核过了';
            echo json_encode($rst);
            exit;
        }
        $params = [
            'record_id'=>$record_id,
            'backend_user_id'=>\Yii::$app->user->id,
        ];
        if(!TicketToCashUtil::CheckPass($params,$error))
        {
            $rst['msg'] = $error;
            echo json_encode($rst);
            exit;
        }
        $rst['code']='0';
        echo json_encode($rst);


























    }
}