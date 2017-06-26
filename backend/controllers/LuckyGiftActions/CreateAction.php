<?php

namespace backend\controllers\LuckyGiftActions;


use common\models\LuckygiftParams;
use frontend\business\LuckyGiftUtil;
use yii\base\Action;
/**
 * 新增礼物概率
 * Class CreateAction
 * @package backend\controllers\CreateAction
 */
class CreateAction extends Action
{
    public function run()
    {
        $model = new LuckygiftParams();
        $this->controller->getView()->title = '新增礼物概率';
        $model->create_time = date('Y-m-d H:i:s');
        if ($model->load(\Yii::$app->request->post()) && $model->save())
        {
            LuckyGiftUtil::DeleteLuckyGiftCache();  //删除缓存
            return $this->controller->redirect(['index']);
        }
        else
        {
            return $this->controller->render('create', [
                'model' => $model,
            ]);
        }
    }
}