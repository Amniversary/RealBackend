<?php
namespace frontend\business\SaveRecordByransactions\SaveByTransaction;



use frontend\business\SaveRecordByransactions\ISaveForTransaction;
use yii\log\Logger;


/**
 * 牛牛游戏游戏胜负记录信息设置
 * Class NiuNiuGameResultRecordSaveByTrans
 * @package frontend\business\SaveRecordByransactions\SaveByTransaction
 */
class NiuNiuGameResultRecordSaveByTrans implements ISaveForTransaction
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
        $sql = 'insert into  mb_game_result_record (game_id,seat1,seat2,seat3,seat4) values(:gid,:s1,:s2,:s3,:s4)';
        $result = \Yii::$app->db->createCommand($sql,[
            ':gid' => $this->data['game_id'],
            ':s1' => $this->data['seat1'],
            ':s2' => $this->data['seat2'],
            ':s3' => $this->data['seat3'],
            ':s4' => $this->data['seat4']
        ])->execute();
        if($result <= 0)
        {
            \Yii::getLogger()->log('游戏胜负记录信息写入失败  $sql===:'.\Yii::$app->db->createCommand($sql,[
                    ':gid' => $this->data['game_id'],
                    ':s1' => $this->data['seat1'],
                    ':s2' => $this->data['seat2'],
                    ':s3' => $this->data['seat3'],
                    ':s4' => $this->data['seat4']
                ])->rawSql,Logger::LEVEL_ERROR);
            $error = '游戏胜负记录信息写入失败  sql===:'.\Yii::$app->db->createCommand($sql,[
                    ':gid' => $this->data['game_id'],
                    ':s1' => $this->data['seat1'],
                    ':s2' => $this->data['seat2'],
                    ':s3' => $this->data['seat3'],
                    ':s4' => $this->data['seat4']
                ])->rawSql;
            return false;
        }

        return true;
    }

}