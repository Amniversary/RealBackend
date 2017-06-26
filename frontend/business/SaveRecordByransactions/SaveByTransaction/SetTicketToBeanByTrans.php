<?php
namespace frontend\business\SaveRecordByransactions\SaveByTransaction;


use frontend\business\SaveRecordByransactions\ISaveForTransaction;
use frontend\business\ToBeanGoodsUtil;

/**
 * 票转豆操作
 * Class SetTicketToBeanByTrans
 * @package frontend\business\SaveRecordByransactions\SaveByTransaction
 */
class SetTicketToBeanByTrans implements ISaveForTransaction
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
        $sql = 'insert ignore into mb_ticket_to_bean (user_id,ticket_num,bean_num,status,create_time,op_unique_no)
values(:uid,:tnum,:bnum,:tag,:ctime,:uno)';

        $update_sql = 'update mb_ticket_to_bean set ticket_num=:tnum,bean_num=:bnum,create_time=:ctime where user_id=:uid';
        $result = \Yii::$app->db->createCommand($sql,[
            ':uid' => $this->params['user_id'],
            ':bnum' => $this->params['bean_num'],
            ':tnum' => $this->params['ticket_num'],
            ':tag' => 1,
            ':ctime' => 0,
            ':uno' => $this->params['op_unique_no']
        ])->execute();

        $update_result = \Yii::$app->db->createCommand($update_sql,[
            ':uid' => $this->params['user_id'],
            ':bnum' => $this->params['bean_num'],
            ':tnum' => $this->params['ticket_num'],
            ':ctime' => date('Y-m-d H:i:s'),
        ])->execute();

        if($result === false){
            $error = '账户豆插入失败';
        }

        if($update_result === false){
            $error = '账户豆修改失败';
        }

        $bean_info = ToBeanGoodsUtil::GetBeanGoodsById($this->params['user_id']);

        $outInfo['record_id'] = $bean_info->record_id;

        return true;

    }
}