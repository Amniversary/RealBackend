<?php
namespace backend\controllers\ActivityPrizeActions;


use common\models\ActivityPrize;
use frontend\business\ActivityChanceUtil;
use yii\base\Action;
use yii\log\Logger;

class DeleteAction extends Action
{
    public function run($prize_id)
    {
        $rst=['code'=>'0','msg'=>''];
        if(empty($prize_id))
        {
            $rst['msg']='奖品id不能为空';
            echo json_encode($rst);
            exit;
        }
        $activity_prize = ActivityPrize::findOne(['prize_id'=>$prize_id]);
        if(!isset($activity_prize))
        {
            $rst['msg']='奖品不存在';
            echo json_encode($rst);
            exit;
        }

        if($activity_prize->delete() === false)
        {
            $rst['msg']='删除失败';
            \Yii::getLogger()->log('删除失败:'.var_export($activity_prize->getErrors(),true),Logger::LEVEL_ERROR);
            echo json_encode($rst);
            exit;
        }
        ActivityChanceUtil::DeleteActivityPrizeCache($error);  //删除缓存信息
        echo json_encode($rst);
        exit;
//        return $this->controller->redirect(['/activityprize/index']);
    }
}