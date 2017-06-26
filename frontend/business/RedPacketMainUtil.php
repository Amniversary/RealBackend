<?php
/**
 * Created by PhpStorm.
 * User: hlq
 * Date: 2016/06/23
 * Time: 15:38
 */

namespace frontend\business;



use common\models\RedPacketMain;
use yii\db\Query;

class RedPacketMainUtil
{

    /**
     * 根据红包ID获取信息
     */
    public static function GetRedPacketById($red_packet_id)
    {
        return RedPacketMain::findOne(['gu_id'=>$red_packet_id]);
    }

    /**
     * 获取抢到红包用户的信息
     * @param $red_packet_id
     * @return null|static
     */
    public static function getRedPacketSonLiset($red_packet_id)
    {
        $query = (new Query())
            ->select(['red_packet_money as money','pic','nick_name'])
            ->from('mb_red_packet_son as son')
            ->innerJoin('mb_client as cl','son.client_id=cl.client_id')
            ->where('son.gu_id=:guid',[':guid' => $red_packet_id])
            ->orderBy('son.get_time desc')
            ->all();
        return $query;
    }


}