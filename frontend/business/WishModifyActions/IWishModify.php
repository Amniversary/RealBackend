<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/1/3
 * Time: 15:59
 */

namespace frontend\business\WishModifyActions;

/**
 * Interface IWishModify 愿望修改接口
 * @package frontend\business\WishModifyActions
 */
interface IWishModify
{
    function WishModify($wish,&$error,$params=[]);
} 