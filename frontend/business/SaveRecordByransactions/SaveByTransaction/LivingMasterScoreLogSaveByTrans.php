<?php
/**
 * Created by PhpStorm.
 * User: hlq
 * Date: 16-05-10
 * Time: 下午5:30
 */
namespace frontend\business\SaveRecordByransactions\SaveByTransaction;


use common\models\LivingMasterScoreBoard;
use frontend\business\SaveRecordByransactions\ISaveForTransaction;
use yii\log\Logger;


/**
 * 主播积分处理日志
 */
class LivingMasterScoreLogSaveByTrans  implements ISaveForTransaction
{
    private  $params;

    /**
     * @param $params    ['gift_id', 'integral', 'send_user_id', 'send_gift_time','score_id','activity_id','living_master_id']
     */
    public function __construct($params)
    {
        $this->params = $params;
    }

    public function SaveRecordForTransaction(&$error,&$outInfo)
    {
        if($this->params['integral'] < 0)
        {
            $error = '积分不能为负数';
            return false;
        }

        if(empty($this->params['score_id']))
        {
            $this->params['score_id'] = $outInfo['score_id'];
        }

        $fields = ['gift_id', 'integral', 'send_user_id', 'send_gift_time','score_id','activity_id','living_master_id'];
        $fields_len = count($fields);
        for($i = 0;$i<$fields_len;$i++)
        {
            if(empty($this->params[$fields[$i]]))
            {
                \Yii::getLogger()->log($fields[$i].'  参数为空  action=LivingMasterScoreLogSaveByTrans',Logger::LEVEL_ERROR);
                 $error = '参数不合法';
                 return false;
            }
        }

        $insert_sql = 'insert into mb_living_master_score_board_log (score_id,activity_id,living_master_id,gift_id,integral,send_user_id,send_gift_time)
values(:sid,:aid,:uid,:gid,:ral,:suid,:stime)';
        $insert_out = \Yii::$app->db->createCommand($insert_sql,[
            ':sid' => $this->params['score_id'],
            ':aid' => $this->params['activity_id'],
            ':uid' => $this->params['living_master_id'],
            ':gid' => $this->params['gift_id'],
            ':ral' => $this->params['integral'],
            ':suid' => $this->params['send_user_id'],                  //送礼物用户id
            ':stime' => $this->params['send_gift_time'],
        ])->execute();
        if($insert_out <= 0)
        {
            $error = '主播积分日志写入失败';
            \Yii::getLogger()->log(\Yii::$app->db->createCommand($insert_sql,[
                ':sid' => $this->params['score_id'],
                ':aid' => $this->params['activity_id'],
                ':uid' => $this->params['living_master_id'],
                ':gid' => $this->params['gift_id'],
                ':ral' => $this->params['integral'],
                ':suid' => $this->params['send_user_id'],                  //送礼物用户id
                ':stime' => $this->params['send_gift_time'],
            ])->rawSql,Logger::LEVEL_ERROR);
            return false;
        }
        return true;
    }


}