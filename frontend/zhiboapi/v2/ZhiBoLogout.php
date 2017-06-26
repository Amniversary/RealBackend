<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 15-12-13
 * Time: 下午9:36
 */

namespace frontend\zhiboapi\v2;

use frontend\business\ApiCommon;
use frontend\zhiboapi\IApiExcute;
use yii\log\Logger;

/**
 * Class 退出登录协议
 * @package frontend\meiyuanapi\v2
 */
class ZhiBoLogout implements IApiExcute
{
    public function excute_action($dataProtocal, &$rstData,&$error, $extendData= array())
    {
        $error = '';
        $deviceNo = '';
        $uniqueNo= '';
        $registerType='';
        $deviceType='';
        if(!ApiCommon::GetBaseInfoFromProtocol($dataProtocal, $deviceNo, $uniqueNo,$registerType,$deviceType,$error))
        {
            return false;
        }
        $synLoginInfo = null;
        $error = '';
        //\Yii::getLogger()->log('logout:'.$uniqueNo,Logger::LEVEL_ERROR);
        /*if(!ApiCommon::DelLoginCode($uniqueNo))
        {
            $error = '系统错误，删除登录验证失败';
            \Yii::getLogger()->log($error.' unique_no:'.$uniqueNo,Logger::LEVEL_ERROR);
            return false;
        }*/
        //\Yii::getLogger()->log('是否调用',Logger::LEVEL_ERROR);
        if(!ApiCommon::DelLoginInfo($uniqueNo))
        {
            $error = '系统错误，删除登录状态失败';
            \Yii::getLogger()->log($error.' unique_no:'.$uniqueNo, Logger::LEVEL_ERROR);
            //return false;
        }
        $rstData['data'] = ['type'=>1];
        return true;
    }
} 