<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/8/22
 * Time: 下午1:41
 */

namespace backend\business\SaveRecordByTransactions\SaveByTransaction;


use backend\business\SaveRecordByTransactions\ISaveForTransaction;

class SaveAuthSignByTrans implements ISaveForTransaction
{
    public $data;
    public $extend;

    public function __construct($data, $extend = [])
    {
        $this->data =  $data;
        $this->extend = $extend;
    }

    function SaveRecordForTransaction(&$error,&$outInfo)
    {
        $outInfo['is_sign'] = 0;
        $sql = 'insert ignore into wc_auth_sign(app_id, user_id, sign_num, create_time, update_time) VALUES(:appid, :user, 0, :create,:update)';
        \Yii::$app->db->createCommand($sql,[
            ':appid'=>$this->data['app_id'],
            ':user'=>$this->data['user_id'],
            ':create'=>date('Y-m-d H:i:s'),
            ':update'=>'1999-01-01',
        ])->execute();

        $time = date('Y-m-d');
        $upsql = 'update wc_auth_sign set sign_num = sign_num + 1, update_time = :time where app_id = :ap and user_id = :ud and update_time < :dt';
        $rst = \Yii::$app->db->createCommand($upsql,[
            ':time'=>$time,
            ':ap'=>$this->data['app_id'],
            ':ud'=>$this->data['user_id'],
            ':dt'=>$time
        ])->execute();

        if($rst <= 0){
            $outInfo['is_sign'] = 1;
        }
        return true;
    }
}