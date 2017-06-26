<?php
/**
 * Created by PhpStorm.
 * User: hlq
 * Date: 16-05-10
 * Time: 下午5:30
 */
namespace frontend\business\SaveRecordByransactions\SaveByTransaction;


use frontend\business\RewardUtil;
use frontend\business\SaveRecordByransactions\ISaveForTransaction;
use yii\db\Query;
use yii\log\Logger;


/**
 * 送礼物主播打赏处理
 */
class LivingMasterRewardByTrans  implements ISaveForTransaction
{
    private  $living_before_id;
    private  $params;
    private  $living_master_id;

    /**
     * @param $living_before_id
     * @param $living_master_id
     * @param $params
     */
    public function __construct($living_before_id,$living_master_id,$params)
    {
        $this->living_before_id = $living_before_id;
        $this->living_master_id = $living_master_id;
        $this->params = $params;
    }

    public function SaveRecordForTransaction(&$error,&$outInfo)
    {
        if(empty($this->living_before_id))
        {
            $error = '直播次数不能为空';
            return false;
        }
        if(empty($this->living_master_id))
        {
            $error = '主播ID不能为空';
            return false;
        }
        //打赏记录表记录
        $sql = 'insert into mb_reward (living_before_id,reward_user_id,living_master_id,gift_id,gift_name,gift_type,gift_value,multiple,total_gift_value,receive_rate,create_time,status,op_unique_no)
values(:lid,:ruid,:luid,:gid,:gname,:gtype,:gvalue,:mul,:total_gift,:rate,:ctime,:tag,:uno)';
        $result = \Yii::$app->db->createCommand($sql,[
            ':lid' =>  $this->living_before_id,
            ':ruid' => $this->params['user_id'],
            ':luid' => $this->living_master_id,
            ':gid' => $this->params['gift_id'],
            ':gname' => $this->params['gift_name'],
            ':gtype' => $this->params['money_type'],
            ':gvalue' => $this->params['gift_value'],
            ':mul' => $this->params['multiple'],
            ':total_gift' => $this->params['total_gift_value'],
            ':rate' => $this->params['receive_rate'],
            ':ctime' => date('Y-m-d H:i:s',time()),
            ':tag' => 1,
            ':uno' => $this->params['op_unique_no']
        ])->execute();

        if($result <= 0){
            \Yii::getLogger()->log('insert_mb_reward_sql=:'.
                \Yii::$app->db->createCommand($sql,[
                    ':lid' =>  $this->living_before_id,
                    ':ruid' => $this->params['user_id'],
                    ':luid' => $this->living_master_id,
                    ':gid' => $this->params['gift_id'],
                    ':gname' => $this->params['gift_name'],
                    ':gtype' => $this->params['money_type'],
                    ':gvalue' => $this->params['gift_value'],
                    ':mul' => $this->params['multiple'],
                    ':total_gift' => $this->params['total_gift_value'],
                    ':rate' => $this->params['receive_rate'],
                    ':ctime' => date('Y-m-d H:i:s',time()),
                    ':tag' => 1,
                    ':uno' => $this->params['op_unique_no']
                ])->rawSql,Logger::LEVEL_ERROR);
            $error = '打赏记录生成失败';
            return false;
        }

        $reward_id = \Yii::$app->db->lastInsertID;
        /*$sql = 'SELECT LAST_INSERT_ID()';
        $reward_id = \Yii::$app->db->createCommand($sql)->queryScalar();*/
        $outInfo['relate_id'] = $reward_id;
        return true;
    }


}