<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/18
 * Time: 10:44
 */

namespace frontend\testcase\v2;


use frontend\business\ApiCommon;
use frontend\testcase\IApiExcute;
use common\models\Client;

use frontend\zhiboapi\v2\ZhiBoUpdateKey;
use common\components\AESCrypt;
use common\components\UsualFunForStringHelper;
use common\components\UsualFunForNetWorkHelper;
use yii\base\Exception;
use yii\log\Logger;
use linslin\yii2\curl\Curl;

use frontend\testcase\v2\TestQiNiuEnterRoom;
use frontend\testcase\v2\TestQuitRoom;

use frontend\testcase\v2\QiNiuFinishLiving;

/**
 * 用户进入直播间，退出直播间
 * Class TestQiNiuEnterQuitRoom
 * @package frontend\testcase\v2
 */
class TestQiNiuEnterQuitRoom implements  IApiExcute
{
    function excute_action($dataProtocal,&$rstData,&$error, $extendData= array())
    {
        $login = new TestApiUserLogin();

        if( empty( $user ) && !isset( $user ) ){
            \Yii::getLogger()->log('模拟用户登陆时出现了现有用户已全部登陆完，请重新增加新测试用户>',Logger::LEVEL_ERROR);
            return false;
        }

        $outInfo = $login->outInfo;
        $outInfo['living_id'] = $dataProtocal['living_id'];
        $enterRoomInfo =  TestQiNiuEnterRoom::EnterRoom($outInfo);
        \Yii::getLogger()->log("模拟测试用户unique_no:".$outInfo['unique_no']."进入直播间:".$dataProtocal['live_id']."的返回信息====>".var_export($enterRoomInfo,true),Logger::LEVEL_ERROR);

        $quitRoomInfo = TestQuitRoom::QuitRoom($outInfo);
        \Yii::getLogger()->log("模拟测试用户unique_no:".$outInfo['unique_no']."退出直播间:".$dataProtocal['live_id']."的返回信息====>".var_export($quitRoomInfo,true),Logger::LEVEL_ERROR);

        return true;
    }
}