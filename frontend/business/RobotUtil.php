<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/12
 * Time: 14:49
 */

namespace frontend\business;


use common\components\PhpLock;
use common\components\SystemParamsUtil;
use common\models\ClientRobotinfo;
use yii\log\Logger;

use frontend\business\ChatPersonGroupUtil;

class RobotUtil
{
    /**
     * 从缓存中获取系统参数, 没有则从数据库中获取直播机器人参数
     * @param bool $reflash 是否刷新
     * @param $user_id
     * @return mixed
     * [
     * 'create_robot_no'=>1,//主播直播时机器人数量 0表示没有机器人
     * 'audience_robot_no'=>2//观众进入直播间带上的机器人数量 0 表示没有机器人
     * ]
     */
    public static function GetRobotPeopleParams($user_id,$reflash = false)
    {
        $is_open = SystemParamsUtil::GetSystemParam('system_robot_switch',false,'value2');
        if($is_open == '1')
        {
            if($reflash)
            {
                $robot = self::GetUserRobotInfo($user_id);
                $data = [
                    'create_robot_no'=>$robot->create_robot_no,
                    'audience_robot_no'=>$robot->audience_robot_no,
                ];
                if(!isset($robot))
                {
                    $robot = SystemParamsUtil::GetSystemParam('system_robot_params',true,null);
                    $data['create_robot_no'] = $robot['value1'];
                    $data['audience_robot_no'] = $robot['value2'];
                }
                $rst = $data;
                $data = serialize($data);
                \Yii::$app->cache->set('get_robot_params_'.$user_id,$data);
            }
            else
            {
                $cnt = \Yii::$app->cache->get('get_robot_params_'.$user_id);
                if($cnt === false)
                {
                    $lock = new PhpLock('get_robot_people_'.$user_id);
                    $lock->lock();
                    $cnt = \Yii::$app->cache->get('get_robot_params_'.$user_id);
                    if($cnt === false)
                    {
                        $robot = self::GetUserRobotInfo($user_id);
                        $data = [
                            'create_robot_no'=>$robot->create_robot_no,
                            'audience_robot_no'=>$robot->audience_robot_no,
                        ];
                        if(!isset($robot))
                        {
                            $robot = SystemParamsUtil::GetSystemParam('system_robot_params',true,null);
                            $data['create_robot_no'] = $robot['value1'];
                            $data['audience_robot_no'] = $robot['value2'];
                        }
                        $rst = $data;
                        $data = serialize($data);

                        \Yii::$app->cache->set('get_robot_params_'.$user_id,$data);
                    }
                    else
                    {
                        $rst = unserialize($cnt);
                    }
                    $lock->unlock();
                }
                else
                {
                    $rst = unserialize($cnt);
                }
            }
        }
        else
        {
            $rst = [
                'create_robot_no'=>0,
                'audience_robot_no'=>0
            ];
        }

        return $rst;
    }

    /**
     * 获取用户机器人参数
     * @param $user_id
     * @return null|static
     */
    public static function GetUserRobotInfo($user_id)
    {
        return ClientRobotinfo::findOne(['user_id'=>$user_id]);
    }

    /**
     * 更新直播间的信息时，获取机器人信息
     * @param $living_id
     * @param $user_id
     * @return array
     */
    public static function GetRobotInfoForUpdateLiving( $living_id ,$user_id )
    {

        $chat_group = ChatPersonGroupUtil::GetLivingOwner($living_id,$user_id);
        $robot = self::GetRobotPeopleParams($chat_group['living_master_id']);
        $robot_info = [];
        $ceil = 0;
        $back_robot = 0;
        switch(intval($chat_group['owner']))
        {
            case 1:
                $ceil = intval(($robot['create_robot_no'] + 54)/55);
                $back_robot = $robot['create_robot_no'];
                break;
            case 3:
                $ceil = intval(($robot['audience_robot_no'] + 54)/55);
                $back_robot = $robot['audience_robot_no'];
                break;
            default:
                $error = '不正确的用户类型';
                return false;
        }

        for($i = 0; $i < $ceil ; $i++)    //  从1000个缓存中获取55个机器人,直到55个或大于55个
        {
            $num = rand(1,1000);
            $robot_num = \Yii::$app->cache->get('living_robot_'.$num);
            $robot_num = json_decode($robot_num,true);
            if(count($robot_num) <= 0)
            {
                $i--;
                continue;
            }
            $robot_info = array_merge($robot_info,$robot_num);  // 组合到数组中去
        }
        $rand_robot_len = count($robot_info) -1;   // 从缓存中统计获取的机器人个数
        $time_count = 200;
        $isset_robot = [];
        $time_out = 0;
        //var_dump($back_robot);
        for($j = 0; $j < $back_robot;$j ++)  // 主播创建直播时 进入的机器人
        {
            $time_out++;
            if($time_out == $time_count)
            {
                break;
            }

            $suiji = rand(0,$rand_robot_len);
            if($chat_group['living_master_id'] == $robot_info[$suiji]['user_id'])   //过滤主播本身的机器人
            {
                $j--;
                continue;
            }
            $robot_user_id = $robot_info[$suiji]['user_id'];
            if(isset($isset_robot[$robot_user_id]))
            {
                $j--;
                continue;
            }
            $isset_robot[$robot_user_id] = 1;
            $rst[] = $robot_info[$suiji];
        }

        return $rst;
    }
} 