<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/23
 * Time: 16:17
 */

namespace backend\controllers\FamilyActions;


use backend\business\UserUtil;
use yii\base\Action;

class SetStatusAction extends Action
{
    public function run($family_id)
    {
        $rst =['message'=>'','output'=>''];
        if(empty($family_id))
        {
            $rst['message'] = '用户id不能为空';
            echo json_encode($rst);
            exit;
        }
        $Family = UserUtil::GetFamilyById($family_id);
        if(!isset($Family))
        {
            $rst['message'] = '用户不存在';
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

        $editIndex = \Yii::$app->request->post('editableIndex');
        if(!isset($editIndex))
        {
            $rst['message'] = 'editableIndex参数为空';
            echo json_encode($rst);
            exit;
        }
        $modifyData = \Yii::$app->request->post('Family');
        if(!isset($modifyData))
        {
            $rst['message'] = '没有User模型对应的数据';
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
        $status = $dataItem['status'];
        $Family->status = $status;
        if(!UserUtil::SaveFamily($Family, $error))
        {
            $rst['message'] = $error;
            echo json_encode($rst);
            exit;
        }
        echo '0';
    }
} 