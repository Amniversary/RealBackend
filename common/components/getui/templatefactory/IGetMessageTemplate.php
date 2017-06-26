<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/5/12
 * Time: 14:02
 */

namespace common\components\getui\templatefactory;


interface IGetMessageTemplate
{
    /**
     * @param array $data
     * @return \IGtBaseTemplate
     */
    function GetTemplate($data);
} 