<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/6/21
 * Time: 13:04
 */

namespace backend\controllers\LuckyGiftActions;

use frontend\business\LuckyGiftUtil;
use yii\base\Action;
use yii\log\Logger;

class SetStatusAction extends Action
{
    public function run($lucky_id)
    {
        $rst =['message'=>'','output'=>''];
        if(empty($lucky_id))
        {
            $rst['message'] = 'id不能为空';
            echo json_encode($rst);
            exit;
        }

        $luckygift = LuckyGiftUtil::GetLuckyGiftById($lucky_id);
        if(!isset($luckygift))
        {
            $rst['message'] = '信息不存在';
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

        $status = \Yii::$app->request->post('LuckygiftParams')[$editIndex]['status'];
        if(!isset($status))
        {
            $rst['message'] = '状态参数为空';
            echo json_encode($rst);
            exit;
        }

        $luckygift->status = intval($status);
        if(!LuckyGiftUtil::SetStatus($luckygift,$error))
        {
            $rst['message'] = $error;
            echo json_encode($rst);
            exit;
        }
        LuckyGiftUtil::DeleteLuckyGiftCache();  //删除缓存
        echo '0';
    }
} 