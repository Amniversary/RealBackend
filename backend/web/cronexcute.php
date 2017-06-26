<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/1/9
 * Time: 10:38
 */
if(!isset($argv) || !is_array($argv))
{
    echo 'error1';
    exit;
}
if($argv[1] !== '111back999')
{
    echo 'error2';
    exit;
}
ob_clean();
$url = 'http://front.meiyuan.com/fuck/test';
header('location:'.$url);