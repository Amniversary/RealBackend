<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/1/27
 * Time: 15:10
 */

namespace backend\controllers\AdvertisingManageActions;



use common\models\AdImages;
use yii\base\Action;

class SetStatusAction extends Action
{
    public function run($ad_id)
    {
        $rst =['message'=>'','output'=>''];
        if(empty($ad_id))
        {
            $rst['message'] = '弹窗广告图id不能为空';
            echo json_encode($rst);
            exit;
            //ExitUtil::ExitWithMessage('用户id不能为空');
        }
        $adImages = AdImages::findOne(['ad_id' => $ad_id]);
        if(!isset($adImages))
        {
            //ExitUtil::ExitWithMessage('用户不存在');
            $rst['message'] = '弹窗广告图记录不存在';
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
        $modifyData = \Yii::$app->request->post('AdImages');
        if(!isset($modifyData))
        {
            $rst['message'] = '没有AdImages模型对应的数据';
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
        $adImages->status = $status;
        if(!$adImages->save())
        {
            $rst['message'] = var_export($adImages->getErrors(),true);
            echo json_encode($rst);
            exit;
        }
        echo '0';
    }
} 