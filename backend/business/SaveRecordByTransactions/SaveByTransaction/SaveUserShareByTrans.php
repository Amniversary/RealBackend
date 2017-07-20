<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/7/19
 * Time: 上午10:39
 */

namespace backend\business\SaveRecordByTransactions\SaveByTransaction;


use backend\business\SaveRecordByTransactions\ISaveForTransaction;
use common\models\QrcodeShare;

class SaveUserShareByTrans implements ISaveForTransaction
{
    public $data;
    public $extend;

    public function __construct($userData, $extend)
    {
        $this->data = $userData;
        $this->extend = $extend;
    }

    function SaveRecordForTransaction(&$error,&$outInfo)
    {
        //TODO: 记录邀请者绑定关系
        $share = new QrcodeShare();
        $share->share_user_id = $this->data->client_id;
        $share->other_user_id = $this->extend->client_id;
        $share->create_time = date('Y-m-d H:i:s');
        if(!$share->save()) {
            $error = '保存二维码分享关注信息失败';
            \Yii::error($error.' : '.var_export($share->getErrors(),true));
            return false;
        }

        $sql = 'update wc_client set invitation = invitation + 1, is_vip = if(invitation>=5,1,0) WHERE client_id = :cd';
        $rst = \Yii::$app->db->createCommand($sql,[
            ':cd'=>$this->data->client_id,
        ])->execute();
        if($rst <= 0) {
            $error = '保存邀请人数量失败';
            \Yii::error($error.' : '. \Yii::$app->db->createCommand($sql,[
                    ':cd'=>$this->data->client_id,
                ])->rawSql);
            return false;
        }

        return true;
    }
}