<?php
/**
 * Created by PhpStorm.
 * User: hlq
 * Date: 2016/5/12
 * Time: 10:00
 */

namespace backend\controllers\GoodsTicketToCashActions;


use common\models\GoodsTicketToCash;
use frontend\business\GoodsTicketToCashUtil;
use yii\base\Action;

class SetStatusAction extends Action
{
    public function run($goods_id)
    {
        $rst=['code'=>'1','msg'=>''];
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
        $modifyData = \Yii::$app->request->post('GoodsTicketToCash');
        if(!isset($modifyData))
        {
            $rst['message'] = '没有GoodsTicketToCashs模型对应的数据';
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
        if(!isset($dataItem['status']))
        {
            $rst['message'] = '状态值为空';
            echo json_encode($rst);
            exit;
        }
        $status = $dataItem['status'];
        $GoodsTicketToCash->status = $status;
        if(!GoodsTicketToCashUtil::SaveGoodsTicketToCash($GoodsTicketToCash,$error))
        {
            $rst['message'] = $error;
            echo json_encode($rst);
            exit;
        }
        echo '0';
    }
}