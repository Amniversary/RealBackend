<?php
/**
 * Created by PhpStorm.
 * User: hlq
 * Date: 2016/5/11
 * Time: 16:00
 */

namespace frontend\business;



use common\models\ToBeanGoods;

class ToBeanGoodsUtil
{
    /**
     * 根据票可转豆列表ID得到基本信息
     * @param $record_id
     * @return null|static
     */
    public static function GetBeanGoodsById($record_id)
    {
        return ToBeanGoods::findOne(['record_id'=>$record_id]);
    }
}