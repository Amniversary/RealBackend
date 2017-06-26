<?php
/**
 * Created by PhpStorm.
 * User: hlq
 * Date: 2016/6/6
 * Time: 10:00
 */

namespace backend\controllers\EnterRoomNoteActions;


use backend\models\EnterRoomNoteSearch;
use yii\base\Action;
use yii\log\Logger;

class IndexAction extends Action
{
    public function run()
    {
        $this->controller->getView()->title = '等级欢迎词设置';
        $searchModel = new EnterRoomNoteSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);
        return $this->controller->render('index',
            [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
                'add_title' => '等级欢迎词设置',
            ]
        );
    }
}