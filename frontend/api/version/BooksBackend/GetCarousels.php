<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/8/11
 * Time: 下午2:18
 */

namespace frontend\api\version\BooksBackend;


use frontend\api\IApiExecute;
use frontend\business\CarouselsUtil;

class GetCarousels implements IApiExecute
{
    function execute_action($dataProtocol, &$rstData, &$error, $extendData = [])
    {
        $rst = CarouselsUtil::GetCarouselInfo(false);
        $rst = empty($rst) ? [] : $rst;
        $rstData['code'] = 0;
        $rstData['data'] = $rst;

        return true;
    }
}