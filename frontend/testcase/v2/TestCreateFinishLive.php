<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/17
 * Time: 15:20
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

use frontend\testcase\v2\QiNiuCreateLiving;
use frontend\testcase\v2\QiNiuUpdateLiving;
use frontend\testcase\v2\QiNiuFinishLiving;

/**
 * 用户创建直播间，并退出直播间
 * Class TestCreateFinishLive
 * @package frontend\testcase\v2
 */
class TestCreateFinishLive implements  IApiExcute
{
    function excute_action($dataProtocal,&$rstData,&$error, $extendData= array())
    {
        $login = new TestApiUserLogin();
        if( empty( $login ) && !isset( $login ) ){
            \Yii::getLogger()->log('模拟用户登陆时出现了现有用户已全部登陆完，请重新增加新测试用户>',Logger::LEVEL_ERROR);
            return false;
        }
        $outInfo = $login->outInfo;
        $createLiving = QiNiuCreateLiving::CreateLiving($outInfo);
        \Yii::getLogger()->log('模拟测试创建直播间返回信息====>'.var_export($createLiving,true),Logger::LEVEL_ERROR);
        if( $createLiving['info'] )
        {
            $Info = json_decode( $createLiving['info'] ,true);
            $living_id = $Info['data']['living_id'];
            $group_id = $Info['data']['group_id'];

            $updateLivingInfo = QiNiuUpdateLiving::UpdateLiving($outInfo,$living_id,$group_id);
            \Yii::getLogger()->log('模拟测试更新直播间返回信息====>'.var_export($updateLivingInfo,true),Logger::LEVEL_ERROR);

            //$endLivingInfo = QiNiuFinishLiving::FinishLiving($outInfo,$living_id);
           // \Yii::getLogger()->log('模拟测试结束直播间返回信息====>'.var_export($endLivingInfo,true),Logger::LEVEL_ERROR);
        }

        return true;
    }
}