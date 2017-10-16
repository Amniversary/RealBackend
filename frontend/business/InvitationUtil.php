<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/10/10
 * Time: 下午4:50
 */

namespace frontend\business;


use common\models\CGuest;
use yii\db\Query;

class InvitationUtil
{
    /**
     * 根据用户id 获取 我的请帖
     * @param $user_id
     * @return array
     */
    public static function GetMyWeddingInfo($user_id)
    {
        $query = (new Query())
            ->select(['ci.card_id', 'bride', 'bridegroom', 'ci.phone', 'wedding_time', 'site', 'pic'])
            ->from('cGuest cg')
            ->innerJoin('cInvitationCard ci', 'cg.card_id = ci.card_id and ci.status = 1')
            ->where('user_id = :ud and user_status in (1,2)', [':ud' => $user_id])
            ->all();
        return $query;
    }

    /**
     * 根据用户id 获取 收到请帖
     * @param $user_id
     * @return array
     */
    public static function GetOtherWeddingInfo($user_id)
    {
        $query = (new Query())
            ->select(['ci.card_id', 'bride', 'bridegroom', 'ci.phone', 'wedding_time', 'site', 'pic'])
            ->from('cGuest cg')
            ->innerJoin('cInvitationCard ci', 'cg.card_id = ci.card_id and ci.status = 1')
            ->where('user_id = :ud and user_status in (3)', [':ud' => $user_id])
            ->all();
        return $query;
    }

    /**
     * 获取宾客用户信息
     * @param $card_id
     * @param $user_id
     * @return null|CGuest
     */
    public static function GetGuestUserById($card_id, $user_id)
    {
        return CGuest::findOne(['card_id'=>$card_id, 'user_id'=>$user_id]);
    }
}