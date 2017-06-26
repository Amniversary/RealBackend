<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2015/12/16
 * Time: 16:46
 */

namespace frontend\business;
use common\components\SystemParamsUtil;
use common\models\BusinessLog;
use common\models\Message;
use common\models\SignLogin;
use common\models\UserActive;
use yii\base\Exception;
use yii\log\Logger;

/**
 * Class 签名辅助类
 * @package frontend\business
 */
class SignUtil
{
    /**
     * 今天是否签到
     */
    public static function HasSignToday($user_id)
    {
        $rc  = SignLogin::findOne([
            'user_id'=>$user_id,
            'sign_date'=>date('Y-m-d')
        ]);
        return isset($rc);
    }

    /**
     * 昨天是否签到
     * @return bool
     */
    public static function HasPreDaySign($user_id)
    {
        $rc = SignLogin::findOne([
            'and', 'sign_date=date_format(date_add(current_timestamp(),interval -1 day),\'%Y-%m-%d\')',['user_id'=>$user_id]
        ]);
        return isset($rc);
    }

    /**
     * 初始化签到模型并返回
     * @param $user 用户信息
     * @param $device_no
     * @return SignLogin
     */
    public static function GetSignNewModel($user,$device_no)
    {
        $signLogin = new SignLogin();
        $signLogin->phone_no = $user->phone_no;
        $signLogin->device_no = $device_no;
        $signLogin->sign_date = date('Y-m-d');
        $signLogin->create_time = date('Y-m-d H:i:s');
        $signLogin->user_id = $user->account_id;
        $signLogin->nick_name = $user->nick_name;
        return $signLogin;
    }

    public static function SignToday($user,$device_no, &$error)
    {
        $error = '';
        if(self::HasSignToday($user->account_id))
        {
            $error = '您今天已经签过名';
            return false;
        }
        $model = self::GetSignNewModel($user, $device_no);
        $userActive = UserActiveUtil::GetUserActiveByUserId($user->account_id);
        if(!isset($userActive))
        {
            $userActive = UserActiveUtil::GetUserActiveNewModel($user->account_id);
        }
        $default_circle = intval(SystemParamsUtil::GetSystemParam('system_sign_circle',true));
        $yestodaySign = self::HasPreDaySign($user->account_id);
        if($yestodaySign)
        {
                //连续的
            $userActive->sign_count += 1;
            if($userActive->sign_count > 0 && $userActive->sign_count % $default_circle === 0)
            {
                $userActive->sign_circle_count += 1;
            }
            $userActive->sign_count = $userActive->sign_count % $default_circle;
        }
        else
        {
            //非连续的
            $userActive->sign_count = 1;
        }
        $userActive->sign_sum_count += 1;
        $signDays = $userActive->sign_count;
        if($signDays === 0)
        {
            $signDays = 7;
        }
        $sqlInsert = '
insert into my_sign_login ( `phone_no`, `device_no`, `sign_date`, `create_time`, `user_id`, `nick_name`)
 select :pno, :dno,:sdate,:ct,:uid,:nname from my_sign_login
 where not EXISTS (select sign_login_id from my_sign_login where user_id=:uid1 and sign_date=:sdate1) limit 1
        ';
        $trans = \Yii::$app->db->beginTransaction();
        try
        {
            $rst = \Yii::$app->db->createCommand($sqlInsert,[
                ':pno'=>$model->phone_no,
                ':dno'=>$model->device_no,
                ':sdate'=>$model->sign_date,
                ':ct'=>$model->create_time,
                ':uid'=>$model->user_id,
                ':nname'=>$model->nick_name,
                ':uid1'=>$model->user_id,
                ':sdate1'=>$model->sign_date
            ])->execute();
            if($rst <= 0)
            {
                \Yii::getLogger()->log('您今天已经签到过',Logger::LEVEL_ERROR);
                throw new Exception('您今天已经签到过');
            }
/*            if(!$model->save())
            {
                \Yii::getLogger()->log(var_export($userActive->getErrors(), true),Logger::LEVEL_ERROR);
                throw new Exception('签到信息保存失败');
            }*/
            if(!UserActiveUtil::ModifyUseractive('sign',$userActive,$error,['sign_count'=>$userActive->sign_count]))
            {
                \Yii::getLogger()->log(var_export($userActive->getErrors(), true),Logger::LEVEL_ERROR);
                throw new Exception('活跃度信息更新失败');
            }
            $trans->commit();
        }
        catch(Exception $e)
        {
            $error = $e->getMessage();
            $trans->rollBack();
            return false;
        }
        $sign_red_packets_id = intval(SystemParamsUtil::GetSystemParam('system_sign_red_packets',true));
        //\Yii::getLogger()->log('sign_red_packets_id:'.$sign_red_packets_id,Logger::LEVEL_ERROR);
        if(!empty($sign_red_packets_id))
        {
            $redPacketsForSign = RedPacketsUtil::GetRedPacketsById($sign_red_packets_id);
            //\Yii::getLogger()->log(var_export($redPacketsForSign->attributes, true),Logger::LEVEL_ERROR);
            if(isset($redPacketsForSign))
            {
                $params = [
                    'red_packet'=>$redPacketsForSign,
                    'user'=>$user,
                    'sign_days'=>$signDays
                ];
                if(!RedPacketsUtil::SendRedPackets($params, $error))
                {
                    \Yii::getLogger()->log('签到发送红包错误：'.$error,Logger::LEVEL_ERROR);
                }
            }
        }

        return true;
    }
} 