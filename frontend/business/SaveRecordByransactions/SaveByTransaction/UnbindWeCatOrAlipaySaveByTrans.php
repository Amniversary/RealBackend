<?php
namespace frontend\business\SaveRecordByransactions\SaveByTransaction;


use frontend\business\SaveRecordByransactions\ISaveForTransaction;

class UnbindWeCatOrAlipaySaveByTrans implements ISaveForTransaction
{
    private  $params;

    /**
     * @param $data   所要插入的数据
     * @throws Exception
     */
    public function __construct($params)
    {
        $this->params = $params;
    }

    function SaveRecordForTransaction(&$error,&$outInfo)
    {
        if($this->params['unbind_type'] == 1)
        {

            $sql = 'delete from mb_client_other where user_id=:uid and register_type=2';
            $res = \Yii::$app->db->createCommand($sql,[
                ':uid'=>$this->params['client_id'],
            ])->execute();
            if($res <= 0){
                $error = '微信解除绑定失败';
                return false;
            }


            $client_sql = 'update mb_client set is_bind_weixin=1 where client_id=:uid';
            $res = \Yii::$app->db->createCommand($client_sql,[
                ':uid'=>$this->params['client_id'],
            ])->execute();

            if($res <= 0){
                $error = '微信解除绑定记录更新失败';
                return false;
            }

        }
        else
        {

            $sql = 'delete from mb_alipay_for_cash where user_id=:uid';
            $res = \Yii::$app->db->createCommand($sql,[
                ':uid'=>$this->params['client_id'],
            ])->execute();
            if($res <= 0){
                $error = '支付宝解除绑定失败';
                return false;
            }

            $client_sql = 'update mb_client set is_bind_alipay=1 where client_id=:uid';
            $res = \Yii::$app->db->createCommand($client_sql,[
                ':uid'=>$this->params['client_id'],
            ])->execute();

            if($res <= 0){
                $error = '支付宝解除绑定记录更新失败';
                return false;
            }

        }

        return true;

    }
}