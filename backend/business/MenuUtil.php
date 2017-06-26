<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/2/19
 * Time: 17:28
 */

namespace backend\business;

   use common\models\Menu;
   use common\models\UserMenu;
   use yii\log\Logger;
class MenuUtil
{
    public static function gmt_iso8601($time) {

        $dtStr = date("c", $time);
        $mydatetime = new \DateTime($dtStr);
        $expiration = $mydatetime->format(\DateTime::ISO8601);
        $pos = strpos($expiration, '+');
        $expiration = substr($expiration, 0, $pos);
        return $expiration."Z";
    }
} 