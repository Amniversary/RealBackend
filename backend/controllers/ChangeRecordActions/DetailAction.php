<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/18
 * Time: 16:34
 */

namespace backend\controllers\ChangeRecordActions;


use backend\business\BackendBusinessCheckUtil;
use backend\components\ExitUtil;
use common\models\ChangeRecord;
use frontend\business\BusinessCheckUtil;
use frontend\business\TicketToCashUtil;
use yii\base\Action;
use yii\db\Query;

/**
 * 审核详情
 * Class CreateAction
 * @package backend\controllers\UpdateAction
 */
class DetailAction extends Action
{
    public function run($record_id)
    {
//        echo \Yii::$app->user->id;exit;
//        $model = ChangeRecord::findOne(['record_id' => $record_id]);

        $model = (new Query())
            ->select(['mcr.user_id','mcr.user_name','mcr.gift_name','mcr.change_time','mcr.change_state','mcr.address','mcr.record_id','mc.client_no','mc.nick_name','muc.phone','muc.alipay','muc.wx_number','muc.wx_name'])
            ->from('mb_change_record mcr')
            ->innerJoin('mb_client mc','mc.client_id=mcr.user_id')
            ->innerJoin('mb_user_contact muc','muc.user_id=mcr.user_id')
            ->where('mcr.record_id=:rid',[':rid'=>$record_id])
            ->one();

        $this->controller->layout='main_empty';

        return $this->controller->render('detail', [
            'model' => $model,
        ]);
    }
}