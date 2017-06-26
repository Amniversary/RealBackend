<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/28
 * Time: 9:44
 */

namespace backend\controllers\ActivityPrizeActions;


use frontend\business\ActivityUtil;
use yii\base\Action;

class SetPrizeRecordAction extends Action
{
    public function run($record_id)
    {
        $rst =['code'=>'1','msg'=>''];
        if(empty($record_id))
        {
            $rst['msg'] = '中奖记录id不能为空';
            echo json_encode($rst);
            exit;
        }
        $prize_record = ActivityUtil::GetPrizeRecordById($record_id);
        if(!isset($prize_record))
        {
            $rst['msg'] = '中奖记录不存在';
            echo json_encode($rst);
            exit;
        }

        $hasEdit = \Yii::$app->request->post('hasEditable');
        if(!isset($hasEdit))
        {
            $rst['msg'] = 'hasEditable参数为空';
            echo json_encode($rst);
            exit;
        }
        if(empty($hasEdit))
        {
            $rst['msg'] = '';
            echo json_encode($rst);
            exit;
        }
        $editIndex = \Yii::$app->request->post('editableIndex');
        if(!isset($editIndex))
        {
            $rst['msg'] = 'editableIndex参数为空';
            echo json_encode($rst);
            exit;
        }
        $attributes = \Yii::$app->request->post('editableAttribute');
        if(!isset($attributes))
        {
            $rst['msg'] = '模型对应字段的数据为空';
            echo json_encode($rst);
            exit;
        }

        $value = \Yii::$app->request->post($attributes);
        if(!isset($value))
        {
            $rst['msg'] = '对应的列下没有数据';
            echo json_encode($rst);
            exit;
        }

        $prize_record->$attributes = $value;
        if(!ActivityUtil::SavePrizeRecord($prize_record,$error))
        {
            $rst['msg'] = $error;
            echo json_encode($rst);
            exit;
        }

        $rst['code'] = '0';
        echo json_encode($rst);
    }
} 