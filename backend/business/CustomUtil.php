<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/9/8
 * Time: 上午11:37
 */

namespace backend\business;


use common\models\BatchCustomer;
use yii\db\Query;

class CustomUtil
{
    /**
     * 根据任务id 获取客服任务信息
     * @param $id
     * @return null|BatchCustomer
     */
    public static function getCustomerById($id)
    {
        return BatchCustomer::findOne(['id' => $id]);
    }

    /**
     * 根据客服消息任务id 获取消息列表
     * @param $task_id
     * @return array
     */
    public static function getCustomerMsg($task_id)
    {
        $params = sprintf('select msg_id from wc_batch_customer_params where task_id = %d', $task_id);
        $condition = 'record_id in ('.$params.')';
        $query = (new Query())
            ->select(['record_id', 'event_id', 'content', 'msg_type', 'title', 'description', 'url', 'picurl', 'update_time', 'video'])
            ->from('wc_attention_event')
            ->where($condition)
            ->orderBy('order_no asc, create_time asc')->all();

        return $query;
    }
}