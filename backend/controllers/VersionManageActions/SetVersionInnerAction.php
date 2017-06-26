<?php

namespace backend\controllers\VersionManageActions;


use frontend\business\MultiUpdateContentUtil;
use yii\base\Action;
use yii\log\Logger;

/**
 * 设置内部版本号
 * Class CreateAction
 * @package backend\controllers\VersionManageActions
 */
class SetVersionInnerAction extends Action
{
    public function run()
    {
        $update_id = \Yii::$app->request->get('update_id');
        $rst =['message'=>'','code'=>''];
        if(empty($update_id)){
            $rst['message'] = '子版本ID不能为空';
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

        $app_version_inner = \Yii::$app->request->post('MultiUpdateContent')[$editIndex]['app_version_inner'];
        if(empty($app_version_inner)){
            $rst['message'] = '设置内部版本号不能为空';
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

        $error = '';

        $update->app_version_inner = (string)$app_version_inner;
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