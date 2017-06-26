<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/22
 * Time: 18:50
 */

namespace frontend\controllers\MblivingActions;


use frontend\business\ActivityUtil;
use frontend\business\ApiCommon;
use frontend\business\ClientUtil;
use yii\base\Action;
use yii\log\Logger;

class MbLuckyDrawInfoAction extends Action
{
    public function run($activity_id,$rand_str,$time,$sign,$unique_no)
    {

        $params['activity_id'] = $activity_id;
        $params['unique_no'] = $unique_no;
        $params['rand_str'] = $rand_str;
        $params['time'] = $time;
        $rst = ['code'=>'1','msg'=>''];
        if(!isset($unique_no))
        {
            $rst['msg'] = '用户唯一号不能为空';
            echo json_encode($rst);
            exit;
        }

        if(!isset($activity_id))
        {
            $rst['msg'] = '活动标识不能为空';
            echo json_encode($rst);
            exit;
        }

        $activity_info = ActivityUtil::GetActivityInfoById($activity_id);
        if(!isset($activity_info))
        {
            $rst['msg'] = '活动记录不存在';
            echo json_encode($rst);
            exit;
        }

        $prize_info = ActivityUtil::GetActivityPrizeInfo($activity_id);
        /*$test = array_keys($prize_info);
        shuffle($test);
        $data = [];   //需修改 根据奖品排序 order_no
        foreach($test as $list)
        {
            $data[$list] = $prize_info[$list]['gift_name'];
        }*/
        $rst['prize_info'] = $prize_info;

        if($unique_no == '@unique_new')
        {
            $rst['msg'] = '请下载蜜播App!';
            echo json_encode($rst);
            exit;
        }

        $sourceSign = ActivityUtil::GetActivitySign($params);
        if($sourceSign !== $sign)
        {
            $rst['msg'] = '抽奖签名信息错误';
            echo json_encode($rst);
            exit;
        }

        if(!ApiCommon::GetLoginInfo($unique_no, $LoginInfo, $error))
        {
            $rst['msg'] = $error;
            if(is_array($error))
            {
                $rst['msg'] = $error['errmsg'];
            }
            echo json_encode($rst);
            exit;
        }

        $date = date('Y-m-d');
        if(($activity_info->status == 0) || ($activity_info->end_time < $date))
        {
            $rst['msg'] = '活动已结束!';
            echo json_encode($rst);
            exit;
        }
        if(($activity_info->status == 1) || ($activity_info->start_time > $date))
        {
            $rst['msg'] = '活动未开始!';
            echo json_encode($rst);
            exit;
        }
        $prize_draw = ActivityUtil::GetWinningInfo($activity_id,$LoginInfo['user_id']);
        $user_chance = ActivityUtil::GetActivityUserChance($activity_id,$LoginInfo['user_id']);
        $rst['user_info'] = $prize_draw;
        $rst['lucky_draw'] = '0';
        if(isset($user_chance))
        {
            $rst['lucky_draw'] = $user_chance->number;
        }

        $rst['code'] = '0';
        echo json_encode($rst);
    }
} 