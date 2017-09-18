<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/9/13
 * Time: 下午2:29
 */

namespace frontend\business\SaveRecordByTransactions\SaveByTransaction;


use common\models\Comments;
use frontend\business\SaveRecordByTransactions\ISaveForTransaction;

class AddCommentSaveByTrans implements ISaveForTransaction
{
    public $data;
    public $extend;

    public function __construct($data, $extend = [])
    {
        $this->data = $data;
        $this->extend = $extend;
    }

    function SaveRecordForTransaction(&$error, &$outInfo)
    {
        if (!($this->data instanceof Comments)) {
            $error = '不是评论数据对象';
            return false;
        }
        if (!$this->data->save()) {
            $error = '保存评论信息失败';
            \Yii::error($error . ' :' . var_export($this->data->getErrors(), true));
            return false;
        }

        $sql = 'update wc_studying_dynamic set comment_count = comment_count + 1 where dynamic_id = :dy';
        $rst = \Yii::$app->db->createCommand($sql, [
            ':dy' => $this->data->dynamic_id
        ])->execute();
        if ($rst <= 0) {
            $error = '更新动态评论次数失败';
            \Yii::error($error . ' ' . \Yii::$app->db->createCommand($sql, [
                    ':dy' => $this->data->dynamic_id
                ])->rawSql);
            return false;
        }

        return true;
    }
}