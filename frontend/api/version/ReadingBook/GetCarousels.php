<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/9/14
 * Time: 下午6:32
 */

namespace frontend\api\version\ReadingBook;


use frontend\api\IApiExecute;
use frontend\business\CarouselsUtil;

class GetCarousels implements IApiExecute
{
    function execute_action($data, &$rstData, &$error, $extendData = [])
    {
        $rst = CarouselsUtil::getBookCarousel();
        $rst = empty($rst) ? [] : $rst;
        $rstData['code'] = 0;
        $rstData['data'] = $rst;
        return true;
    }
}