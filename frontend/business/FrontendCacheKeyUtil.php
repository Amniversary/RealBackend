<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/28
 * Time: 11:14
 */

namespace frontend\business;
use Yii;

class FrontendCacheKeyUtil
{
    /*
     *获取礼物列表的缓存Key
     * 2016-10-28
     * wangwei
     */
    const FRONTEND_V2_ZHIBOGETGIFTS_LIST_ALL  = "FRONTEND_V2_ZHIBOGETGIFTS_LIST_ALL";

    /*
     * 获取封播的间的状态
     *2016-11-2
     * wangwei
     */
    const FRONTEND_V2_ZHIBOQINIUCREATELIVING_GET_LIVING_ID    = "FRONTEND_V2_ZHIBOQINIUCREATELIVING_GET_LIVING_ID";

}