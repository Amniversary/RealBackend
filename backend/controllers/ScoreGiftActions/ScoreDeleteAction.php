<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/1/27
 * Time: 11:21
 */

namespace backend\controllers\ScoreGiftActions;


use common\models\GiftScore;
use yii\base\Action;
use yii\log\Logger;

class ScoreDeleteAction extends Action
{
    public function run($record_id)
    {
        $rst=['code'=>'1','msg'=>''];
        if(empty($record_id))
        {
            $rst['msg']='商品id不能为空';
            echo json_encode($rst);
            exit;
        }
        $score = GiftScore::findOne(['record_id'=>$record_id]);
        if(!isset($score))
        {
            $rst['msg']='积分商品不存在';
            echo json_encode($rst);
            exit;
        }

        if($score->delete() === false)
        {
            $rst['msg']='删除失败';
            \Yii::getLogger()->log('删除失败:'.var_export($score->getErrors(),true),Logger::LEVEL_ERROR);
            echo json_encode($rst);
            exit;
        }

        \Yii::$app->cache->delete('get_score_gifts');
        $rst['code'] = '0';
        echo json_encode($rst);
        exit;
//        return $this->controller->redirect('/goods/index');
    }
}