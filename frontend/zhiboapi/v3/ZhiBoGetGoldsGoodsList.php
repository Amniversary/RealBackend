<?php
/**
 * Created by PhpStorm.
 * User: wangwei
 * Date: 2016/9/12
 * Time: 9:58
 */

namespace frontend\zhiboapi\v3;

use frontend\business\GoldsGoodsUtil;
use frontend\business\ApiCommon;
use frontend\zhiboapi\IApiExcute;

/**
 * 获取金币商品列表信息
 * Class ZhiBoGetGoldsGoodsList
 * @package frontend\zhiboapi\v3
 */
class ZhiBoGetGoldsGoodsList implements IApiExcute
{
    /**
     * @param $dataProtocal
     * @param $rstData
     * @param $error
     * @param array $extendData
     * @return bool
     */
    function excute_action($dataProtocal, &$rstData,&$error, $extendData= array()){
        $unique_no = $dataProtocal['data']['unique_no'];
        if(!ApiCommon::GetLoginInfo($unique_no,$LoginInfo,$error)){
           return false;
        }
        $saleType = $dataProtocal['data']['sale_type'];
        $key = "golds_goods_list_$saleType";
        $goldsGoodsList = \Yii::$app->cache->get($key);
        if( !$goldsGoodsList )
        {
            $goldsGoodsList = GoldsGoodsUtil::GetGoldsGoodsListBySaleType($saleType);
            \Yii::$app->cache->set($key,$goldsGoodsList);
        }
        if(!isset($goldsGoodsList) || empty($goldsGoodsList)){
            $error = '金币商品列表不存在';
            return false;
        }

        $rstData['has_data'] = '1';
        $rstData['data_type'] = 'json';
        $rstData['data'] = $goldsGoodsList;
        return true;
    }
} 