<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/10/12
 * Time: 下午6:04
 */

namespace frontend\business;


use common\models\CBalance;

class BalanceUtil
{
    /**
     * 获取用户账户信息
     * @param $user_id
     * @return null|CBalance
     */
    public static function GetUserBalanceById($user_id)
    {
        return CBalance::findOne(['user_id' => $user_id]);
    }
}