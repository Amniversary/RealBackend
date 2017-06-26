<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/1/12
 * Time: 9:52
 */

namespace frontend\business;


use common\components\StatusUtil;
use common\models\HotWords;
use yii\log\Logger;

class HotWordsUtil
{

    public static function SaveHotWords($hotwords, &$error)
    {
        if(!($hotwords instanceof HotWords))
        {
            $error = '不是热词记录';
            return false;
        }
        if(!$hotwords->save())
        {
            $error = '热词记录保存失败';
            \Yii::getLogger()->log($error.' :'.var_export($hotwords->getErrors(),true),Logger::LEVEL_ERROR);
            return false;
        }
        return true;
    }

    public static function GetHotWordsById($hot_words_id)
    {
        return HotWords::findOne(['hot_words_id'=>$hot_words_id]);
    }

    /**
     * 获取热门关键字，无分页
     * @param $type
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function GetHotWords($type, $limit)
    {
        if(empty($type))
        {
            $typeList = [4];//默认城市
        }
        else
        {
            $typeList = StatusUtil::GetStatusList($type,3);
        }
        $condition = ['and','status=1',['in','words_type',$typeList]];
        $query = HotWords::find()->where($condition);
        if(!empty($limit))
        {
            $query->limit($limit);
        }
        $query->orderBy('order_no asc');
        return $query->all();
    }

    /**
     * 格式化热门关键字
     * @param $rcList
     */
    public static function GetFormateHotWords($rcList)
    {
        $out = [];
        if(empty($rcList))
        {
            return $out;
        }
        foreach($rcList as $hotWords)
        {
            $ary = [
                'hot_words_id'=>$hotWords->hot_words_id,
                'words_type'=>$hotWords->words_type,
                'content'=>$hotWords->content,
                'order_no'=>$hotWords->order_no
            ];
            $out[] = $ary;
        }
        return $out;
    }
} 