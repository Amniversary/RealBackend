<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/9
 * Time: 14:56
 */

namespace backend\controllers\ClientalbumActions;

use frontend\business\DynamicUtil;
use yii\base\Action;
use yii\log\Logger;

class ViewallAction extends Action
{
    public function run($user_id)
    {
        $this->controller->getView()->title = '查看用户相册';
        $dynamicInfo = DynamicUtil::GetDynamicByUserId($user_id);
//        \Yii::getLogger()->log('$dynamicInfo___'.var_export($dynamicInfo,true),Logger::LEVEL_ERROR);
        return $this->controller->render('viewall',
            [
                'dataProvider' => $dynamicInfo,
            ]
        );
    }
}