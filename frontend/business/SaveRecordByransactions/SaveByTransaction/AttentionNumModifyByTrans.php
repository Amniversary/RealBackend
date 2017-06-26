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
 * 关注数的修改
 * Class AttentionNumModifyByTrans
 * @package frontend\business\SaveRecordByransactions\SaveByTransaction
 */
class AttentionNumModifyByTrans implements ISaveForTransaction
{
    private  $clientAvtive = null;
    private  $extend_params=[];

    /**
     * @param $record
     * @param array $extend_params
     * @throws Exception
     */
    public function __construct($record,$extend_params=[])
    {
        $this->clientAvtive = $record;
        $this->extend_params = $extend_params;
    }

    function SaveRecordForTransaction(&$error,&$outInfo)
    {
        if(!($this->clientAvtive instanceof ClientActive))
        {
            $error = '用户活跃记录';
            return false;
        }
        $op_type = $this->extend_params['op_type'];
        if(!in_array($op_type,['attention','cancel']))
        {
            $error = '操作类型异常';
            return false;
        }
        if($op_type === 'attention')
        {
            $sql = 'update mb_client_active set attention_num= attention_num + 1 where user_id=:uid';
        }
        else
        {
            $sql = 'update mb_client_active set attention_num= attention_num - 1 where user_id=:uid and attention_num > 0';
        }
        $rst = \Yii::$app->db->createCommand($sql,[

                ':uid'=>$this->clientAvtive->user_id
            ])->execute();
        if($rst <= 0)
        {
            throw new Exception('更新关注数失败');
        }

        return true;
    }
} 