<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/1/27
 * Time: 11:21
 */

namespace backend\controllers\EnterRoomNoteActions;


use common\models\EnterRoomNote;
use yii\base\Action;
use yii\log\Logger;

class DeleteAction extends Action
{
    public function run($record_id)
    {
        $rst=['code'=>'0','msg'=>''];
        if(empty($record_id))
        {
            $rst['msg']='欢迎词id不能为空';
            echo json_encode($rst);
            exit;
        }
        $EnterRoomNote = EnterRoomNote::findOne(['record_id'=>$record_id]);
        if(!isset($EnterRoomNote))
        {
            $rst['msg']='欢迎词不存在';
            echo json_encode($rst);
            exit;
        }

        if($EnterRoomNote->delete() === false)
        {
            $rst['msg']='删除失败';
            \Yii::getLogger()->log('删除失败:'.var_export($EnterRoomNote->getErrors(),true),Logger::LEVEL_ERROR);
            echo json_encode($rst);
            exit;
        }
        echo json_encode($rst);
        exit;
//        return $this->controller->redirect('/enterroomnote/index');
    }
}