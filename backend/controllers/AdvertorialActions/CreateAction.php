<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/31
 * Time: 13:23
 */

namespace backend\controllers\AdvertorialActions;


use common\models\Advertorial;
use yii\base\Action;
/**
 * 新增活动
 * Class CreateAction
 * @package backend\controllers\CreateAction
 */
class CreateAction extends Action
{
    public function run()
    {
        $model = new Advertorial();
        $this->controller->getView()->title = '新增软文';
        if ( $model->load(\Yii::$app->request->post()) )
        {
            if($model->save())
            {
                \Yii::$app->cache->set("model",$model);
                return $this->controller->redirect('index');
            }
        }
        else
        {
            return $this->controller->render('create', [
                'model' => $model,
            ]);
        }
    }
}