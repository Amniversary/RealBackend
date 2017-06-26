<?php
namespace frontend\business\SaveRecordByransactions\SaveByTransaction;



use frontend\business\SaveRecordByransactions\ISaveForTransaction;
use yii\log\Logger;


/**
 * 牛牛游戏外围用户信息设置
 * Class NiuNiuGameGrabPeripherySaveByTrans
 * @package frontend\business\SaveRecordByransactions\SaveByTransaction
 */
class NiuNiuGameGrabPeripherySaveByTrans implements ISaveForTransaction
{
    private  $data=[];

    /**
     * @param $data
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    function SaveRecordForTransaction(&$error,&$outInfo)
    {
        $sql = 'insert into mb_game_periphery (user_id,game_id,base_num,multiple,seat_num,is_win,win_money_num) values(:uid,:gid,:bnum,:mul,:snum,:iwin,:wmnum)';
        $result = \Yii::$app->db->createCommand($sql,[
            ':gid' => $this->data['game_id'],
            ':uid' => $this->data['user_id'],
            ':bnum' => $this->data['base_num'],
            ':mul' => $this->data['multiple'],
            ':snum' => $this->data['seat_num'],
            ':iwin' => $this->data['is_win'],
            ':wmnum' => $this->data['win_money_num']
        ])->execute();
        if($result <= 0)
        {
            \Yii::getLogger()->log('用户外围信息写入失败  $sql===:'.\Yii::$app->db->createCommand($sql,[
                    ':gid' => $this->data['game_id'],
                    ':uid' => $this->data['user_id'],
                    ':bnum' => $this->data['base_num'],
                    ':mul' => $this->data['multiple'],
                    ':snum' => $this->data['seat_num'],
                    ':iwin' => $this->data['is_win'],
                    ':wmnum' => $this->data['win_money_num']
                ])->rawSql,Logger::LEVEL_ERROR);
            $error = '用户外围信息写入失败';
            return false;
        }

        return true;
    }

}