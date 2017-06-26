<?php

namespace backend\controllers\HotwordsActions;


use backend\components\ExitUtil;
use frontend\business\CarouselUtil;
use frontend\business\HotWordsUtil;
use yii\base\Action;
/**
 * 修改热词
 * Class CreateAction
 * @package backend\controllers\CreateAction
 */
class UpdateAction extends Action
{
    public function run($hot_words_id)
    {

        $model = HotWordsUtil::GetHotWordsById($hot_words_id);
        if(!isset($model))
        {
            ExitUtil::ExitWithMessage('热词记录不存在');
        }
        if ($model->load(\Yii::$app->request->post()) && $model->save())
        {
            return $this->controller->redirect(['index']);
        }
        else
        {
            return $this->controller->render('update', [
                'model' => $model,
            ]);
        }
    }
} 