<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/1/27
 * Time: 11:21
 */

namespace backend\controllers\GoodsActions;


use common\models\Goods;
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
        $goods = Goods::findOne(['goods_id'=>$goods_id]);
        if(!isset($goods))
        {
            $rst['msg']='商品不存在';
            echo json_encode($rst);
            exit;
        }

        if($goods->delete() === false)
        {
            $rst['msg']='删除失败';
            \Yii::getLogger()->log('删除失败:'.var_export($goods->getErrors(),true),Logger::LEVEL_ERROR);
            echo json_encode($rst);
            exit;
        }
        echo json_encode($rst);
        exit;
//        return $this->controller->redirect('/goods/index');
    }
}