<?php
/**
 * Created by PhpStorm.
 * User: zff
 * Date: 2016/8/17
 * Time: 16:25
 */

namespace frontend\controllers\MblivingActions;


use frontend\business\ActivityUtil;
use yii\base\Action;

class ActivityScoreBoardAction extends Action
{
    public function run()
    {
        $datas = \Yii::$app->request->post();
        $activity_id = $datas['activity_id'];
        $rand_str = $datas['rand_str'];
        $time = $datas['time'];
        $sign = $datas['sign'];
        //加入排行榜类型 1 是主播排行榜  2是用户排行榜
        $score_type = $datas['score_type'];

        $params = [
            'activity_id' => $activity_id,
            'rand_str' => $rand_str,
            'time' => $time
        ];

        $rst = ['code'=>'1','msg'=>''];
        if(!isset($activity_id))
        {
            $rst['msg'] = '活动id不能为空';
            echo json_encode($rst);
            exit;
        }
        //\Yii::getLogger()->log('get_level_info:'.var_export($params,true),Logger::LEVEL_ERROR);
        $sourceSign = ActivityUtil::GetActivitySign($params);
        //\Yii::getLogger()->log('sign:'.$sign,Logger::LEVEL_ERROR);
        if($sourceSign !== $sign)
        {
            $rst['msg'] = '签名不正确';
            echo json_encode($rst);
            exit;
        }

        if(!isset($score_type))
        {
            $rst['msg'] = '排行榜类型不能为空';
            echo json_encode($rst);
            exit;
        }

        if($score_type == 1)
        {
            $scoreboard = ActivityUtil::GetScoreBoardByActivityID($activity_id);
        }
        else if($score_type == 2)
        {
            $scoreboard = ActivityUtil::GetUserScoreBoardByActivityID($activity_id);
        }

        if(empty($scoreboard))
        {
            $error = '活动排行榜信息未找到';
            $rst['msg'] = $error;
            echo json_encode($rst);
            exit;
        }

        $rst['code'] = '0';
        $rst['msg'] = $scoreboard;
        echo json_encode($rst);
        exit;
    }
} 