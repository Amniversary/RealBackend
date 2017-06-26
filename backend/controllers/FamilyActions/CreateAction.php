<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/23
 * Time: 15:52
 */

namespace backend\controllers\FamilyActions;


use common\models\Family;
use yii\base\Action;

/**
 * 新增家族长账号
 * Class CreateAction
 * @package backend\controllers\FamilyActions
 */
class CreateAction extends Action
{
    public function run()
    {
        $model = new Family();
        $model->pic = 'http://oss-cn-hangzhou.aliyuncs.com/mblive/meibo-test/logo.png';
        $model->create_time = date('Y-m-d H:i:s');
        $model->family_num = 0;
        $model->setScenario('create');
        if ($model->load(\Yii::$app->request->post()) && $model->save())
        {
            return $this->controller->redirect(['index']);
        }
        else
        {
            return $this->controller->render('create',[
                'model'=>$model,
            ]);
        }
    }
} 