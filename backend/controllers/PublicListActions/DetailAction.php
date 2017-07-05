<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/7/4
 * Time: 上午10:08
 */

namespace backend\controllers\PublicListActions;


use backend\components\ExitUtil;
use common\models\AttentionEvent;
use yii\base\Action;

class DetailAction extends Action
{
    public function run($record_id)
    {
        $model = AttentionEvent::findOne(['record_id'=>$record_id]);
        if(empty($model)){
            ExitUtil::ExitWithMessage('记录信息不存在');
        }

        $this->controller->layout='main_empty';
        return $this->controller->render('detail',[
            'model'=>$model,
        ]);
    }
}