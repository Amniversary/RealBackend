<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/11
 * Time: 14:52
 */

namespace backend\controllers\IntegralMallActions;

use common\models\IntegralMall;
use yii\base\Action;
/**
 * 新增礼物积分
 * Class CreateAction
 * @package backend\controllers\CreateAction
 */
class CreateAction extends Action
{
    public function run()
    {
        $model = new IntegralMall();
        $this->controller->getView()->title = '新增礼品';
        if ($model->load(\Yii::$app->request->post()) && $model->save())
        {
            return $this->controller->redirect(['integralmall/index']);
        }
        else
        {
            return $this->controller->render('create', [
                'model' => $model,
            ]);
        }
    }
}