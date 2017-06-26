<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/4/28
 * Time: 14:00
 */

namespace frontend\business;


use yii\db\Query;

class PaymentsUtil {

    /**
     * 获取支付方式
     * @param $status
     */
    public static function GetPaymentsList($status)
    {
        $query =new Query();
        $sql = $query
            ->select(['code','title','icon','app_id'])
            ->from('mb_payment')
            ->where('status = :sd',[':sd'=>$status])
            ->orderBy('order_no asc')
            ->all();

        return $sql;
    }

    /**
     *获取支付方式
     * @param $status
     * @param $appType [1,2] 或 1
     * @parma $isIos  是否IOS
     * @return array
     */
    public static function GetPaymentsListByAppType($status,$appType,$isIos = false)
    {
        $query =new Query();
        $query
            ->select(['code','title','icon','app_id'])
            ->from('mb_payment')
            ->andWhere(['NOT', ['app_id' =>'']])
            ->andFilterWhere(['status'=>$status])
            ->andFilterWhere(['app_type'=>$appType])
            ->orderBy('order_no asc');

        // $isIos && $query->andFilterWhere(['applied_ios' => 1]);

        return $query->all();
    }

    const CACHE_KEYNAME = 'payments_d5e62';

    /**
     * 根据appid获取支付方式
     * @param string $appId
     * @param int $status
     * @return array
     */
    public static function getPaymentsByAppId($appId, $status)
    {
        $payments = self::getPaymentsInCache(false);
        $result = [];
        foreach ($payments as $key => $payment) {
            $appIds = json_decode($payment['app_id'], true);
            if (is_array($appIds) && in_array($appId, $appIds)) {
                $result[] = $payment;
            }
        }
        return $result;
    }

    /**
     * 查询所有可用支付方式
     * @param bool $flush 是否刷新缓存
     * @return array
     */
    public static function getPaymentsInCache($flush = false)
    {
        $payments = \Yii::$app->cache->get(self::CACHE_KEYNAME);
        if ($flush || !$payments) {
            $query = new Query();
            $query
                ->select(['code', 'status', 'title', 'icon', 'app_id'])
                ->from('mb_payment')
                ->andWhere(['!=', 'status', 0])
                ->andWhere('app_id IS NOT NULL')
                ->orderBy('order_no ASC');
            $payments = $query->all();
            $payments = json_encode($payments);
            \Yii::$app->cache->set(self::CACHE_KEYNAME, $payments);
        }

        return json_decode($payments, true);
    }
} 