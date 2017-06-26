<?php

namespace backend\controllers\ScoreGiftActions;


use common\models\Gift;
use common\models\GiftScore;
use frontend\business\UpdateContentUtil;
use yii\base\Action;
/**
 * 新增礼物积分
 * Class CreateAction
 * @package backend\controllers\CreateAction
 */
class ScoreCreateAction extends Action
{
    public function run()
    {
        $model = new GiftScore();
        $this->controller->getView()->title = '新增礼物积分';
        $model->score = 0;
        if ($model->load(\Yii::$app->request->post()) && $model->save())
        {
            return $this->controller->redirect(['gift_score_index']);
        }
        else
        {
            return $this->controller->render('createscore', [
                'model' => $model,
            ]);
        }
    }
}