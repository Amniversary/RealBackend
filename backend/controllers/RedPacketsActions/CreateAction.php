<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2015/12/29
 * Time: 19:17
 */

namespace backend\controllers\RedPacketsActions;


use yii\base\Action;
use common\models\RedPackets;
/**
 * 新增红包
 * Class CreateAction
 * @package backend\controllers\RedPacketsActions
 */
class CreateAction extends Action
{
    public function run()
    {
        $model = new RedPackets();
        $model->get_type = '1';
        $model->start_time = '无';
        $model->end_time = '无';
        if(empty($model->pic))
        {
            $model->pic = '1';
        }
        if(empty($model->over_pic))
        {
            $model->over_pic = '1';
        }
        if ($model->load(\Yii::$app->request->post()) && $model->save())
        {
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