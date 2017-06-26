<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/24
 * Time: 9:46
 */

namespace backend\controllers\AdvertiseActions;


use yii;
use common\models\Advertise;
use yii\base\Action;

class StatusAction extends Action
{
    public function run($id)
    {
        $rst =['message'=>'','output'=>''];
        if(empty($id)){
            $rst['message'] = 'id不能为空';
            echo json_encode($rst);
            exit;
        }

        $advertise =   Advertise::findOne(['id'=>$id]);
        if(!isset($advertise))
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
        $modifyData = \Yii::$app->request->post('Advertise');
        if(!isset($modifyData))
        {
            $rst['message'] = '没有Advertise模型对应的数据';
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
        $advertise->status = $status;
        if(!$advertise->save()){
            $rst['message'] = '更新错误';
            echo json_encode($rst);
            exit;
        }

        echo '0';
    }

}