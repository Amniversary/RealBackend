<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/4/26
 * Time: 12:19
 */

namespace frontend\business;


use common\models\GoldsGoods;
use common\models\Goods;
use common\models\SystemParams;
use yii\db\Query;
use yii\log\Logger;

class ClientGoodsUtil {


    /**
     * 根据销售类型 ,获取内购商品数据
     * @param $saleType
     * @param $outInfo
     * @param $error
     * @return bool
     */
    public static function GetInsideBuy($saleType,&$outInfo,&$error)
    {
        $condition = 'status = 1 and sale_type = :tp';
        $query = new Query();
        $sql = $query
            ->select(['goods_id','bean_num','extra_bean_num','pic','goods_price'])
            ->from('mb_goods')
            ->where($condition,[':tp'=>$saleType])
            ->orderBy('order_no asc')
            ->all();

        if(!isset($sql))
        {
            $error = '内购商品信息不存在，查找失败';
            \Yii::getLogger()->log($error.' '.var_export($sql),Logger::LEVEL_ERROR);
            return false;
        }
        $outInfo = [];
        foreach($sql as $info)
        {
            $outInfo[] = $info;
        }

        return true;
    }


    /**
     * 获取所有豆商品信息
     * @return null|static
     */
    public static function GetBeanCommodityList()
    {
        $query = (new Query())
            ->select(['goods_id','goods_name','pic','goods_price','sale_type','bean_num','extra_bean_num','high_led'])
            ->from('mb_goods')
            ->where('sale_type = 8 AND status = 1')
            ->orderBy('order_no asc')
            ->all();

        return $query;
    }

    /**
     * 根据商品id 获取商品信息
     * @param $goodsId
     * @return null|static
     */
    public static function GetGoodsInfoById($goodsId)
    {
        return Goods::findOne(['goods_id'=>$goodsId]);
    }

    /**
     * 根据金币id 获取商品信息
     * @param $gold_id
     * @return null|static
     */
    public static function GetGoodsGoldInfoById($gold_id)
    {
        return GoldsGoods::findOne(['gold_goods_id'=>$gold_id]);
    }

    /**
     * 获取所有金币商品信息
     * @return array
     */
    public static function GetGoldGoodsList()
    {
        $query = (new Query())
            ->select(['gold_goods_id','gold_goods_name','gold_goods_pic','gold_goods_price','sale_type','gold_num','extra_integral_num'])
            ->from('mb_golds_goods')
            ->where(['and','status = 1',['or','sale_type = 4','sale_type = 8']])
            ->orderBy('order_no asc')
            ->all();

        return $query;
    }

    /**
     * 给用户新增固定的鲜花
     * @param $client_no
     * @return mixed
     */
    public static function AddClientFlower($client_no)
    {
        //获取鲜花的值
        $flower = SystemParams::findOne(['code'=>'present_flower_for_login']);
        $flower_value = $flower['value1'];

        $client_info = ClientUtil::GetClientNo($client_no);

        //增加鲜花
        BalanceUtil::AddReadBeanNum($client_info['client_id'],$flower_value,$error);

    }
} 