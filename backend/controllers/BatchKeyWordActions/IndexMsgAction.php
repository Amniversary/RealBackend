<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/7/4
 * Time: 下午3:25
 */

namespace backend\controllers\batchKeyWordActions;


use backend\business\WeChatUserUtil;
use backend\models\BatchKeyWordMsgSearch;
use backend\models\KeyWordMsgSearch;
use yii\base\Action;

class IndexMsgAction extends Action
{
    public function run()
    {
        $key_id = \Yii::$app->request->get('key_id');
        $this->controller->getView()->title = '批量关键词消息';
        $searchModel = new BatchKeyWordMsgSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);
        return $this->controller->render('indexmsg',[
            'searchModel'=>$searchModel,
            'dataProvider'=>$dataProvider,
            'key_id'=>$key_id,
        ]);

    }
}