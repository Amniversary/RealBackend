<?php
/**
 * H5活动抽奖，执行抽奖接口
 * User: hlq
 * Date: 2016/5/3
 * Time: 14:58
 */
namespace frontend\controllers\MblivingActions;
use common\models\ActivityChance;
use common\models\ActivityPrize;
use frontend\business\ActivityChanceUtil;
use frontend\business\ActivityUtil;
use frontend\business\ApiCommon;
use frontend\business\JobUtil;
use frontend\business\SaveByTransUtil;
use frontend\business\SaveRecordByransactions\SaveByTransaction\ActivityPrizeNumberSaveByTrans;
use yii\base\Action;
use yii\log\Logger;

class MbWebDoActivityPrizeAction extends Action
{
    public function run($activity_id,$unique_no,$rand_str,$sign,$time)
    {
        $error_msg = 'ok';
        $params['activity_id'] = $activity_id;
        $params['unique_no'] = $unique_no;
        $params['rand_str'] = $rand_str;
        $params['time'] = $time;
        if(!isset($unique_no))
        {
            $arr_data = ['error_msg' => '用户唯一号不能为空'];
            echo  json_encode($arr_data);
            exit;
        }

        if(!isset($activity_id))
        {
            $arr_data = ['error_msg' => '活动标识不能为空'];
            echo  json_encode($arr_data);
            exit;
        }

        if($unique_no == '@unique_new')
        {
            $arr_data = ['error_msg' => '请下载蜜播App!'];
            echo  json_encode($arr_data);
            exit;
        }

        $sourceSign = ActivityUtil::GetActivitySign($params);
        if($sourceSign !== $sign)
        {
            $arr_data = ['error_msg' => '抽奖签名信息错误'];
            echo  json_encode($arr_data);
            exit;
        }

        if(!ApiCommon::GetLoginInfo($unique_no, $LoginInfo, $error))
        {
            $arr_data = ['error_msg' => $error];
            echo  json_encode($arr_data);
            exit;
        }

        $activity_info = ActivityUtil::GetActivityInfoById($activity_id);
        if(!isset($activity_info) || empty($activity_info))
        {
            $arr_data = ['error_msg' => '活动不存在'];
            echo  json_encode($arr_data);
            exit;
        }

        $date = date('Y-m-d');
        if(($activity_info->start_time > $date) || ($activity_info->status == 1))
        {
            $arr_data = ['error_msg' => '活动还未开始'];
            echo  json_encode($arr_data);
            exit;
        }
        if(($activity_info->end_time < $date) || ($activity_info->status == 0))
        {
            $arr_data = ['error_msg' => '活动已经结束了'];
            echo  json_encode($arr_data);
            exit;
        }

        $activity_chance = ActivityChance::findOne(['user_id' => $LoginInfo['user_id']]);
        if($activity_chance->number <= 0)
        {
            $arr_data = ['error_msg' => '抽奖次数已经用完了'];
            echo  json_encode($arr_data);
            exit;
        }

        $lottery_info = ActivityChanceUtil::DoLottery($activity_info->activity_id,$error);  //执行抽奖方法，返回中奖的奖品信息
        if(empty($lottery_info))
        {
            $arr_data = ['error_msg' => $error];
            echo  json_encode($arr_data);
            exit;
        }

        $activity_prize = ActivityPrize::findOne(['prize_id' => $lottery_info['prize_id']]);

        if($activity_prize->last_number <= 0)
        {
            $arr_data = ['error_msg' => '网络错误，请重试！'];
            echo  json_encode($arr_data);
            exit;
        }
        $transaction = [];
        $ex_params = [
            'user_id' => $LoginInfo['user_id'],
            'prize_id' => $activity_prize['prize_id']
        ];
        $transaction[] = new ActivityPrizeNumberSaveByTrans($ex_params);
        if(!SaveByTransUtil::RewardSaveByTransaction($transaction,$error,$outInfo))       //更新礼物剩余份数、用户抽奖次数
        {
            \Yii::getLogger()->log('网络错误，请重试！===:'.$error,Logger::LEVEL_ERROR);
            $arr_data = ['error_msg' => '网络错误，请重试！'];
            echo  json_encode($arr_data);
            exit;
        }

        //礼物分发队列
        $prize_data = [
                'key_word' => 'send_prize_info',
            'activity_id' => $activity_info->activity_id,
            'grade' => $lottery_info['grade'],
            'user_id' => $LoginInfo['user_id']
        ];
        if(!JobUtil::AddCustomJob('ActivityChanceBeanstalk','activity_prize_send',$prize_data,$error))
        {
            $arr_data = ['error_msg' => '网络错误，请重试！'];
            echo  json_encode($arr_data);
            exit;
        }

        //礼物统计列表
        $prize_data = [
            'activity_id' => $activity_info->activity_id,
            'field' => 'record_id'
        ];
        if(!JobUtil::AddCustomJob('ActivityChanceBeanstalk','activity_statistic',$prize_data,$error))
        {
            $arr_data = ['error_msg' => '网络错误，请重试！'];
            echo  json_encode($arr_data);
            exit;
        }

        $arr_data = [
            'error_msg' => $error_msg,
            'activity_prize' => $lottery_info
        ];
        echo  json_encode($arr_data);
        exit;
    }
}




