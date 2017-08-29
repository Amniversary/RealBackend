<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/5/27
 * Time: 10:03
 */

namespace console\controllers\BackendActions\BackStatistic;

/**
 * 后台统计接口
 * Interface IBackStatistic
 * @package console\controllers\BackStatistic
 */
interface IBackStatistic
{
    /**
     * @param $params
     * @param $outInfo
     * @param $error
     * @return bool
     */
    function ExecuteStatistic($params, &$outInfo, &$error);
} 