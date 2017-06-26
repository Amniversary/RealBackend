<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/8
 * Time: 16:56
 */

namespace frontend\zhiboapi\v1\waistcoat;


use yii\log\Logger;

class CreateFilterCoat
{

    /**
     * 获取过虑的马甲号
     * @param $appID
     * @return array
     */
    public static  function GetFilterCoat( $appID )
    {
        $configFile = \Yii::$app->getBasePath().'/zhiboapi/v1/waistcoat/MasterSlaveCoatConfig.php';
        if(!file_exists($configFile))
        {
            $rst['errmsg'] = '找不到配置文件:MasterSlaveCoatConfig';
            \Yii::getLogger()->log('MasterSlaveCoatConfig'.var_export($rst['errmsg'],true),Logger::LEVEL_ERROR);
            echo json_encode($rst);
            exit;
        }

        $funData = require($configFile);
        $isMasterSlave = $funData[$appID];
        foreach ( $funData as $key=>$val )
        {
            if( $val==$isMasterSlave )
            {
                $appidString[] = strval($key);
            }
        }

        return $appidString;
    }
}