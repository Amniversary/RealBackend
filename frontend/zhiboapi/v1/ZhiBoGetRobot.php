<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/12
 * Time: 14:44
 */

namespace frontend\zhiboapi\v1;


use frontend\business\ApiCommon;
use frontend\business\ChatPersonGroupUtil;
use frontend\business\RobotUtil;
use frontend\zhiboapi\IApiExcute;
use yii\log\Logger;

/**
 * 获取机器人
 * Class ZhiBoRobot
 * @package frontend\zhiboapi\v3
 */
class ZhiBoGetRobot implements IApiExcute
{
    private function check_param_ok($dataProtocal, &$error = '')
    {
        $fields = ['living_id'];
        $fieldLabels = ['直播间id'];
        $len = count($fields);
        for($i = 0; $i < $len; $i++)
        {
            if(!isset($dataProtocal['data'][$fields[$i]]) || empty($dataProtocal['data'][$fields[$i]])) {
                $error = $fieldLabels[$i] . '不能为空';
                return false;
            }
        }
        return true;
    }

    function excute_action($dataProtocal, &$rstData,&$error, $extendData= array())
    {
        if(!$this->check_param_ok($dataProtocal,$error))
        {
            return false;
        }
        $unique_no = $dataProtocal['data']['unique_no'];
        if(!ApiCommon::GetLoginInfo($unique_no,$loginInfo,$error))
        {
            return false;
        }
        $living_id = $dataProtocal['data']['living_id'];
        $user_id = $loginInfo['user_id'];
        $chat_group = ChatPersonGroupUtil::GetLivingOwner($living_id,$user_id);
        $robot = RobotUtil::GetRobotPeopleParams($chat_group['living_master_id']);
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

        //\Yii::getLogger()->log('robot_rst:  living_id :'.$living_id.'_____'.var_export($rst,true),Logger::LEVEL_ERROR);
        $rstData['has_data'] = '1';
        $rstData['data_type'] = 'json';
        $rstData['data'] = $rst;
        return true;
    }
}