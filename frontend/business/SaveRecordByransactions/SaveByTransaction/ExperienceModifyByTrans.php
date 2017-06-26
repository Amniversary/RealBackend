<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/1/3
 * Time: 17:50
 */

namespace frontend\business\SaveRecordByransactions\SaveByTransaction;


use common\models\ClientActive;
use frontend\business\SaveRecordByransactions\ISaveForTransaction;
use yii\base\Exception;
use yii\log\Logger;

/**
 * 经验的修改
 * Class ExperienceModifyByTrans
 * @package frontend\business\SaveRecordByransactions\SaveByTransaction
 */
class ExperienceModifyByTrans implements ISaveForTransaction
{
    private  $clientActive = null;
    private  $extend_params=[];

    /**
     * @param $record
     * @param array $extend_params
     * @throws Exception
     */
    public function __construct($record,$extend_params=[])
    {
        $this->clientActive = $record;
        $this->extend_params = $extend_params;
    }

    function SaveRecordForTransaction(&$error,&$outInfo)
    {
        if(!($this->clientActive instanceof ClientActive))
        {
            $error = '不是用户活跃记录2';
            return false;
        }
        $experience_num = $this->extend_params['experience_num'];
        if(intval($experience_num) <= 0)
        {
            $error = '经验不能增加0';
            return false;
        }
        
        $sql = 'update mb_client_active ca set experience= experience + :ep,
level_no = (select level_id from mb_level where experience <= ca.experience order by level_id desc limit 1)
where user_id=:uid ';


        $rst = \Yii::$app->db->createCommand($sql,
            [
                ':ep'=>$experience_num,
                ':uid'=>$this->clientActive->user_id
            ])->execute();


        if($rst <= 0)
        {
            \Yii::getLogger()->log('等级无法更新，sql:'.\Yii::$app->db->createCommand($sql,
                    [
                        ':ep'=>$experience_num,
                        ':uid'=>$this->clientActive->user_id
                    ])->rawSql,Logger::LEVEL_ERROR);
            //throw new Exception('更新经验和等级数失败');
        }
        return true;
    }
} 