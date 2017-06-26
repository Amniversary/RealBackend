<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/5
 * Time: 13:46
 */

namespace backend\controllers\GoodsActions;


use backend\business\GoodsUtil;
use yii\base\Action;

class SetHighLedAction extends Action
{
    public function run($goods_id)
    {
        $rst =['message'=>'','output'=>''];
        if(empty($goods_id))
        {
            $rst['message'] = '商品id不能为空';
            echo json_encode($rst);
            exit;
        }
        $goods = GoodsUtil::GetGoodsById($goods_id);
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
        $modifyData = \Yii::$app->request->post('Goods');
        if(!isset($modifyData))
        {
            $rst['message'] = '没有Goods模型对应的数据';
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
        if(!isset($dataItem['high_led']))
        {
            $rst['message'] = '销售类型值为空';
            echo json_encode($rst);
            exit;
        }
        $high_led = $dataItem['high_led'];
        $goods->high_led= $high_led;
        if(!GoodsUtil::SaveGoods($goods,$error))
        {
            $rst['message'] = $error;
            echo json_encode($rst);
            exit;
        }
        echo '0';
    }
} 