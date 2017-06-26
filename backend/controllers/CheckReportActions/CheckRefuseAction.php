<?php

namespace backend\controllers\CheckReportActions;


use frontend\business\ReportUtil;
use frontend\business\RewardUtil;
use frontend\business\TicketToCashUtil;
use yii\base\Action;

/**
 * 审核拒绝、通过
 * Class CreateAction
 * @package backend\controllers\UpdateAction
 */
class CheckRefuseAction extends Action
{
    public function run()
    {
        $error = '';
        $report_id = \Yii::$app->request->post('check_id');
        $remark3 = \Yii::$app->request->post('remark3');
        $report = ReportUtil::GetReportId($report_id);
        if(empty($report)){
            $rst['msg']='记录不存在';
            echo json_encode($rst);
            exit;
        }
        if(empty($remark3)){
            $rst['msg']='审核备注不能为空';
            echo json_encode($rst);
            exit;
        }

        if($report->status == 2){
            $rst['msg']='记录已经处理过了';
            echo json_encode($rst);
            exit;
        }
        if(!ReportUtil::UpdateReport($report_id,$remark3,$error)){
            $rst['msg'] = $error;
            return false;
        }

        $rst['code']='0';
        echo json_encode($rst);

    }
}