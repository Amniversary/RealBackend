<?php

/**
 * Created by PhpStorm.
 * User: wangwei
 * Date: 2016/10/12
 * Time: 16:31
 */

namespace frontend\business;


use common\components\PhpLock;
use common\models\GoldsGoods;
use yii\db\Query;
use yii\log\Logger;

class GoldsGoodsUtil {
    
    /**
     * 获取所有的金币商品列表信息 model
     * @param $user_id
     * @return null|static   GoldsGoods model
     */
    public static function GetGoldsGoodsList(){
        
        return GoldsGoods::find()->asArray()
                ->select([ 'gold_goods_id','gold_goods_name','gold_goods_pic','gold_goods_price','sale_type','status','gold_goods_type','gold_num','extra_integral_num','order_no'])
                ->orderBy("order_no desc")
                ->all();
    }

    /**
     * 获取所有的金币商品列表信息 model
     * @param $sale_type
     * @return null|static   GoldsGoods model
     */
    public static function GetGoldsGoodsListBySaleType($sale_type){

        return GoldsGoods::find()->asArray()
            ->select([ 'gold_goods_id','gold_goods_name','gold_goods_pic','gold_goods_price','sale_type','status','gold_goods_type','gold_num','extra_integral_num','order_no'])
            ->where(['sale_type'=>[4,$sale_type]])
            ->where(['status'=>1])
            ->orderBy("order_no desc")
            ->all();
    }

    /*
     * 获取金币商品的信息Model
     * @param $gold_goods_id
     * @return null|static   GoldsGoods model
     */
    public static function GetGoldGoodsModelOne($gold_goods_id){
        return GoldsGoods::findOne(['gold_goods_id'=>$gold_goods_id]);
    }



}