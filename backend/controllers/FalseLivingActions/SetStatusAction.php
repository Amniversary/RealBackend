<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/1/27
 * Time: 15:10
 */

namespace backend\controllers\FalseLivingActions;

use backend\business\GoodsUtil;
use common\models\Living;
use yii\base\Action;

class SetStatusAction extends Action
{
    public function run($living_id)
    {
        $rst =['message'=>'','output'=>''];
        if(empty($living_id))
        {
            $rst['message'] = '商品id不能为空';
            echo json_encode($rst);
            exit;
        }
        $living = Living::findOne(['living_id' => $living_id]);
        if(!isset($living))
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
        $status= \Yii::$app->request->post('status');
        if(0)
        {
            $rst['message'] = '参数为空';
            echo json_encode($rst);
            exit;
        }

        $living->status = $status;
        if(!$living->save())
        {
            $rst['message'] = '修改失败';
            echo json_encode($rst);
            exit;
        }
        echo '0';
    }
} 