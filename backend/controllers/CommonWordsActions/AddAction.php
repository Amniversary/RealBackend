<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/29
 * Time: 17:22
 */

namespace backend\controllers\CommonWordsActions;

use common\models\CommonWords;
use yii\base\Action;

/**
 * 新增超管常用语
 * Class CreateAction
 * @package backend\controllers\CreateAction
 */
class AddAction extends Action
{
    public function run()
    {
        $model = new CommonWords();
        $type = 1;
        $this->controller->getView()->title = '新增常用语';
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
            return $this->controller->render('create', [
                'model' => $model,
                'type' => $type
            ]);
        }
    }
}