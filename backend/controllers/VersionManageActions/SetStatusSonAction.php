<?php

namespace backend\controllers\VersionManageActions;


use frontend\business\MultiUpdateContentUtil;
use frontend\business\MultiVersionInfoUtil;
use yii\base\Action;

/**
 * 设置子版本状态
 * Class CreateAction
 * @package backend\controllers\VersionManageActions
 */
class SetStatusSonAction extends Action
{
    public function run()
    {
        $set_type = \Yii::$app->request->get('settype');
        $update_id = \Yii::$app->request->get('update_id');
        $rst =['message'=>'','code'=>''];
        if(empty($update_id)){
            $rst['message'] = '子版本ID不能为空';
            echo json_encode($rst);
            exit;
        }

        if(!in_array($set_type,[0,1,2])){
            $rst['message'] = '设置号不能为空';
            echo json_encode($rst);
            exit;
        }


        $update = MultiUpdateContentUtil::GetUpdateContentById($update_id);
        if(!isset($update))
        {
            $rst['message'] = '用户记录不存在';
            echo json_encode($rst);
            exit;
        }

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
        $modifyData = \Yii::$app->request->post('MultiUpdateContent');
        if(!isset($modifyData))
        {
            $rst['message'] = '没有MultiUpdateContent模型对应的数据';
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

        if($set_type == 1){
            if(!isset($dataItem['force_update']))
            {
                $rst['message'] = '状态值为空';
                echo json_encode($rst);
                exit;
            }
            $force_update = $dataItem['force_update'];
            $update->force_update = $force_update;
        }else{
            if(!isset($dataItem['status']))
            {
                $rst['message'] = '状态值为空';
                echo json_encode($rst);
                exit;
            }
            $status = $dataItem['status'];
            $update->status = $status;
        }


        $error = '';
        $update->app_version_inner = (string)$update->app_version_inner;
        if(!MultiUpdateContentUtil::SaveMultiUpdateContent($update,$error))
        {
            $rst['message'] = $error;
            echo json_encode($rst);
            exit;
        }
        \Yii::$app->cache->delete('app_version_info');
        echo  '0';
        exit;
    }
}