<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/9/6
 * Time: 上午10:43
 */

namespace backend\controllers\PublicListActions;


use common\models\SystemTag;
use common\models\SystemTagMenu;
use yii\base\Action;

class SetTagListAction extends Action
{
    public function run()
    {
        $title = \Yii::$app->request->post('title');
        if (empty($title)) {
            return $this->controller->redirect('index', 200);
        }
        $params = implode(',', $title);
        $tag_list = SystemTag::find()
            ->select(['id', 'tag_name'])
            ->where('id in (' . $params . ')')
            ->all();
        $dis = [];
        foreach ($tag_list as $v) {
            $dis[$v['id']] = $v['tag_name'];
        }
        $system = SystemTagMenu::find()
            ->select(['auth_id'])
            ->where('tag_id in (' . $params . ')')
            ->groupBy('auth_id')
            ->all();
        $result = [];
        foreach ($system as $item) {
            $result[] = $item['auth_id'];
        }
        return $this->controller->redirect(['index', 'tag' => ['auth' => $result, 'title' => $dis]], 200);
    }
}