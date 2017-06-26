<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/11
 * Time: 19:33
 */

namespace frontend\business\SaveRecordByransactions\SaveByTransaction;


use common\models\Comment;
use frontend\business\DynamicUtil;
use frontend\business\SaveRecordByransactions\ISaveForTransaction;
use yii\log\Logger;

class AddCommentSaveByTrans implements ISaveForTransaction
{
    private $CommentRecord = null;
    private $extend_params = [];

    public function __construct($Comment,$extend_params=[])
    {
        $this->CommentRecord = $Comment;
        $this->extend_params = $extend_params;
    }

    function SaveRecordForTransaction(&$error,&$outInfo)
    {
        if(!DynamicUtil::SaveComment($this->CommentRecord ,$error))
        {
            $error = '用户评论信息保存失败';
            return false;
        }


        $sql = 'update mb_friends_circle set comment_num = comment_num + 1 WHERE dynamic_id = :md';
        $query = \Yii::$app->db->createCommand($sql,[
            ':md'=>$this->CommentRecord->dynamic_id,
        ])->execute();

        if($query <= 0)
        {
            $error = '更新评论数失败';
            \Yii::getLogger()->log($error.' : dynamic_id:'.$this->CommentRecord->dynamic_id .\Yii::$app->db->createCommand($sql,[
                    ':md'=>$this->CommentRecord->dynamic_id,
                ])->rawSql,Logger::LEVEL_ERROR);
            return false;
        }

        return true;
    }
} 