<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 15-12-17
 * Time: 下午10:07
 */

namespace frontend\business;
use common\models\WishType;

class WishTypeUtil
{

    /**
     * 获取下拉框数据
     */
    public static function GetTypeListItems()
    {
        $typeList = self::GetWishTypeList();
        $rst = [];
        foreach($typeList as $wishType)
        {
            $rst[$wishType->wish_type_id]=$wishType->type_name;
        }
        return $rst;
    }

    /**
     * 根据id获取类别
     * @param $wish_type_id
     * @return null|static
     */
    public static function GetWishTypeById($wish_type_id)
    {
        return WishType::findOne([
            'wish_type_id'=>$wish_type_id
        ]);
    }

    /**
     * 获取类别列表
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function GetWishTypeList()
    {
        $rcList = WishType::find()->orderBy('order_no asc')->where(
            [
                'status'=>'1'
            ]
        )->all();
        return $rcList;
    }

    /**
     * 格式化类别数据
     * @param $recordList
     * @return array
     */
    public static function GetWishTypeFormate($recordList)
    {
        $out = [];
        if(!isset($recordList) || empty($recordList))
        {
            return $out;
        }
        foreach($recordList as $model)
        {
            $ary=[
                'wish_type_id'=>$model->wish_type_id,
                'type_name' => $model->type_name,
                'order_no' => $model->order_no,
                'pic_url' => $model->pic_url,
                'fun_type'=>$model->fun_type,
                'fun_id'=>$model->fun_id,
                'fun_param'=>$model->fun_param
            ];
            $out[] =$ary;
        }
        return $out;
    }
} 