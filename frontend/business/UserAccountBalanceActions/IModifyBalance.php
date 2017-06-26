<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/1/7
 * Time: 11:15
 */

namespace frontend\business\UserAccountBalanceActions;

/**
 * 账户信息修改接口
 * Interface IModifyUserAccountInfo
 * @package frontend\business\UserAccountBalanceActions
 */
interface IModifyBalance
{
    function ModifyBalance($params,&$balance, &$error);
} 