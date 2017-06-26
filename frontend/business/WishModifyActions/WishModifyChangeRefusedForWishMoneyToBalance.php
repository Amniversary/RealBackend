<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/1/3
 * Time: 16:07
 */

namespace frontend\business\WishModifyActions;


use common\models\Wish;
use yii\base\Exception;
use yii\log\Logger;

class WishModifyChangeRefusedForWishMoneyToBalance implements IWishModify
{
    public function WishModify($wish,&$error,$params=[])
    {
        if(!($wish instanceof Wish))
        {
            $error='不是愿望对象';
            return false;
        }
        if(!isset($params['cancel_wish']))
        {
            $error = '是否取消愿望状态不能为空';
            return false;
        }
        $cancel_wish = $params['cancel_wish'];
        if(!isset($params['back_money']))
        {
            $error = '取消愿望后退狂状态不能为空';
            return false;
        }
        $back_money = $params['back_money'];
        //来到这里都是已经实现的愿望
        $sql = 'update my_wish set to_balance = 1';
        if($cancel_wish == '1')
        {
            $sql .= ',status=0,finish_status=1,is_finish=1,wish_money=(ready_reward_money + red_packets_money + 1)';//设置取消愿望
            if($back_money == '1')
            {
                $sql .= ',back_status=3';//设置已退款
            }
            else
            {
                $sql .= ',back_status=2';//设置成退款中
            }
        }

        $sql .= ' where wish_id=:wid and is_finish=2 and status=1 and to_balance = 3 and back_status = 1 and finish_status < 3
        ';
        $rst = \Yii::$app->db->createCommand($sql,[
            ':wid'=>$wish->wish_id
        ])->execute();
        if($rst <= 0)
        {
            $error = '愿望状态错误，执行失败';
            \Yii::getLogger()->log($error.' sql:'.$sql,Logger::LEVEL_ERROR);
            throw new Exception($error);
        }
        return true;
    }
} 