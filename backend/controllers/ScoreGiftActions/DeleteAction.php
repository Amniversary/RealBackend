<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/18
 * Time: 9:51
 */
namespace backend\controllers\ScoreGiftActions;


use backend\business\ScoreGiftUtil;
use yii\base\Action;
use yii\log\Logger;

class DeleteAction extends Action
{
    public function run($activity_id)
    {
        $rst=['code'=>'1','msg'=>''];
        if(empty($activity_id))
        {
            $rst['msg']='活动参数id不能为空';
            echo json_encode($rst);
            exit;
        }
        $ScoreGift = ScoreGiftUtil::GetScoreGiftById($activity_id);
        if(!isset($ScoreGift))
        {
            $rst['msg']='活动参数不存在';
            echo json_encode($rst);
            exit;
        }

        if($ScoreGift->delete() === false)
        {
            $rst['msg']='活动删除失败';
            \Yii::getLogger()->log('删除失败:'.var_export($ScoreGift->getErrors(),true),Logger::LEVEL_ERROR);
            echo json_encode($rst);
            exit;
        }
        //获取前一个页面的地址
        $rst['code'] = '0';
        echo json_encode($rst);
        exit;
    }
}