<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/2/17
 * Time: 11:52
 */

namespace backend\controllers\LivingActions;


use backend\models\ClientHotLivingSearch;
use frontend\business\LivingUtil;
use yii\base\Action;

class LivingMonitorAction extends Action
{
    public function run()
    {
        $this->controller->getView()->title = '直播监控室(每6个)';

        $data = LivingUtil::GetLiveLiving();

        /***** 测试用 *****/
        $_data = [];
        for ($i = 0; $i < 22; $i++) {
            $_data = array_merge($_data, $data);
        }
        $data = $_data;
        /***** end *****/

        return $this->controller->render('livingmonitor',
            [
                'data' => $data,
            ]
        );
    }
}