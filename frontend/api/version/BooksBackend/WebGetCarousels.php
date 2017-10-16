<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/8/15
 * Time: 下午5:31
 */

namespace frontend\api\version\BooksBackend;


use frontend\api\IApiExecute;
use frontend\business\CarouselsUtil;

class WebGetCarousels implements IApiExecute
{
    function execute_action($dataProtocol, &$rstData, &$error, $extendData = [])
    {
        if(empty($dataProtocol['data']['book_id']) || !isset($dataProtocol['data']['book_id'])) {
            $error = '书籍Id ,不能为空';
            return false;
        }
        $rst = CarouselsUtil::GetWebCarousels($dataProtocol['data']['book_id']);
        $rst = empty($rst) ? [] : $rst;
        $rstData['code'] = 0;
        $rstData['data'] = $rst;
        return true;
    }
}