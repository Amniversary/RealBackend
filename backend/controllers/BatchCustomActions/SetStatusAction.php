<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/7/28
 * Time: 下午5:42
 */

namespace backend\controllers\BatchCustomActions;


use common\models\SystemMenu;
use yii\base\Action;

class SetStatusAction extends Action
{
    public function run($id)
    {
        $rst =['message'=>'','output'=>''];
        if(empty($id)) {
            $rst['message'] = '配置id 不能为空';
            echo json_encode($rst);
            exit;
        }
        $data = SystemMenu::findOne(['id'=>$id]);
        if(!isset($data)) {
            $rst['message'] = '配置记录不存在';
            echo json_encode($rst);
            exit;
        }
        $hasEdit = \Yii::$app->request->post('hasEditable');
        if(!isset($hasEdit)) {
            $rst['message'] = 'hasEditable参数为空';
            echo json_encode($rst);
            exit;
        }
        $editIndex = \Yii::$app->request->post('editableIndex');
        if(!isset($editIndex)) {
            $rst['message'] = 'editableIndex参数为空';
            echo json_encode($rst);
            exit;
        }
        $modifyData = \Yii::$app->request->post('SystemMenu');
        if(!isset($modifyData)) {
            $rst['message'] = '没有User模型对应的数据';
            echo json_encode($rst);
            exit;
        }

        if(!isset($modifyData[$editIndex])) {
            $rst['message'] = '对应的列下没有数据';
            echo json_encode($rst);
            exit;
        }
        $dataItem = $modifyData[$editIndex];
        if(!isset($dataItem['status'])) {
            $rst['message'] = '状态值为空';
            echo json_encode($rst);
            exit;
        }
        $status = $dataItem['status'];

        $data->status = $status;
        if(!($data instanceof SystemMenu))
        {
            $rst['message'] = '不是配置记录对象';
            echo  json_encode($rst);
            exit;
        }
        if(!$data->save()){
            $rst['message'] = '保存配置记录失败';
            \Yii::error($rst['message']. ' :'. var_export($data->getErrors(),true));
            echo json_encode($rst);
            exit;
        }
        echo '0';
    }
}