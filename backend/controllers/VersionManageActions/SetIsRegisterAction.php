<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/12
 * Time: 10:53
 */

namespace backend\controllers\VersionManageActions;


use frontend\business\MultiUpdateContentUtil;
use yii\base\Action;

class SetIsRegisterAction extends Action
{
    public function run($update_id)
    {
        $rst =['message'=>'','output'=>''];
        if(empty($update_id))
        {
            $rst['message'] = '版本id不能为空';
            echo json_encode($rst);
            exit;
        }

        $version = MultiUpdateContentUtil::GetUpdateContentById($update_id);
        if(!isset($version))
        {
            $rst['message'] = '记录不存在';
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
        if(!isset($dataItem['is_register']))
        {
            $rst['message'] = '状态值为空';
            echo json_encode($rst);
            exit;
        }
        $is_register = $dataItem['is_register'];
        $version->is_register = $is_register;
        if(!MultiUpdateContentUtil::SaveMultiUpdateContent($version,$error))
        {
            $rst['message'] = $error;
            echo json_encode($rst);
            exit;
        }
        \Yii::$app->cache->delete('app_version_info');
        echo '0';
    }
} 