<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/22
 * Time: 21:21
 */

namespace frontend\business;


use common\components\SystemParamsUtil;
use common\components\UsualFunForStringHelper;
use common\models\GuessRecord;
use frontend\business\SaveRecordByransactions\SaveByTransaction\AddLivingGuessParamsSaveByTrans;
use frontend\business\SaveRecordByransactions\SaveByTransaction\CreateUserBalanceLogByTrans;
use frontend\business\SaveRecordByransactions\SaveByTransaction\GuessLivingRecordSaveByTrans;
use frontend\business\SaveRecordByransactions\SaveByTransaction\LivingEnterRoomUserByTrans;
use frontend\business\SaveRecordByransactions\SaveByTransaction\ModifyBalanceBySubRealBean;
use frontend\business\SaveRecordByransactions\SaveByTransaction\TicketLivingMasterMoneyTrans;
use yii\log\Logger;

class LivingGuessUtil
{
    /**
     * 根据用户id和房间编号和竞猜类型 获取用户竞猜次数信息
     */
    public static function GetGuessRecord($user_id, $room_id,$guess_type)
    {
        return GuessRecord::findOne(['room_no' => $room_id, 'user_id' => $user_id,'guess_type'=>$guess_type]);
    }

    /**
     * 生成竞猜及相关记录 , 并进行统计
     * @param $LoginInfo
     * @param $guess_type
     * @param $is_ok
     * @param $living_id
     * @param $device_type
     * @param $result
     * @param $error
     * @return bool
     */
    public static function CreateGuessRecord($LoginInfo,$guess_type,$is_ok,$living_id,$device_type,&$result = [],&$error)
    {
        $str_data = [];
        $next_money = '';
        //将直播间信息和用户对该直播间的状态进行查询
        $living = LivingUtil::GetLivingTicketGuess($living_id, $LoginInfo['user_id']);
        if (!isset($living)) //判断直播间是否存在
        {
            $error = '直播间信息不存在';
            \Yii::error($error.' : living_id:'.$living.' and user_id:'.$LoginInfo['user_id']);
            return false;
        }
        if (!in_array($living['living_type'], [3, 4, 5])) //判断直播类型是否正确
        {
            /*$error = '直播类型参数错误';
            \Yii::getLogger()->log($error . '  living_type:' . $living['living_type'], Logger::LEVEL_ERROR);
            return false;*/
            $error = ['errno' => '1115', 'errmsg' => '已经进入过，无需竞猜'];
            return false;
        }

        if ($living['status'] == 0) //是否正在直播
        {
            $error = '直播间已关闭，无法参与竞猜';
            \Yii::error($error . '   living_id :' . $living_id . '  status:' . $living['status']);
            return false;
        }
        //判断用户是否进入过该直播间
        if (!empty($living['private_views']) || !empty($living['ticket_views']))
        {
            $error = ['errno' => '1115', 'errmsg' => '已经进入过，无需竞猜'];
            return false;
        }
        if (!empty($living['limit_num']))
        {
            if ($living['person_count'] >= $living['limit_num'])
            {
                //直播间人数已满
                $error = '直播间人数已满';
                return false;
            }
        }
        $living_type = $living['living_type'];
        $guess_data = LivingGuessUtil::GetGuessRecord($LoginInfo['user_id'], $living['room_no'], $guess_type);
        if ($guess_data->is_ok == 1)
        {
            $error = ['errno' => '1115', 'errmsg' => '已经进入过，无需竞猜'];
            return false;
        }
        if ($living_type == 3 || $living_type == 5)
        {
            $str = SystemParamsUtil::GetSystemParam('guess_living_money', true);
            $str_data = explode(',', $str);
            sort($str_data);
        }
        else
        {
            if ($guess_type == 1)
            {
                $str = SystemParamsUtil::GetSystemParam('guess_ticket_money', true);
                $str_data = explode(',', $str);
                sort($str_data);
            }
        }
        $max_num = intval(count($str_data));
        if($guess_type == 1)
        {
            if ($guess_data->guess_num >= $max_num) //竞猜次数是否用完
            {
                $error = '竞猜次数已经用完。';
                return false;
            }
        }
        $data = [  //初始化竞猜直播记录
            'room_no' => $living['room_no'],
            'living_id' => $living_id,
            'guess_num' => 0,
            'user_id' => $LoginInfo['user_id'],
        ];

        $free_no = intval(array_count_values($str_data)[0]);
        $guess_num = 1;
        if (isset($guess_data) || !empty($guess_data))  //判断竞猜用户记录是否存在
        {
            $guess_num = $guess_data->guess_num + 1;
            $free_no = $guess_data->free_num;
        }
        $params = [
            'unique_id' => UsualFunForStringHelper::CreateGUID(),
            'device_type' => $device_type,
            'relate_id' => '',
            'field' => 'bean_balance'
        ];
        $statistic_param = [
            'guess_num' => 0,
            'ticket_num' => 0,
            'living_id' => $living_id,
            'room_no' => $living['room_no'],
        ];
        if ($living_type == 3 || $living_type == 5)
        {
            if ($guess_type != 1)
            {
                $error = '竞猜类型参数错误 -- 1';
                return false;
            }
            $guess_money = intval($str_data[$guess_num - 1]);
            $params['operate_type'] = '27';
            $statistic_param['guess_num'] = $guess_money;
            $next_money = intval($str_data[$guess_num]);
        }
        else
        {
            if (!in_array($guess_type, [1, 2]))
            {
                $error = '竞猜类型参数错误 -- 2';
                return false;
            }
            if ($guess_type == 1)  //竞猜进入
            {
                $cash = $str_data[$guess_num - 1];
                $next_money = intval(ceil($living['tickets'] * $str_data[$guess_num]));
                $guess_money = intval(ceil($living['tickets'] * $cash));
                $params['operate_type'] = '27';
                $statistic_param['guess_num'] = $guess_money;
            }
            else  //门票进入
            {
                if (intval($guess_data->guess_num > 0))
                {
                    $error = '您已经购买过门票，无需购买';
                    return false;
                }
                $guess_money = $living['tickets'];
                $statistic_param['ticket_num'] = $guess_money;
                $params['operate_type'] = '28';
                $is_ok = 1;
            }
        }

        $params['op_value'] = $guess_money;
        $data['free_num'] = $free_no;
        $data['living_type'] = $living_type;
        $data['guess_type'] = $guess_type;
        $data['guess_money'] = $guess_money;
        $data['is_ok'] = $is_ok;

        //更新用户竞猜数
        if(!self::GuessBalanceManage($data,$params,$statistic_param,$living,$LoginInfo,$error))
        {
            return false;
        }

        $result = [
            'free_no'=> (($free_no > 0) ?  $free_no - 1 : $free_no),
            'guess_num'=> intval($max_num - (($guess_num > $max_num ) ? $max_num : $guess_num)),
            'guess_money'=>intval($next_money),
        ];

        return true;
    }




