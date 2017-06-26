<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/31
 * Time: 16:26
 */

namespace backend\controllers\AdvertorialActions;

use backend\business\AdvertorialUtil;
use yii\base\Action;
use yii;


class DetailAction extends Action
{
    public function run()
    {
        $record_id = \Yii::$app->request->get('record_id');
        //设置布局文件，使其外观不和网后台整体外观一致
        $this->controller->layout = 'main_empty';

        $model = AdvertorialUtil::AdvertorialById($record_id);

        \Yii::$app->cache->delete("model");
        \Yii::$app->cache->set("model",$model);
        return $this->controller->render('detail',
            [
                'model' => $model,
            ]
        );
    }
}
