<?php
/**
 * Created by PhpStorm.
 * User: hlq
 * Date: 16-04-26
 * Time: 下午5:30
 */

namespace frontend\zhiboapi\v2;

use common\components\PhpLock;
use frontend\business\ApiCommon;
use frontend\business\LivingHotUtil;
use frontend\zhiboapi\IApiExcute;
use yii\db\Query;
use yii\log\Logger;


/**
 * Class 获取观众
 * @package frontend\zhiboapi\v2
 */
class ZhiBoGetLivingAudience implements IApiExcute
{

    private function check_param_ok($dataProtocal,&$error='')
    {
        $fields = ['living_id'];//'wish_type_id',
        $fieldLabels = ['直播id'];//'愿望类别id',
        $len =count($fields);
        for($i = 0; $i <$len; $i ++)
        {
            if (!isset($dataProtocal['data'][$fields[$i]]) || empty($dataProtocal['data'][$fields[$i]])) {
                $error = $fieldLabels[$i] . '，不能为空';
                return false;
            }
        }
        return true;
    }
    /**
     * 获取观众
     * @param string $error
     */
    public function excute_action($dataProtocal, &$rstData,&$error, $extendData= array())
    {
        $error = '';
        if(!$this->check_param_ok($dataProtocal,$error))
        {
            return false;
        }
        $uniqueNo = $dataProtocal['data']['unique_no'];
        if(!ApiCommon::GetLoginInfo($uniqueNo,$LoginInfo,$error))
        {
            return false;
        }
        $pageNo = intval($dataProtocal['data']['page_no']);
        if(empty($pageNo) || ($pageNo <= 0))
        {
            $pageNo = 1;
        }
        //$page_size = intval($dataProtocal['data']['page_size']);
        /*if(empty($page_size) || ($page_size <= 0)){
            $page_size = 5;
        }*/
        $page_size = 5;
        $livingId = $dataProtocal['data']['living_id'];
        $key = 'mibo_living_audience_'.$livingId;
        $data = \Yii::$app->cache->get($key);
        if($data == false)
        {
            $phpLock = new PhpLock($key);
            $phpLock->lock();
            $data = \Yii::$app->cache->get($key);
            if($data === false)
            {
                $rst = LivingHotUtil::GetLivingAudienceFromContribution($livingId,$page_size);
                if(!isset($rst))
                {
                    $data = [];
                }
                else
                {
                    \Yii::$app->cache->set($key,json_encode($rst),60*5);
                    $data = $rst;
                }
            }
            else
            {
                $data = json_decode($data,true);
            }
            $phpLock->unlock();
        }
        else
        {
            $data = json_decode($data,true);
        }
        //$rst = LivingHotUtil::GetLivingAudience($pageNo,$livingId,$page_size);

        $rstData['has_data'] = '1';
        $rstData['data_type'] = 'jsonarray';
        $rstData['data'] = $data;
        //\Yii::getLogger()->log('get ddddd:'.var_export($rstData,true),Logger::LEVEL_ERROR);
        return true;
    }
}


