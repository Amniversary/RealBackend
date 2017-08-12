<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/8/7
 * Time: 下午4:01
 */

namespace frontend\business\SaveRecordByTransactions\SaveByTransaction;

use frontend\business\SaveRecordByTransactions\ISaveForTransaction;

class TemplateSaveByTrans implements ISaveForTransaction
{
    public $Data;
    public $extend;

    public function __construct($data, $extend_params = [])
    {
        $this->Data = $data;
        $this->extend = $extend_params;
    }

    function SaveRecordForTransaction(&$error,&$outInfo)
    {
        $sql = 'insert ignore into wc_template_flag (user_id, app_id ,temp_num) value(:ud,:pd,0)';
        \Yii::$app->db->createCommand($sql, [
            ':ud'=>$this->Data['client_id'],
            ':pd'=>$this->Data['app_id'],
        ])->execute();


        $up = 'update wc_template_flag set temp_num = :tm ,';
        if($this->Data['flag'] == 1){
            $up .= 'remark1 = :dt ';
        }else{
            $up .= 'remark2 = :dt ';
        }
        $up .= 'WHERE user_id = :ud and app_id =:pd';
        $rst = \Yii::$app->db->createCommand($up,[
            ':tm'=>$this->Data['flag'],
            ':ud'=>$this->Data['client_id'],
            ':pd'=>$this->Data['app_id'],
            ':dt'=>date('Y-m-d H:i:s')
        ])->execute();
        if($rst <= 0) {
            $error = '更新模板消息标记次数失败'."\n";
            \Yii::error($error . ' :' . \Yii::$app->db->createCommand($up,[
                    ':ud'=> $this->Data['client_id'],
                    ':tm'=>$this->Data['flag'],
                    ':pd'=>$this->Data['app_id'],
                    ':dt'=>date('Y-m-d H:i:s')
                ])->rawSql);
            return false;
        }

        return true;
    }
}