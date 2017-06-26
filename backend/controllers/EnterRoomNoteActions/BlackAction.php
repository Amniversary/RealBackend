<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/1/27
 * Time: 11:21
 */

namespace backend\controllers\GiftActions;


use common\models\Gift;
use yii\base\Action;
use yii\log\Logger;

class BlackAction extends Action
{
    //拉黑
    public function run($gift_id)
    {
        $rst=['code'=>'1','msg'=>''];
        if(empty($gift_id))
        {
            $rst['msg']='礼物id不能为空';
            echo json_encode($rst);
            exit;
        }
        $Gift = Gift::findOne(['gift_id'=>$gift_id]);
        if(!isset($Gift))
        {
            $rst['msg']='礼物不存在';
            echo json_encode($rst);
            exit;
        }

        if($Gift->remark2 == '0'){
            $Gift->remark2 = '1';
        }else{
            $Gift->remark2 = '0';
        }

        if(!$Gift->save())
        {
            $rst['msg']='拉黑失败';
            \Yii::getLogger()->log('拉黑失败:'.var_export($Gift->getErrors(),true),Logger::LEVEL_ERROR);
            echo json_encode($rst);
            exit;
        }
        return $this->controller->redirect('/gift/index');
    }
}