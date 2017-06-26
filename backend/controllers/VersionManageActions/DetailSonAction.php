<?php
/**
 * Created by PhpStorm.
 * User: hlq
 * Date: 2016/5/12
 * Time: 10:00
 */

namespace backend\controllers\VersionManageActions;


use frontend\business\MultiUpdateContentUtil;
use yii\base\Action;

class DetailSonAction extends Action
{
    public function run($update_id)
    {
        $this->controller->getView()->title = '子版本详情';
        $model = MultiUpdateContentUtil::GetUpdateContentById($update_id);
        $this->controller->layout='main_empty';
        return $this->controller->render('detailson',
            [
                'model' => $model,
            ]
        );
    }
}