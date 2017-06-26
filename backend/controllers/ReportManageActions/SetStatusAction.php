<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/1/27
 * Time: 15:10
 */

namespace backend\controllers\ReportManageActions;



use frontend\business\CarouselUtil;
use frontend\business\HotWordsUtil;
use frontend\business\ReportUtil;
use yii\base\Action;

class SetStatusAction extends Action
{
    public function run($my_report_id)
    {
        $rst =['message'=>'','output'=>''];
        if(empty($my_report_id))
        {
            $rst['message'] = '举报id不能为空';
            echo json_encode($rst);
            exit;
            //ExitUtil::ExitWithMessage('用户id不能为空');
        }
        $report = ReportUtil::GetReportById($my_report_id);
        if(!isset($report))
        {
            //ExitUtil::ExitWithMessage('用户不存在');
            $rst['message'] = '热词记录不存在';
            echo json_encode($rst);
            exit;
        }
        /*
hasEditable:1
editableIndex:0
editableKey:1
User[0][status]:0
         */
        $hasEdit = \Yii::$app->request->post('hasEditable');
        if(!isset($hasEdit))
        {
            $rst['message'] = 'hasEditable参数为空';
            echo json_encode($rst);
            exit;
        }
        if(empty($hasEdit))
        {
            $rst['message'] = '';
            echo json_encode($rst);
            exit;
        }
        $editIndex = \Yii::$app->request->post('editableIndex');
        if(!isset($editIndex))
        {
            $rst['message'] = 'editableIndex参数为空';
            echo json_encode($rst);
            exit;
        }
        $modifyData = \Yii::$app->request->post('ReportList');
        if(!isset($modifyData))
        {
            $rst['message'] = '没有ReportList模型对应的数据';
            echo json_encode($rst);
            exit;
        }

        if(!isset($modifyData[$editIndex]))
        {
            $rst['message'] = '对应的列下没有数据';
            echo json_encode($rst);
            exit;
        }
        $dataItem = $modifyData[$editIndex];
        if(!isset($dataItem['status']))
        {
            $rst['message'] = '状态值为空';
            echo json_encode($rst);
            exit;
        }
        if($report->status === 2)
        {
            $rst['message'] = '已经处理过';
            echo json_encode($rst);
            exit;
        }
        $status = $dataItem['status'];
        $report->status = $status;
        $report->check_time=date('Y-m-d H:i:s');
        $report->remark1 = strval(\Yii::$app->user->id);
        $error='';
        if(!ReportUtil::CheckReport($report,$error))
        {
            $rst['message'] = $error;
            echo json_encode($rst);
            exit;
        }
        echo '0';
    }
} 