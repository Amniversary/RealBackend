<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/1/3
 * Time: 17:50
 */

namespace frontend\business\SaveRecordByransactions\SaveByTransaction;


use common\models\LivingPersonnum;
use frontend\business\SaveRecordByransactions\ISaveForTransaction;
use yii\base\Exception;

/**
 * 经验的修改
 * Class ExperienceModifyByTrans
 * @package frontend\business\SaveRecordByransactions\SaveByTransaction
 */
class LivingPersonNumModifyByTrans implements ISaveForTransaction
{
    private  $livingPersonNum = null;
    private  $extend_params=[];

    /**
     * @param $record
     * @param array $extend_params
     * @throws Exception
     */
    public function __construct($record,$extend_params=[])
    {
        $this->livingPersonNum = $record;
        $this->extend_params = $extend_params;
    }

    function SaveRecordForTransaction(&$error,&$outInfo)
    {
        if(!($this->livingPersonNum instanceof LivingPersonnum))
        {
            $error = '不是直播在线人数记录';
            return false;
        }
        $op_type = $this->extend_params['op_type'];
        if(!in_array($op_type,['enter','quit']))
        {
            $error = '操作类型不正确,type:'.$op_type;
            return false;
        }
        if($op_type === 'enter')
        {
            $sql = 'update mb_living_personnum set person_count= person_count + 1,person_count_total=person_count_total + 1
where living_id=:li';
        }
        else
        {
            $sql = 'update mb_living_personnum set person_count= person_count - 1 where living_id=:li and person_count > 0';
        }
        $rst = \Yii::$app->db->createCommand($sql,
            [
                ':li'=>$this->livingPersonNum->living_id
            ])->execute();
        if($rst <= 0)
        {
            throw new Exception('更新在线人数失败');
        }
        return true;
    }
} 