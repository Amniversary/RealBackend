<?php

namespace backend\controllers\VersionManageActions;


use frontend\business\MultiVersionInfoUtil;
use yii\base\Action;

/**
 * 设置状态
 * Class CreateAction
 * @package backend\controllers\VersionManageActions
 */
class SetStatusAction extends Action
{
    public function run()
    {
        $data_status = \Yii::$app->request->post('data_status');
        $record_id = \Yii::$app->request->post('record_id');
        $forbid_words = \Yii::$app->request->post('forbid_words');
        $rst['code'] = 1;
        if(empty($record_id)){
            $rst['message'] = '版本ID不能为空';
            echo json_encode($rst);
            exit;
        }

        if(!in_array($data_status,[0,1])){
            $rst['message'] = '设置号不能为空';
            echo json_encode($rst);
            exit;
        }

        $versions = MultiVersionInfoUtil::GetVersionById($record_id);
        if(empty($versions->record_id)){
            $rst['message'] = '记录不存在';
            echo json_encode($rst);
            exit;
        }
        if($data_status == 0 && empty($forbid_words))
        {
            $rst['message'] = '禁用提示不能为空';
            echo json_encode($rst);
            exit;
        }

        $versions->status = $data_status;
        $versions->forbid_words = $forbid_words;
        if(!$versions->save())
        {
            $rst['message'] = '状态设置失败';
            echo json_encode($rst);
            exit;
        }
        \Yii::$app->cache->delete('app_version_info');
        $rst['code'] = 0;
        echo json_encode($rst);
        exit;
    }
}