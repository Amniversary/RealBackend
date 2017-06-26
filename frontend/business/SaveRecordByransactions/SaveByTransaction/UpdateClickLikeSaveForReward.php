<?php
namespace frontend\business\SaveRecordByransactions\SaveByTransaction;


use frontend\business\SaveRecordByransactions\ISaveForTransaction;
use yii\db\Query;
use common\components\tenxunlivingsdk\TimRestApi;
use frontend\business\JobUtil;

class UpdateClickLikeSaveForReward implements ISaveForTransaction
{
    private  $data;

    /**
     * @param $data   所要插入的数据
     * @throws Exception
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    function SaveRecordForTransaction(&$error,&$outInfo)
    {
        if(!($this->data instanceof \stdClass))
        {
            $error = '不是json对象，数据异常';
            return false;
        }

        $sql = 'update mb_living_goods set goods_num=goods_num+1 where living_id=:lid';
        $result = \Yii::$app->db->createCommand($sql,[
            ':lid' => $this->data->living_id,
        ])->execute();

        if($result <= 0){
            $error = '点赞记录1写入失败';
            return false;
        }
        return true;

    }
}