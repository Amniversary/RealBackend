<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/22
 * Time: 10:07
 */
namespace frontend\business\SendImMessage;

interface ImExcute
{
    function excute_im($jobData,&$error,$params=[]);
} 