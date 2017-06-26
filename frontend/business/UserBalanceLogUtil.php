<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/4/5
 * Time: 15:01
 */

namespace frontend\business;


use common\components\UsualFunForStringHelper;
use common\models\Balance;
use common\models\UserAccountInfo;
use yii\base\Exception;
use yii\log\Logger;

class UserBalanceLogUtil
{
    /**
     * 生成余额操作日志
     *
     * @param $userAccountInfo
     * @param $op_money
     * @param $operateType         //
     * 1 充值，豆增加
     * 2票转豆，票减少
     * 3票转豆，豆增加
     * 4 票提现，票减少
     * 5 赠送虚拟豆，虚拟豆增加
     * 6 送礼物，豆较少
     * 7 收礼物，票增加
     * 8 送虚拟礼物，虚拟豆减少
     * 9 收虚拟礼物，虚拟豆增加
     * 10 送出的礼物，送出的票增加
     * 11 弹幕扣除虚拟豆
     * 12 弹幕扣除实际豆
     * 13第三方修改增加虚拟豆
     * 14第三方修改增加实际豆
     * @param $field string 涉及到的mb_balance表的字段
     * @param $error
     * @param string $unique_id
     * @return bool
     * @throws Exception
     */
    public static function CreateBalanceLog($balance,$device_type, $op_value,$operate_type,$field,&$error,$unique_id='',$relate_id='')
    {
        if(empty($unique_id))
        {
            $error = '唯一操作码不能为空';
            return false;
        }
        if(!($balance instanceof Balance))
        {
            $error = '不是用户账户记录';
            return false;
        }
        if(!in_array($device_type,['1','2','3']))
        {
            $error = '设备类型不正确';
            return false;
        }
        $fields = ['bean_balance', 'ticket_count', 'ticket_real_sum', 'ticket_count_sum', 'virtual_ticket_count','send_ticket_count','virtual_bean_balance'];
        if(!in_array($field, $fields))
        {
            $error = 'field字段不合法';
            return false;
        }
        //1 充值，豆增加
        //2票转豆，票减少
        //3票转豆，豆增加
        //4 票提现，票减少
        //5 赠送虚拟豆，虚拟豆增加
        //6 送礼物，豆减少
        //7 收礼物，票增加
        //8 送虚拟礼物，虚拟豆减少
        //9 收虚拟礼物，虚拟豆增加
        //10 送出的礼物，送出的票增加
        //11 弹幕扣除虚拟豆
        //12 弹幕扣除实际豆
        //13 退款，票增加
        //14第三方修改增加虚拟豆
        //15第三方修改增加实际豆
        //16第三方修改减少虚拟豆
        //17 发红包豆减少
        //18 收红包都增加
        //19 腿红包都增加
        //20第三方修改减少实际豆
        //21打赏红包照片 减少实际豆
        //22收红包照片打赏 增加票
        //23观众分享获得豆
        //24幸运礼物获得豆
        //25活动中奖 获得豆
        //26点赞，实际豆减少
        //27竞猜密码,实际豆减少
        //28购买门票,实际豆减少
        //29收到门票,增加票
        //30第三方修改增加可提现票数
        //31第三方修改减少可提现票数
        if(!in_array(intval($operate_type),[1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31]))
        {
            $error = '操作类型不合法';
            return false;
        }
        $flag = '';
        $flag2 = '-';
        if(in_array(intval($operate_type),[2,4,6,8,11,12,16,17,20,21,26,27,28,31]))
        {
            $flag = '-';
            $flag2 = '+';
        }
//                $insertSql = '
//insert into mb_client_balance_log ( `device_type`, `user_id`, `balance_id`, `operate_type`, `operate_value`, `before_balance`, `after_balance`, `create_time`, `unique_op_id`,`relate_id`)
//select :dtype , :uid1,:uaid,:ot,'.$flag.' :om,'.$field.' + :om1,'.$field.',now(),:uqid,:relateid from mb_balance where user_id=:uid limit 1
//        ';

        $insertSql = '
insert into mb_client_balance_log ( `device_type`, `user_id`, `balance_id`, `operate_type`, `operate_value`, `before_balance`, `after_balance`, `create_time`, `unique_op_id`,`relate_id`,`remark1`)
select :dtype , :uid1,:uaid,:ot,'.$flag.' :om,'.$field.$flag2.' :om1'.','.$field.',now(),:uqid,:relateid,:remark1 from mb_balance where user_id=:uid limit 1';
        try
        {
            $rst = \Yii::$app->db->createCommand($insertSql,[
                ':dtype'=>$device_type,
                ':uid1'=>$balance->user_id,
                ':uaid'=>$balance->balance_id,
                ':ot'=>$operate_type,
                ':om'=>$op_value,
                ':om1'=>$op_value,
                ':uqid'=>$unique_id,
                ':uid'=>$balance->user_id,
                ':relateid' => $relate_id,
                ':remark1' => $field,
            ])->execute();

        }
        catch(Exception $e)
        {
            $error = '保存账户余额日志失败';
            \Yii::getLogger()->log($error.' '.$e->getMessage(),Logger::LEVEL_ERROR);
            throw new Exception($error);
        }
        if($rst <= 0)
        {
            $error = '插入账户日志记录失败';
            return false;
        }

        return true;
    }
} 