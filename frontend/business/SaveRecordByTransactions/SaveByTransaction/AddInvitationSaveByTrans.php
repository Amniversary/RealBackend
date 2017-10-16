<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/10/10
 * Time: 下午2:10
 */

namespace frontend\business\SaveRecordByTransactions\SaveByTransaction;


use common\models\CGuest;
use common\models\CInvitationCard;
use frontend\business\SaveRecordByTransactions\ISaveForTransaction;

class AddInvitationSaveByTrans implements ISaveForTransaction
{
    public $data;
    public $extend;

    public function __construct($record, $extend = [])
    {
        $this->data = $record;
        $this->extend = $extend;
    }

    function SaveRecordForTransaction(&$error, &$outInfo)
    {
        if (!($this->data instanceof CInvitationCard)) {
            $error = '不是请帖对象数据模型';
            return false;
        }
        if (!$this->data->save()) {
            $error = '服务器繁忙, 创建请帖失败';
            \Yii::error($error . ' ' . var_export($this->data->getErrors(), true));
            return false;
        }
        $model = new CGuest();
        $model->card_id = $this->data->card_id;
        $model->user_id = $this->extend['id'];
        $model->user_status = 1;
        $model->card_status = 1;
        $model->user_id = 1;
        $model->update_time = time();
        $model->phone = '';
        $model->wish = '';
        $model->name = '';
        $model->num = 0;
        if (!$model->save()) {
            $error = '初始化宾客用户信息失败';
            \Yii::error($error . ' ' . var_export($model->getErrors(), true));
            return false;
        }
        return true;
    }
}