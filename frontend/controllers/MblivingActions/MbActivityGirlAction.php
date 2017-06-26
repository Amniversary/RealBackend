<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/7
 * Time: 20:42
 */

namespace frontend\controllers\MblivingActions;


use frontend\business\ActivityStatisticUtil;
use yii\base\Action;

class MbActivityGirlAction extends Action
{
    public function run()
    {
        $rst = ActivityStatisticUtil::GirlCache();
        echo $rst;
        exit;
    }
} 