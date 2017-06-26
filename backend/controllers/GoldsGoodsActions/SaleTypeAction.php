<?php

namespace backend\controllers\GoldsGoodsActions;


use frontend\business\GoldsAccountUtil;
use frontend\business\GoldsGoodsUtil;
use backend\business\UserUtil;
use backend\components\ExitUtil;

use yii\base\Action;
/**
 * 修改金币商品
 * Class SaleTypeAction
 * @package backend\controllers\SaleTypeAction
 */
class SaleTypeAction extends Action
{
    public function run($gold_goods_id){
        
        $rst =['message'=>'','output'=>''];
        if(empty($gold_goods_id)){
            $rst['message'] = '商品id不能为空';
            echo json_encode($rst);
            exit;
        }
        
        $goods =   GoldsGoodsUtil::GetGoldGoodsModelOne($gold_goods_id);
        if(!isset($goods))
        {
            $rst['message'] = '记录不存在';
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
        $modifyData = \Yii::$app->request->post('GoldsGoods');
        if(!isset($modifyData))
        {
            $rst['message'] = '没有GoldsGoods模型对应的数据';
            echo json_encode($rst);
            exit;
        }

        if(!isset($modifyData[$editIndex]))
        {
            $rst['message'] = '对应的列下没有数据';
            echo json_encode($rst);
            exit;
        }
        $dataItem = $modifyData[$editIndex];
        if(!isset($dataItem['sale_type']))
        {
            $rst['message'] = '销售类型值为空';
            echo json_encode($rst);
            exit;
        }
        $sale_type = $dataItem['sale_type'];
        $goods->sale_type = $sale_type;
        if(!$goods->save()){
            $rst['message'] = '更新错误';
            echo json_encode($rst);
            exit;
        }
        echo '0';
    }
} 