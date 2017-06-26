<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/1/3
 * Time: 16:06
 */

namespace frontend\business\WishModifyActions;

use common\models\Wish;
use frontend\business\WishUtil;
use yii\base\Exception;
use yii\log\Logger;

class WishModifyByReward implements IWishModify
{
    public function WishModify($wish,&$error,$params=[])
    {
        if(!($wish instanceof Wish))
        {
            $error='不是愿望对象';
            return false;
        }
        $wish = WishUtil::GetWishRecordById($wish->wish_id);
        if(!isset($params['pay_left_money']))
        {
            $error = '除红包外打赏金额不能为空';
            return false;
        }
        if(!isset($params['packetsMoney']))
        {
            $error = '红包金额不能为空';
            return false;
        }
        $sql = 'update my_wish set
reward_num=reward_num + 1,
comment_num=comment_num+1,
ready_reward_money = ready_reward_money + :rrm,
red_packets_money = red_packets_money + :pkm
where wish_id=:wid and finish_status=1 and status=1
';
        $rst = \Yii::$app->db->createCommand($sql,
            [
                ':rrm'=>$params['pay_left_money'],
                ':pkm'=>$params['packetsMoney'],
                ':wid'=>$wish->wish_id
            ])->execute();
        if($rst <= 0)
        {
            $error = '愿望已经结束，打赏失败';
            throw new Exception($error);
        }
/*        $pay_left_money = $params['pay_left_money'];
        $packetsMoney = $params['packetsMoney'];
        $wish->reward_num += 1;
        //打赏同时，评论数加一
        $wish->comment_num += 1;
        $wish->ready_reward_money = strval(doubleval($wish->ready_reward_money) +  $pay_left_money);
        $wish->red_packets_money = strval( doubleval($wish->red_packets_money) + $packetsMoney);
        if(!$wish->save())
        {
            \Yii::getLogger()->log(var_export($wish->getErrors(), true),Logger::LEVEL_ERROR);
            throw new Exception('愿望信息更新失败');
        }*/
        return true;
    }
} 