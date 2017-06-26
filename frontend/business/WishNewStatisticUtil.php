<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/3/14
 * Time: 15:00
 */

namespace frontend\business;


use common\models\WishNewStatistic;
use yii\log\Logger;

class WishNewStatisticUtil
{
    /**
     * 更新愿望最新状态，打赏、评论时调用
     * @param $wish_id
     * @param null $order_no
     * @param $error
     */
    public static function UpdateWishNewInfo($wish_id,$order_no=null)
    {
        $sql = 'update my_wish_new_statistic set modify_time=now()';
        $params = [];
        if(isset($order_no))
        {
            $sql .= ' ,order_no=:no';
            $params[':no']=$order_no;
        }
        $sql .= ' where wish_id=:wid';
        $params[':wid']=$wish_id;
        return (\Yii::$app->db->createCommand($sql,$params)->execute() > 0);
    }

    /**
     * 获取新模型
     * @param $wish_id
     * @param int $order_no
     */
    public static function GetNewModel($wish_id,$order_no=1000)
    {
        $model = new WishNewStatistic();
        $model->wish_id = $wish_id;
        $model->order_no = $order_no;
        $model->modify_time = date('Y-m-d H:i:s');
        return $model;
    }

    /**
     * 创建愿望动态信息记录  新增愿望时调用
     * @param $wish_id
     * @param null $order_no
     * @param $error
     */
    public static function CreateWishNewInfo($wish_id,&$error,$order_no=1000)
    {
        $model = new WishNewStatistic();
        $model->wish_id = $wish_id;
        $model->order_no = $order_no;
        $model->modify_time = date('Y-m-d H:i:s');
        if(!$model->save())
        {
            $error = '创建愿望动态信息失败';
            \Yii::getLogger()->log($error.' '.var_export($model->getErrors(),true), Logger::LEVEL_ERROR);
            return false;
        }
        return true;
    }
} 