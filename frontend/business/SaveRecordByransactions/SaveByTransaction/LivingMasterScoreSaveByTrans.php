<?php
/**
 * Created by PhpStorm.
 * User: hlq
 * Date: 16-05-10
 * Time: 下午5:30
 */
namespace frontend\business\SaveRecordByransactions\SaveByTransaction;


use frontend\business\SaveRecordByransactions\ISaveForTransaction;
use yii\db\Query;
use yii\log\Logger;


/**
 * 活动排行榜积分处理
 */
class LivingMasterScoreSaveByTrans  implements ISaveForTransaction
{
    private  $living_master_id;
    private  $activity_id;
    private  $integral;
    private  $send_user_id;

    /**
     * @param $living_master_id
     * @param $activity_id
     * @param $integral
     */
    public function __construct($living_master_id,$send_user_id,$activity_id,$integral)
    {
        $this->living_master_id = intval($living_master_id);
        $this->send_user_id = intval($send_user_id);
        $this->activity_id = intval($activity_id);
        $this->integral = intval($integral);
    }

    public function SaveRecordForTransaction(&$error,&$outInfo)
    {
        if(empty($this->living_master_id) || empty($this->activity_id) || empty($this->integral))
        {
            \Yii::getLogger()->log('积分参数为空  living_master_id===:'.$this->living_master_id.'    activity_id===:'.$this->activity_id.'   integral===:'.$this->integral,Logger::LEVEL_ERROR);
            $error = '积分参数为空';
            return false;
        }

        if($this->integral < 0)
        {
            $error = '积分不能为负数';
            return false;
        }

        //主播
        $InsertSql = 'insert ignore into mb_living_master_score_board (living_master_id,activity_id,total_integral) values(:uid,:aid,:total)';
        $UpdateSql = 'update mb_living_master_score_board set total_integral=total_integral+:total where living_master_id=:uid and activity_id=:aid';

        $res_insert = \Yii::$app->db->createCommand($InsertSql,[
            ':uid' => $this->living_master_id,
            ':aid' => $this->activity_id,
            ':total' => 0
        ])->execute();

//        $score_id = (new Query())->select(['score_id'])->from('mb_living_master_score_board')->where('living_master_id=:uid and activity_id=:aid',[
//            ':uid' => $this->living_master_id,
//            ':aid' => $this->activity_id,
//        ])->one();

        $sql = 'select score_id from mb_living_master_score_board where living_master_id=:uid and activity_id=:aid';
        $score_id = \Yii::$app->db->createCommand($sql,[
            ':uid' => $this->living_master_id,
            ':aid' => $this->activity_id,
        ])->queryOne();

        $res_update = \Yii::$app->db->createCommand($UpdateSql,[
            ':uid' => $this->living_master_id,
            ':aid' => $this->activity_id,
            ':total' => $this->integral
        ])->execute();
        if($res_update <= 0)
        {
            $error = '主播积分修改失败';
            \Yii::getLogger()->log(\Yii::$app->db->createCommand($UpdateSql,[
                ':uid' => $this->living_master_id,
                ':aid' => $this->activity_id,
                ':total' => $this->integral
            ])->rawSql,Logger::LEVEL_ERROR);
            return false;
        }

        //用户
        $InsertUserSql = 'insert ignore into mb_living_user_score_board (send_user_id,activity_id,total_integral) values(:uid,:aid,:total)';
        $UpdateUserSql = 'update mb_living_user_score_board set total_integral=total_integral+:total where send_user_id=:uid and activity_id=:aid';

        \Yii::$app->db->createCommand($InsertUserSql,[
            ':uid' => $this->send_user_id,
            ':aid' => $this->activity_id,
            ':total' => 0
        ])->execute();

        $res_user_update = \Yii::$app->db->createCommand($UpdateUserSql,[
            ':uid' => $this->send_user_id,
            ':aid' => $this->activity_id,
            ':total' => $this->integral
        ])->execute();
        if($res_user_update <= 0)
        {
            $error = '主播积分修改失败';
            \Yii::getLogger()->log(\Yii::$app->db->createCommand($UpdateUserSql,[
                ':uid' => $this->living_master_id,
                ':aid' => $this->activity_id,
                ':total' => $this->integral
            ])->rawSql,Logger::LEVEL_ERROR);
            return false;
        }

        $outInfo['score_id'] = $score_id['score_id'];
        return true;
    }


}