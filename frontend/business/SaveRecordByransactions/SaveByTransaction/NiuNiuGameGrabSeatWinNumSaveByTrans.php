<?php
namespace frontend\business\SaveRecordByransactions\SaveByTransaction;



use frontend\business\SaveRecordByransactions\ISaveForTransaction;
use yii\log\Logger;


/**
 * 牛牛游戏抢庄信息设置
 * Class NiuNiuGameGrabSeatSaveByTrans
 * @package frontend\business\SaveRecordByransactions\SaveByTransaction
 */
class NiuNiuGameGrabSeatWinNumSaveByTrans implements ISaveForTransaction
{
    private  $data=[];

    /**
     * @param $data   所要修改和插入的数据
     * @throws Exception
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    function SaveRecordForTransaction(&$error,&$outInfo)
    {
        $sql = 'update mb_game_seat set is_banker=:bk,multiple=:mt,win_num=:wn,chip_num=:cnum,remark1=:rem1 where game_id=:gid and user_id=:uid';   //用户座位信息更新
        $result = \Yii::$app->db->createCommand($sql,[
            ':bk' => $this->data['is_banker'],
            ':mt' => $this->data['multiple'],
            ':wn' => $this->data['win_num'],
            ':gid' => $this->data['game_id'],
            ':uid' => $this->data['user_id'],
            ':cnum' => $this->data['chip_num'],
            ':rem1' => time()
        ])->execute();
        if($result <= 0)
        {
            $error = '抢庄修改用户座位信息写入失败 '.\Yii::$app->db->createCommand($sql,[
                    ':bk' => $this->data['is_banker'],
                    ':mt' => $this->data['multiple'],
                    ':wn' => $this->data['win_num'],
                    ':gid' => $this->data['game_id'],
                    ':uid' => $this->data['user_id'],
                    ':cnum' => $this->data['chip_num'],
                    ':rem1' => time()
                ])->rawSql;
            return false;
        }
        return true;
    }

}