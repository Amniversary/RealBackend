<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/17
 * Time: 17:39
 */
namespace backend\controllers\ScoreGiftActions;


use frontend\business\GiftUtil;
use yii\base\Action;
use yii\log\Logger;

class SetScoreAction extends Action
{
    public function run($record_id)
    {
        $rst =['message'=>'','output'=>''];
        if(empty($record_id))
        {
            $rst['message'] = '参数id不能为空';
            echo json_encode($rst);
            exit;
        }
        $score_gift = GiftUtil::GetGiftScoreById($record_id);
        if(!isset($score_gift))
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
        $score = \Yii::$app->request->post('score');

        $score_gift->score = $score;
        if(!GiftUtil::SaveGiftScore($score_gift,$error))
        {
            $rst['message'] = $error;
            echo json_encode($rst);
            exit;
        }
        \Yii::$app->cache->delete('get_score_gifts');

        echo '0';
    }
} 