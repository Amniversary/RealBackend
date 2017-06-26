<?php
/**
 * Created by PhpStorm.
 * User: hlq
 * Date: 16-04-27
 * Time: 上午11:30
 */

namespace frontend\zhiboapi\v3;

use common\models\GoldsAccount;
use frontend\business\ApiCommon;
use frontend\business\FrontendCacheKeyUtil;
use frontend\business\LivingUtil;
use frontend\zhiboapi\IApiExcute;
use yii\db\Query;
use yii\log\Logger;

/**
 * Class 获取礼物列表
 * @package frontend\zhiboapi\v3
 */
class ZhiBoGetGifts implements IApiExcute
{

    /**
     * 获取礼物列表
     * @param string $error
     */
    public function excute_action($dataProtocal, &$rstData,&$error, $extendData= array())
    {
        $key = FrontendCacheKeyUtil::FRONTEND_V2_ZHIBOGETGIFTS_LIST_ALL;
        $error = '';
        $data = \Yii::$app->cache->get($key);
        if( $data ){
            $rstData['data'] = $data;
        }else{
            $data = LivingUtil::GetGiftsList();
            if(!isset($data))
            {
                $data = [];
            }
            \Yii::$app->cache->set($key,$data);
        }
        $rstData['has_data'] = '1';
        $rstData['data_type'] = 'jsonarray';
        $rstData['data'] = $data;

        return true;
    }
}