    /**
     * 根据用户id 和 房间号 获取用户是否竞猜
     * @param $room_no
     * @param $user_id
     */
    public static function IsOfGuess($room_no, $user_id)
    {
        return GuessRecord::findOne(['room_no' => $room_no, 'user_id' => $user_id,'is_ok'=>1]);
    }



    /**
     * 处理竞猜对应财务逻辑 和 相关数据统计
     */
    public static function GuessBalanceManage($data,$params,$statistic_param,$living,$LoginInfo,&$error)
    {
        $transActions[] = new AddLivingGuessParamsSaveByTrans($data);
        if ($data['is_ok'] == 1)
        {
            $enter_params = [
                'room_no'=>$living['room_no'],
                'user_id'=>$LoginInfo['user_id'],
                'living_type'=>$living['living_type'],
            ];
            $transActions[] = new LivingEnterRoomUserByTrans($enter_params);
        }
        if($data['free_num'] <= 0 && $living['living_type'] != 5)
        {
            //扣除相关鲜花数
            $balance = BalanceUtil::GetUserBalanceByUserId($LoginInfo['user_id']);
            if($balance->bean_balance < $data['guess_money'])
            {
                $error = '鲜花余额不足，请充值';
                return false;
            }
            $transActions[] = new ModifyBalanceBySubRealBean($balance,['bean_num'=>$data['guess_money']]);
            //增加相关财务日志
            $transActions[] = new CreateUserBalanceLogByTrans($balance,$params);
            //每场统计 ,日周月统计
            $transActions[] = new GuessLivingRecordSaveByTrans($statistic_param);
            if($data['guess_type'] == 2)
            {
                //收到门票用户票处理  --- 异步处理
                $up_params = [
                    'gift_value'=>$data['guess_money'],
                    'living_master_id'=>$living['to_user_id'],
                ];
                $to_balance = BalanceUtil::GetUserBalanceByUserId($living['to_user_id']);
                $transActions[] = new TicketLivingMasterMoneyTrans($to_balance,$up_params);
                //生成财务日志log
                $extend_params = [
                    'op_value'=>$data['guess_money'],
                    'unique_id'=>UsualFunForStringHelper::CreateGUID(),
                    'relate_id'=>'',
                    'device_type'=>$params['device_type'],
                ];
                $extend_params['field'] = 'ticket_real_sum';
                $extend_params['operate_type'] = 29;
                $transActions[] = new CreateUserBalanceLogByTrans($to_balance,$extend_params);
                $extend_params['field'] = 'ticket_count_sum';
                $transActions[] = new CreateUserBalanceLogByTrans($to_balance,$extend_params);
                $extend_params['field'] = 'ticket_count';
                $transActions[] = new CreateUserBalanceLogByTrans($to_balance,$extend_params);
            }
        }
        if(!SaveByTransUtil::RewardSaveByTransaction($transActions,$error,$out))
        {
            return false;
        }

        return true;
    }
} 