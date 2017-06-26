<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/1/3
 * Time: 16:07
 */

namespace frontend\business\WishModifyActions;


use common\models\Wish;
use frontend\business\WishUtil;
use yii\base\Exception;
use yii\log\Logger;

class WishModifyRedPacketMoney implements IWishModify
{
    public function WishModify($wish,&$error,$params=[])
    {
        if(!($wish instanceof Wish))
        {
            $error='不是愿望对象';
            return false;
        }
        if(!isset($params['red_packet_money']) || empty($params['red_packet_money']))
        {
            $error = '参数错误，红包金额不能为空';
            return false;
        }
        //$wish = WishUtil::GetWishRecordById($wish->wish_id);
        $redPacketMoney = doubleval($params['red_packet_money']);
        $sql = 'update my_wish set red_packets_money = red_packets_money + :rm where wish_id=:wid and status = 1 and finish_status = 1
        ';
        $rst = \Yii::$app->db->createCommand($sql,[
            ':rm'=>$redPacketMoney,
            ':wid'=>$wish->wish_id
        ])->execute();
        if($rst <= 0)
        {
            $error = '更新红包金额失败，愿望状态错误';
            return false;
        }
        return true;
    }
} 