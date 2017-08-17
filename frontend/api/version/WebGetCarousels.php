<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/8/15
 * Time: 下午5:31
 */

namespace frontend\api\version;


use frontend\api\IApiExecute;
use frontend\business\CarouselsUtil;

class WebGetCarousels implements IApiExecute
{
    function execute_action($data, &$rstData,&$error, $extendData = [])
    {
        $rst = CarouselsUtil::GetWebCarouselInfo(true);
        $rst = empty($rst) ? [] : $rst;
        $rstData['code'] = 0;
        $rstData['data'] = $rst;
        return true;
    }
}