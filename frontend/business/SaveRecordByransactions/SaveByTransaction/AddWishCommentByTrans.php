<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/1/3
 * Time: 17:50
 */

namespace frontend\business\SaveRecordByransactions\SaveByTransaction;


use common\models\WishComment;
use frontend\business\SaveRecordByransactions\ISaveForTransaction;
use yii\base\Exception;

class AddWishCommentByTrans implements ISaveForTransaction
{
    private  $wishComment = null;
    private  $extend_params=[];

    /**
     * @param $record
     * @param array $extend_params
     * @throws Exception
     */
    public function __construct($record,$extend_params=[])
    {
        $this->wishComment = $record;
        $this->extend_params = $extend_params;
    }

    function SaveRecordForTransaction(&$error,&$outInfo)
    {
        if(!($this->wishComment instanceof WishComment))
        {
            $error = '不是愿望评论记录';
            return false;
        }
        if(!$this->wishComment->save())
        {
            \Yii::getLogger()->log(var_export($this->wishComment->getErrors(),true), Logger::LEVEL_ERROR);
            throw new Exception('愿望评论记录保存失败');
        }
        return true;
    }
} 