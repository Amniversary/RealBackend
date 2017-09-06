<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/6/29
 * Time: 下午1:23
 */

namespace backend\controllers\PublicListActions;


use backend\models\PublicListSearch;
use yii\base\Action;

class IndexAction extends Action
{
    public function run()
    {
        $post = \Yii::$app->request->get('tag');
        $this->controller->getView()->title = '公众号列表';
        $searchModel = new PublicListSearch();
        if (empty($post) || !isset($post)) {
            $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);
        } else {
            if (empty($post['auth'])) $post['auth'] = [0];
            $params = implode(',', $post['auth']);
            $dataProvider = $searchModel->searchTag($params, \Yii::$app->request->queryParams);
        }
        return $this->controller->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'data' => $post['title']
        ]);
    }
}