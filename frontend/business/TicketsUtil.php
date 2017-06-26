<?php
/**
 * Created by PhpStorm.
 * User: hlq
 * Date: 2016/5/3
 * Time: 15:01
 */

namespace frontend\business;

use yii\db\Query;

class TicketsUtil
{
    /**
     * 通过直播ID获取当前直播票数
     * @param $living_id
     * @param $error
     */
    public static function GetTickets($living_id)
    {
        $query = new Query();
        $result = $query->select(['living_tickets_id','living_id','tickets_num'])
            ->from('mb_living_tickets')
            ->where('living_id=:lid',[
                ':lid' => $living_id
            ])->one();
        return $result;
    }
}