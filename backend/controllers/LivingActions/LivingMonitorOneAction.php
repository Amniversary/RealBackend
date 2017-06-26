<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/2/20
 * Time: 10:12
 */

namespace backend\controllers\LivingActions;


use common\models\User;
use frontend\business\ClientUtil;
use frontend\business\LivingUtil;
use backend\models\BackendUserSearch;

use yii\base\Action;
use yii\db\Query;

class LivingMonitorOneAction extends Action
{
    public function run()
    {
        $this->controller->getView()->title = '直播监控室（全部）';

        $data = LivingUtil::GetLiveLiving();
        //echo "<pre>";
        $username = (new Query())
            ->select(['username','backend_user_id'])
            ->from('mb_user')
            ->all();
        //print_r( $username);//->attributes

        /***** 测试用 *****/
        $_data = [];
        for ($i = 0; $i < 22; $i++) {
            $_data = array_merge($_data, $data);
        }
        $data = $_data;
        /***** end *****/

        return $this->controller->render('livingmonitorone',
            [
                'data' => $data,
                'username'=>$username
            ]
        );
    }
}