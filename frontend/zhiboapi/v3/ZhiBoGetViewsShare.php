<?php
/**
 * Created by PhpStorm.
 * User: hlq
 * Date: 16-04-23
 * Time: 下午5:30
 */

namespace frontend\zhiboapi\v3;

use frontend\business\ApiCommon;
use frontend\business\JobUtil;
use frontend\zhiboapi\IApiExcute;
use yii\log\Logger;


/**
 * 观众直播分享，概率获取豆值
 * Class ZhiBoGetViewsShare
 * @package frontend\zhiboapi\v3
 */
class ZhiBoGetViewsShare implements IApiExcute
{


    public function excute_action($dataProtocal, &$rstData,&$error, $extendData= array())
    {
        //$test_time1 = microtime(true);
        $error = '';
        $uniqueNo = $dataProtocal['data']['unique_no'];
        if(!ApiCommon::GetLoginInfo($uniqueNo,$LoginInfo,$error))
        {
            return false;
        }

        //直播观众分享获得豆队列
        $data = [
            'user_id' => $LoginInfo['user_id'],
            'living_id' => $dataProtocal['data']['living_id'],
            'nick_name' => $LoginInfo['nick_name'],
        ];
        if(!JobUtil::AddCustomJob('LivingViewsShareBeanstalk','living_views_share',$data,$error))
        {
            \Yii::getLogger()->log('ZhiBoGetViewsShare    $error====:'.$error,Logger::LEVEL_ERROR);
            return false;
        }

        if(!isset($rst) || empty($rst))
        {
            $rst = [];
        }


        $rstData['has_data'] = '1';
        $rstData['data_type'] = 'jsonarray';
        $rstData['data'] = $rst;
        //$test_time2 = microtime(true);
        //$alltime = $test_time2-$test_time1;
        //\Yii::getLogger()->log('观众直播分享表执行时间===：'.$alltime,Logger::LEVEL_ERROR);
        return true;
    }
}