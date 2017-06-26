<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/22
 * Time: 14:34
 */

namespace backend\controllers\LivingActions;


use common\models\Living;
use frontend\business\LivingHotUtil;
use frontend\business\LivingUtil;
use yii\base\Action;
use yii\swiftmailer\Logger;

class SetLimitNumAction extends Action
{
    public function run($living_id,$is_contract)
    {
        $rst =['message'=>'','output'=>''];
        if(empty($living_id))
        {
            $rst['message'] = '参数id不能为空';
            echo json_encode($rst);
            exit;
        }
        $living = LivingUtil::GetLivingById($living_id);
        if(!isset($living))
        {
            $rst['message'] = '直播记录不存在';
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
        $limit_num = \Yii::$app->request->post('limit_num');

        if($is_contract == '1')
        {
            $rst['message'] = '非签约主播不允许修改';
            echo json_encode($rst);
            exit;
        }

        if($living->living_type == 1)
        {
            $rst['message'] = '正常直播不允许修改';
            echo json_encode($rst);
            exit;
        }

        $living->limit_num = $limit_num;
        if(!LivingUtil::SaveLiving($living,$error))
        {
            $rst['message'] = $error;
            echo json_encode($rst);
            exit;
        }
        echo '0';
    }
}