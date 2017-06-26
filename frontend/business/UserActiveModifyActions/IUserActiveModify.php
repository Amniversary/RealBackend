<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/1/18
 * Time: 13:23
 */

namespace frontend\business\UserActiveModifyActions;


interface IUserActiveModify
{
    function UserActiveModify($userAcive,&$error,$params=[]);
} 