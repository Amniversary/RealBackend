<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/9/9
 * Time: 下午6:16
 */

namespace backend\controllers\ArticleActions;


use common\models\ArticleOrder;
use yii\base\Action;

class DeleteOrderAction extends Action
{
    public function run($id)
    {
        $rst = ['code'=>1, 'msg'=>''];
        if(empty($id)) {
            $rst['msg'] = '参数 id不能为空';
            echo json_encode($rst);exit;
        }

        $Tag = ArticleOrder::findOne(['id'=> $id]);
        if(empty($Tag) || !isset($Tag)) {
            $rst['msg'] = '找不到对应的标签记录或已删除';
            echo json_encode($rst);exit;
        }

        if(!$Tag->delete()) {
            $rst['msg'] = '删除标签记录失败';
            \Yii::error($rst['msg']. '  '.var_export($Tag->getError(),true));
            echo json_encode($rst);exit;
        }

        $rst['code'] = 0;
        echo json_encode($rst);
    }
}