<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/23
 * Time: 15:52
 */

namespace backend\controllers\FamilyActions;


use backend\business\FamilyMemberUtil;
use common\models\Family;
use common\models\FamilyMember;
use frontend\business\ClientUtil;
use yii;
use yii\base\Action;
use yii\base\Exception;

/**
 * 新增家族成员
 * Class CreateSonAction
 * @package backend\controllers\FamilyActions
 */
class CreateSonAction extends Action
{
    public function run($family_id,$page)
    {
        $model = new FamilyMember();
        $model->family_id = intval($family_id);
        if ($model->load(\Yii::$app->request->post()))
        {
            try
            {
                $model->create_time = date('Y-m-d H:i:s');

                $client_model = ClientUtil::GetClientNo(\Yii::$app->request->post('FamilyMember')['family_member_id']);

                if(!isset($client_model) || empty($client_model))
                {
                    $model->addError('family_member_id', 'ID不存在!');
                }
                else
                {
                    $model->family_member_id = intval($client_model->client_id);
                    $model->remark1 = \Yii::$app->user->getIdentity()->username;
                    if(FamilyMemberUtil::CreateSaveRansactions($model))
                    {
                        return $this->controller->redirect(['family/index_son','family_id'=>$model->family_id,'page'=>$page]);
                    }
                }

            }
            catch ( Exception $e)
            {
                echo "新增家族成员时发生了错误：".$e->getMessage();
            }
        }
        return $this->controller->render('create_son',[
            'model'=>$model,
            'page' => $page
        ]);
    }
} 