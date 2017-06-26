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
 * 粉丝数的修改
 * Class FunsNumModifyByTrans
 * @package frontend\business\SaveRecordByransactions\SaveByTransaction
 */
class FunsNumModifyByTrans implements ISaveForTransaction
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
            $error = '不是用户活跃记录3';
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
            $sql = 'update mb_client_active set funs_num= funs_num + 1 where user_id=:uid';
        }
        else
        {
            $sql = 'update mb_client_active set funs_num= funs_num - 1 where user_id=:uid and funs_num > 0';
        }
        $rst = \Yii::$app->db->createCommand($sql,
            [
                ':uid'=>$this->clientActive->user_id
            ])->execute();
        if($rst <= 0)
        {
            throw new Exception('更新粉丝数失败');
        }
        return true;
    }
} 