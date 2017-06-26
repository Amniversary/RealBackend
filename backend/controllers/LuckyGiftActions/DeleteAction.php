<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/1/27
 * Time: 11:21
 */

namespace backend\controllers\LuckyGiftActions;


use common\models\LuckygiftParams;
use frontend\business\LuckyGiftUtil;
use yii\base\Action;
use yii\log\Logger;

class DeleteAction extends Action
{
    public function run($lucky_id)
    {
        $rst=['code'=>'0','msg'=>''];
        if(empty($lucky_id))
        {
            $rst['msg']='id不能为空';
            echo json_encode($rst);
            exit;
        }
        $luckygift = LuckygiftParams::findOne(['lucky_id'=>$lucky_id]);
        if(!isset($luckygift))
        {
            $rst['msg']='信息不存在';
            echo json_encode($rst);
            exit;
        }

        if($luckygift->delete() === false)
        {
            $rst['msg']='删除失败';
            \Yii::getLogger()->log('删除失败:'.var_export($luckygift->getErrors(),true),Logger::LEVEL_ERROR);
            echo json_encode($rst);
            exit;
        }
        LuckyGiftUtil::DeleteLuckyGiftCache();  //删除缓存
        echo json_encode($rst);
        exit;
        //return $this->controller->redirect(['/luckygift/index']);
//        return $this->controller->redirect('/luckygift/index');
    }
}