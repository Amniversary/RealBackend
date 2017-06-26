<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/10/14
 * Time: 14:43
 */

namespace common\components;


use frontend\business\JobUtil;

class GameRebotsHelper
{
    /**
     * 获取机器人客户端，默认1个
     * @param int $num
     */
    public static function GetRebots($num = 1)
    {
        $rst = [];
        for($i =1; $i <= $num; $i ++)
        {
            if(JobUtil::GetCustomJob('gameRebotBeanstalk','game_rebot_in',$rebot,$error) === false)
            {
                return $rst;
            }
            else
            {
                $rst[]= json_decode(json_encode($rebot),true);
            }

        }
        return $rst;
    }

    /**
     * 生成机器人
     * @param $rebotsList array
     * [
     *   [
     *      'client_id'=>'2',
     *      'nick_name'=>'陈天浩',
     *      'pic'=>'http://dssdsff/sfad.jpg',//小图标即可
     *      'sex'=>'男'
     *   ],
     * .
     * .
     * .
     * .
     * ]
     * @return bool
     */
    public static function GenRebots($rebotsList,&$error='')
    {
        if(!isset($rebotsList) || empty($rebotsList))
        {
            return true;
        }
        foreach($rebotsList as $rebot)
        {
            if(!JobUtil::AddCustomJob('gameRebotBeanstalk','game_rebot_in',$rebot,$error))
            {
                return false;
            }
        }
        return true;
    }


    /**
     * 获取队列 job , 默认1个
     * @param $num
     * @param $jobServer
     * @param $tube
     */
    public static function GetJobDates($jobServer, $tube,$num = 1)
    {
        $rst = [];
        for($i = 0 ; $i <= $num ; $i++ )
        {
            if(JobUtil::GetCustomJob($jobServer,$tube,$data,$error) === false)
            {
                return $rst;
            }
            else
            {
                $rst = json_decode(json_encode($data),true);
            }
        }

        return $rst;
    }
}