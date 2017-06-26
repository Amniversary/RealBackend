<?php
namespace frontend\business\SaveRecordByransactions\SaveByTransaction;


use frontend\business\BalanceUtil;
use frontend\business\ClientUtil;
use frontend\business\LivingUtil;
use frontend\business\SaveRecordByransactions\ISaveForTransaction;
use frontend\business\TicketsUtil;
use yii\db\Query;
use common\components\tenxunlivingsdk\TimRestApi;
use frontend\business\JobUtil;
use yii\log\Logger;

/**
 * 发送礼物事务处理
 */
class SendGiftSaveForReward implements ISaveForTransaction
{
    private  $params=[];

    /**
     * @param $params   所要插入的数据
     * @throws Exception
     */
    public function __construct($params)
    {
        $this->params = $params;
    }

    function SaveRecordForTransaction(&$error,&$outInfo)
    {
        //插入直播票数
        if($this->params['money_type'] == 1)
        {  //实际豆
            if($this->params['living_tickets_id'] <= 0){
                $sql = 'insert ignore into mb_living_tickets (living_id,tickets_num,tickets_real_num) values(:lid,:tnum,:trnum)';
            }else{
                $sql = 'update mb_living_tickets set tickets_num=tickets_num+:tnum,tickets_real_num=tickets_real_num+:trnum where living_id=:lid';
            }

            $result = \Yii::$app->db->createCommand($sql,[
                ':lid' => $this->params['living_id'],
                ':tnum' => $this->params['gift_value'],
                ':trnum' => $this->params['gift_value']
            ])->execute();
            if($result <= 0){
                $error = '直播票数插入失败';
                \Yii::getLogger()->log('直播票数插入失败  '.\Yii::$app->db->createCommand($sql,[
                        ':lid' => $this->params['living_id'],
                        ':tnum' => $this->params['gift_value'],
                        ':trnum' => $this->params['gift_value']
                    ])->rawSql,Logger::LEVEL_ERROR);
                return false;
            }


        }
        else
        {
            if($this->params['living_tickets_id'] <= 0){
                $sql = 'insert ignore into mb_living_tickets (living_id,tickets_num,tickets_real_num) values(:lid,:tnum,0)';
            }else{
                $sql = 'update mb_living_tickets set tickets_num=tickets_num+:tnum where living_id=:lid';
            }

            $result = \Yii::$app->db->createCommand($sql,[
                ':lid' => $this->params['living_id'],
                ':tnum' => $this->params['gift_value'],
            ])->execute();
            if($result <= 0){
                $error = '直播票数插入失败';
                \Yii::getLogger()->log('直播票数插入失败2  '.\Yii::$app->db->createCommand($sql,[
                        ':lid' => $this->params['living_id'],
                        ':tnum' => $this->params['gift_value'],
                    ])->rawSql,Logger::LEVEL_ERROR);
                return false;
            }
        }


        return true;

    }
}