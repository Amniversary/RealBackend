<?php
/**
 * Created by PhpStorm.
 * User: hlq
 * Date: 16-04-23
 * Time: 下午5:30
 */

namespace frontend\zhiboapi\v3;

use frontend\business\ApiCommon;
use frontend\business\ClientUtil;
use frontend\business\LivingUtil;
use frontend\business\RewardUtil;
use frontend\business\SaveRecordByransactions\SaveByTransaction\FinishLivingSaveForReward;
use frontend\zhiboapi\IApiExcute;
use yii\db\Query;


/**
 * Class 获取人气主播
 * @package frontend\zhiboapi\v3
 */
class ZhiBoGetHotLivingMaster implements IApiExcute
{
    private function check_param_ok($dataProtocal,&$error='')
    {
        $fields = ['hot_type'];
        $fieldLabels = ['人气类型'];
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
     * 获取人气主播
     * @param string $error
     */
    public function excute_action($dataProtocal, &$rstData,&$error, $extendData= array())
    {
        $error = '';
        $uniqueNo= $dataProtocal['data']['unique_no'];
        if(!ApiCommon::GetLoginInfo($uniqueNo,$LoginInfo,$error))
        {
            return false;
        }
        if(!$this->check_param_ok($dataProtocal,$error))
        {
            return false;
        }
        $hotType = $dataProtocal['data']['hot_type'];
        $pageNo = intval($dataProtocal['data']['page_no']);
        if(empty($pageNo) || ($pageNo <= 0)){
            $pageNo = 1;
        }
        $page_size = intval($dataProtocal['data']['page_size']);
        if(empty($page_size) || ($page_size <= 0)){
            $page_size = 5;
        }

        switch($hotType){
            case 'day':
                $hotType = 1;
                break;
            case 'week':
                $hotType = 2;
                break;
            case 'month':
                $hotType = 3;
                break;
        }
        $rst = LivingUtil::GetHotLivingMaster($hotType,$LoginInfo,$pageNo,$page_size);
        if(!isset($rst)){
            $rst = [];
        }

        $rstData['has_data'] = '1';
        $rstData['data_type'] = 'jsonarray';
        $rstData['data'] = $rst;

        return true;
    }
}


