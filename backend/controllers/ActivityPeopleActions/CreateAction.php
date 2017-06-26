<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/23
 * Time: 15:52
 */

namespace backend\controllers\ActivityPeopleActions;


use common\models\ActivityPeople;
use common\models\Family;
use frontend\business\ClientUtil;
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
        $model = new ActivityPeople();

        if ($model->load(\Yii::$app->request->post()))
        {
            $Client = ClientUtil::GetClientNo(\Yii::$app->request->post('ActivityPeople')['living_master_id']);
            if(!isset($Client) || empty($Client))
            {
                $model->addError('living_master_id','蜜播ID 不存在');
            }
            else
            {
                $model->living_master_id = $Client->client_id;
                if($model->save())
                {
                    return $this->controller->redirect(['index']);
                }
            }

        }
        return $this->controller->render('create',[
            'model'=>$model,
        ]);
    }
} 