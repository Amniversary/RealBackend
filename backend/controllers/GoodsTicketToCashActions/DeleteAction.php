<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/1/27
 * Time: 11:21
 */

namespace backend\controllers\GoodsTicketToCashActions;


use common\models\GoodsTicketToCash;
use yii\base\Action;
use yii\log\Logger;

class DeleteAction extends Action
{
    public function run($goods_id)
    {
        $rst=['code'=>'0','msg'=>''];
        if(empty($goods_id))
        {
            $rst['msg']='商品id不能为空';
            echo json_encode($rst);
            exit;
        }
        $GoodsTicketToCash = GoodsTicketToCash::findOne(['goods_id'=>$goods_id]);
        if(!isset($GoodsTicketToCash))
        {
            $rst['msg']='商品不存在';
            echo json_encode($rst);
            exit;
        }

        if(!$GoodsTicketToCash->delete())
        {
            $rst['msg']='删除失败';
            \Yii::getLogger()->log('删除失败:'.var_export($GoodsTicketToCash->getErrors(),true),Logger::LEVEL_ERROR);
            echo json_encode($rst);
            exit;
        }
        echo json_encode($rst);
        exit;
//        return $this->controller->redirect('/goodstickettocash/index');
    }
}