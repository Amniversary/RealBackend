<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/7/7
 * Time: 下午6:48
 */

namespace backend\controllers\CustomActions;


use backend\business\WeChatUserUtil;
use yii\base\Action;

class DownloadAction extends Action
{
    public function run()
    {
        $cacheInfo = WeChatUserUtil::getCacheInfo();
        $access_token = $cacheInfo['authorizer_access_token'];
        WeChatUserUtil::getAppMenus($access_token,$cacheInfo['record_id']);

        return $this->controller->redirect('index');
    }
}