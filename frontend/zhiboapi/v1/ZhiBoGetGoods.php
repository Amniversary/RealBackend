<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/4/26
 * Time: 10:10
 */

namespace frontend\zhiboapi\v1;


use frontend\business\ApiCommon;
use frontend\business\ClientGoodsUtil;
use frontend\zhiboapi\IApiExcute;
use yii\log\Logger;

/**
 * 获取豆商品列表
 * Class ZhiBoGetGoods
 * @package frontend\zhiboapi\v3
 */
class ZhiBoGetGoods implements IApiExcute{


    function excute_action($dataProtocal, &$rstData,&$error, $extendData= array())
    {
        $uniqueNo = $dataProtocal['data']['unique_no'];
        if(!ApiCommon::GetLoginInfo($uniqueNo,$LoginInfo,$error))
        {
            return false;
        }

        if(!isset($dataProtocal['data']['sale_type']) ||
            empty($dataProtocal['data']['sale_type']))
        {
            $error = '商品销售类型不能为空';
            return false;
        }

        $saleType =  $dataProtocal['data']['sale_type'];

        if(!ClientGoodsUtil::GetInsideBuy($saleType,$outInfo,$error))
        {
            return false;
        }

        $rstData['has_data'] = '1';
        $rstData['data_type'] = 'jsonarray';
        $rstData['data'] =  $outInfo;
        return true;
    }
} 