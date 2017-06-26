<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/4/26
 * Time: 20:59
 */

namespace frontend\business;


use common\models\GoodsTicketToCash;
use yii\db\Query;
use yii\log\Logger;

class GoodsTicketToCashUtil
{
    /**
     * 根据id获取商品信息
     * @param $id
     * @return null|static
     */
    public static function GetGoodsTicketToCashById($id)
    {
        return GoodsTicketToCash::findOne(['goods_id'=>$id]);
    }

    /**
     * 获取票提现商品列表
     * @return array
     */
    public static function GetGoodsTicketList()
    {
        $query = (new Query())
            ->select(['goods_id','pic','ticket_num','result_money'])
            ->from('mb_goods_ticket_to_cash')
            ->where('status = 1')
            ->orderBy('ticket_num asc')
            ->all();
        $test = [];
        foreach($query as $list)
        {
            $test[] = $list;
        }
        return $test;
    }


    /**
     * 保存商品信息
     * @param $goods
     * @param $error
     * @return bool
     */
    public static function SaveGoodsTicketToCash($GoodsTicketToCash, &$error)
    {
        if(!($GoodsTicketToCash instanceof GoodsTicketToCash))
        {
            $error = '不是票转豆商品记录';
            return false;
        }
        if(!$GoodsTicketToCash->save())
        {
            $error = '状态保存失败';
            \Yii::getLogger()->log($error.' :'.var_export($GoodsTicketToCash->getErrors(),true),Logger::LEVEL_ERROR);
            return false;
        }
        return true;
    }


    /**
     * 获取今日用户提现金额
     * @param $user_id
     * @return int
     */
    public static function GetStatisticsCashMoney($user_id)
    {
        $date = date('Y-m-d');
        $query = (new Query())
            ->select(['SUM(real_cash_money) as cash_money'])
            ->from('mb_ticket_to_cash')
            ->where('user_id = :uid AND cash_type =1 AND DATE_FORMAT(create_time,\'%Y-%m-%d\') = :now',[':now'=>$date,':uid'=>$user_id])
            ->one();
        $cash_money = $query['cash_money'];
        if(empty($query) || !$query)
        {
            $cash_money = 0;
        }

        return $cash_money;
    }
} 