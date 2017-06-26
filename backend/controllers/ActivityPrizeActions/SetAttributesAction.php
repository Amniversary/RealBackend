<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/1/27
 * Time: 15:10
 */

namespace backend\controllers\ActivityPrizeActions;



use common\models\ActivityPrize;
use yii\base\Action;
use yii\log\Logger;

/**
 * 设置值
 * Class SetAttributesAction
 * @package backend\controllers\ClientActions
 */
class SetAttributesAction extends Action
{
    public function run($prize_id,$field)
    {
        $rst =['message'=>'','output'=>''];
        if(empty($prize_id))
        {
            $rst['message'] = 'id不存在';
            echo json_encode($rst);
            exit;
        }
        if(empty($field))
        {
            $rst['message'] = '操作字段不存在';
            echo json_encode($rst);
            exit;
        }
        $fields = ['number','rate'];
        if(!in_array($field,$fields))
        {
            $rst['message'] = '字段不存在';
            echo json_encode($rst);
            exit;
        }
        $activity_prize = ActivityPrize::findOne(['prize_id' => strval($prize_id)]);
        if(!isset($activity_prize))
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
        $modifyData = \Yii::$app->request->post('ActivityPrize');
        if(!isset($modifyData))
        {
            $rst['message'] = '没有ActivityPrize模型对应的数据';
            echo json_encode($rst);
            exit;
        }
        if(!isset($modifyData[$editIndex]))
        {
            $rst['message'] = '对应的列下没有数据';
            echo json_encode($rst);
            exit;
        }
        $activity_prize->$field = $modifyData[$editIndex][$field];
        if(!$activity_prize->save())
        {
            $rst['message'] = '修改失败';
            echo json_encode($rst);
            exit;
        }
/*        $key = 'client_forbid_flag_'.$prize_id;
        if($status === '1')
        {
            //启用
            \Yii::$app->cache->delete($key);
        }
        else
        {
            //禁用
            \Yii::$app->cache->set($key,'1');
        }*/
        echo '0';
    }
} 