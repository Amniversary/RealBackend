<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/7/4
 * Time: 下午3:08
 */

namespace backend\controllers\KeyWordActions;


use backend\components\ExitUtil;
use common\models\Keywords;
use yii\base\Action;

class DeleteAction extends Action
{
    public function run($key_id)
    {
        $rst = ['code'=>'1', 'msg'=>''];
        if(empty($key_id)){
            $rst['msg'] = '关键词记录Id不能为空';
            echo json_encode($rst);
            exit;
        }
        $model = Keywords::findOne(['key_id'=>$key_id]);
        if(empty($model)){
            $rst['msg'] = '关键词记录不存在或已经删除';
            echo json_encode($rst);
            exit;
        }

        if($model->delete() === false) {
            $rst['msg']='删除失败';
            \Yii::error('删除失败:'.var_export($model->getErrors(),true));
            echo json_encode($rst);
            exit;
        }

        $rst['code'] = '0';
        echo json_encode($rst);
        exit;
    }
}