<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/1/27
 * Time: 15:10
 */

namespace backend\controllers\UpdateManageActions;

use backend\business\SystemParamsUtil;
use yii\base\Action;

class SetValue2Action extends Action
{
    public function run($params_id)
    {
        $rst =['message'=>'','output'=>''];
        if(empty($params_id))
        {
            $rst['message'] = '参数id不能为空';
            echo json_encode($rst);
            exit;
        }
        $params = SystemParamsUtil::GetSystemParamsById($params_id);
        if(!isset($params))
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
        $modifyData = \Yii::$app->request->post('SystemParams');
        if(!isset($modifyData))
        {
            $rst['message'] = '没有SystemParams模型对应的数据';
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
        if(!isset($dataItem['value2']))
        {
            $rst['message'] = '参数类型值为空';
            echo json_encode($rst);
            exit;
        }
        $value2 = $dataItem['value2'];
        $params->value2 = $value2;
        if(!SystemParamsUtil::SaveGoods($params,$error))
        {
            $rst['message'] = $error;
            echo json_encode($rst);
            exit;
        }
        //清除缓存记录

        if($params->code == 'set_version_agreement'){
            \Yii::$app->cache->delete('get_api_version');
        }
        echo '0';
    }
} 