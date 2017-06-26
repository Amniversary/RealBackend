<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/29
 * Time: 17:24
 */
namespace backend\controllers\CommonWordsActions;

use backend\components\ExitUtil;
use common\models\CommonWords;
use yii\base\Action;


class UpdateAction extends Action
{
    public function run($cid)
    {
        $model = CommonWords::findOne(['cid'=>$cid]);
        if(!isset($model))
        {
            ExitUtil::ExitWithMessage('信息不存在');
        }

        if ($model->load(\Yii::$app->request->post()) && $model->save())
        {
            $sql = 'select user_id from mb_common_words where user_id > 1 GROUP BY user_id ';
            $rst = \Yii::$app->db->createCommand($sql)->queryAll();
            foreach($rst as $v)
            {
                \Yii::$app->cache->delete('set_admin_warning_'.$v['user_id']);
            }
            return $this->controller->redirect(['commonwords/index']);
        }
        else
        {
            return $this->controller->render('update', [
                'model' => $model,
                'type' => 2,
            ]);
        }
    }
}