<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/10
 * Time: 20:26
 */

namespace frontend\business;


use common\models\Goods;
use common\models\Recharge;
use yii\log\Logger;


class GoodsUtil
{
    /**
     * 根据商品id 获取商品信息
     * @param $goods_id
     * @return null|static
     */
    public static function GetGoodsById($goods_id)
    {
        return Goods::findOne(['goods_id'=>$goods_id]);
    }

    /**
     * 通过唯一号查信息
     * @param $op_unique_no
     * @return null|static
     */
    public static function GetRechargeByOpUniqueNo($op_unique_no)
    {
        return Recharge::findOne(['op_unique_no' => $op_unique_no]);
    }

    /**
     * 根据第三方订单号获取订单信息
     * @param $other_pey_bill
     * @return null|static
     */
    public static function GetRechargeByOpReceiptData($other_pey_bill)
    {
        return Recharge::findOne(['other_pay_bill' => $other_pey_bill]);
    }

    /**
     * 保存商品信息
     * @param $goods
     * @param $error
     * @return bool
     */
    public static function SaveGoods($goods, &$error)
    {
        if(!($goods instanceof Goods))
        {
            $error = '不是商品记录';
            return false;
        }
        if(!$goods->save())
        {
            $error = '商品记录保存失败';
            \Yii::getLogger()->log($error.' :'.var_export($goods->getErrors(),true),Logger::LEVEL_ERROR);
            return false;
        }
        return true;
    }

} 