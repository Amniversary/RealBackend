<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/3/31
 * Time: 17:15
 */
   namespace backend\controllers\RedPacketsActions;


use backend\components\ExitUtil;
use frontend\business\RedPacketsUtil;
use yii\base\Action;

/**
 * 修改红包
 * Class EditAction
 * @package backend\controllers\RedPacketsActions
 */
class EditAction extends Action
{
    public function run($red_packets_id)
    {
        if(empty($red_packets_id))
        {
            ExitUtil::ExitWithMessage('红包id不存在');
        }
        $model = RedPacketsUtil::GetRedPacketsById($red_packets_id);
        if(empty($model->pic))
        {
            $model->pic = '1';
        }
        if(empty($model->over_pic))
        {
            $model->over_pic = '1';
        }
        if(!isset($model))
        {
            ExitUtil::ExitWithMessage('红包记录不存在');
        }
        if ($model->load(\Yii::$app->request->post()) && $model->save())
        {
            return $this->controller->redirect(['index']);
        }
        else
        {
            return $this->controller->render('update',[
                'model' => $model,
            ]);
        }
    }

}