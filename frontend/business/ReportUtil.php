<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/1/8
 * Time: 16:38
 */

namespace frontend\business;


use common\models\Report;
use common\models\ReportList;
use frontend\business\SaveRecordByransactions\SaveByTransaction\ReportInfoSaveByTrans;
use frontend\business\SaveRecordByransactions\SaveByTransaction\UserActiveSaveByTrans;
use yii\log\Logger;

class ReportUtil
{
    public static function CheckReport($report,&$error)
    {
        if(!($report instanceof ReportList))
        {
            $error = '不是举报记录';
            return false;
        }
        if(!$report->save())
        {
            $error = '举报记录保存失败';
            \Yii::getLogger()->log($error.' :'.var_export($report->getErrors(),true),Logger::LEVEL_ERROR);
            return false;
        }
        return true;
    }

    public static function GetReportById($my_report_id)
    {
        return ReportList::findOne(['my_report_id'=>$my_report_id]);
    }

    /**
     * 通过ID得到举报表信息
     * @param $report_id
     * @return null|static
     */
    public static function GetReportId($report_id){
        return Report::findOne('report_id='.$report_id);
    }

    public static function UpdateReport($report_id,$remark3,&$error){
        $reposrt_info = self::GetReportId($report_id);
        $reposrt_info->remark3 = $remark3;
        $reposrt_info->remark4 = (string)\Yii::$app->user->id;
        $reposrt_info->check_time = date('Y-m-d H:i:s');
        $reposrt_info->status = 2;
        if(!$reposrt_info->save()){
            $error = '举报审核失败';
            return false;
        }
        return true;
    }




    /**
     * 获取举报模型
     * @param $params
     * @param $userInfo
     * @param $scene = 1
     * @return ReportList
     */
    public static function GetReportNewModel($userInfo,$params,$scene=1)
    {
        $model = new Report();
        if(empty($params['scene']))
        {
            $model->scene = $scene;
        }
        $user = ClientUtil::GetClientById($userInfo['user_id']);
        $model->user_id = $user->client_id;
        $model->nick_name = $user->nick_name;
        $model->client_no = $user->client_no;
        if(!empty($params['scene']))
        {
            $scene = $params['scene'];
        }
        $report_id = $params['report_id'];
        $report_user = ClientUtil::GetClientById($report_id);
        $model->report_client_no = $report_user->client_no;
        switch(intval($scene))
        {
            case 1:
                $livingInfo = LivingUtil::GetLivingUserInfo($report_id);
                $report_user_info = ClientUtil::GetClientById($report_id);
                $model->living_id = intval($livingInfo->living_id);
                $model->report_type = intval($params['report_type']);
                $model->report_user_id = intval($report_id);
                $model->report_user_name = $report_user_info->nick_name;
                $model->report_content = $params['report_content'];
                $model->remark1 = $report_user_info->phone_no;
                break;

            case 2:
                $report_user_info = ClientUtil::GetClientById($report_id);
                $model->report_user_id = intval($report_id);
                $model->report_user_name = $report_user_info->nick_name;
                $model->report_type = intval($params['report_type']);
                $model->report_content = $params['report_content'];
                $model->remark1 = $report_user_info->phone_no;
                break;
        }
        $model->scene = intval($scene);
        $model->create_time = date('Y-m-d H:i:s');
        $model->status = 1;
        $model->remark2 = $user->phone_no;//举报人电话
        return $model;
    }

    /**
     * 保存举报信息
     * @param $model
     * @param $error
     * @return bool
     */
    public static function SaveReport($model, &$error)
    {

        $transActions = [];
        $transActions[] = new ReportInfoSaveByTrans($model);
        //$transActions[] = new UserActiveSaveByTrans($userActive,['modify_type'=>'report']);
        if(!RewardUtil::RewardSaveByTransaction($transActions,$out, $error))
        {
            return false;
        }
        return true;
    }
} 