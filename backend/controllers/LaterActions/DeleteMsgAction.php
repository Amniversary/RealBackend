<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/7/3
 * Time: 上午9:44
 */

namespace backend\controllers\LaterActions;


use common\models\AttentionEvent;
use common\models\KeywordParams;
use common\models\LaterImage;
use common\models\Resource;
use common\models\SignImage;
use yii\base\Action;
use yii\db\Exception;

class DeleteMsgAction extends Action
{
    public function run($record_id)
    {

        $rst = ['code'=>'1', 'msg'=>''];
        if(empty($record_id)){
            $rst['msg'] = '记录Id不能为空';
            echo json_encode($rst);
            exit;
        }

        $msgData = LaterImage::findOne(['id'=>$record_id]);
        if(!isset($msgData)){
            $rst['msg'] = '操作记录不存在或已经删除';
            echo json_encode($rst);
            exit;
        }
        if($msgData->delete() === false) {
            $rst['msg']='删除失败';
            \Yii::error('删除失败:'.var_export($msgData->getErrors(),true));
            echo json_encode($rst);
            exit;
        }
        $rst['code'] = '0';
        echo json_encode($rst);
        exit;
    }
}