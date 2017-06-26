<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/22
 * Time: 19:00
 */

namespace frontend\zhiboapi\v3;


use common\components\PhpLock;
use frontend\business\ApiCommon;
use frontend\business\LivingGuessUtil;
use frontend\business\LivingUtil;
use frontend\zhiboapi\IApiExcute;
use yii\log\Logger;

/**
 * 门票直播竞猜协议接口
 * Class ZhiBoGetGuess
 * @package frontend\zhiboapi\v3
 */
class ZhiBoGetGuess implements IApiExcute
{
    function excute_action($dataProtocal, &$rstData,&$error, $extendData= array())
    {
        if(!$this->check_param_ok($dataProtocal, $error))
        {
            return false;
        }
        $unique_no = $dataProtocal['data']['unique_no'];
        $guess_type = $dataProtocal['data']['guess_type'];
        //$living_type = $dataProtocal['data']['living_type'];
        $is_ok = $dataProtocal['data']['is_ok'];
        $living_id = $dataProtocal['data']['living_id'];
        $device_type = $dataProtocal['device_type'];
        if(!ApiCommon::GetLoginInfo($unique_no, $LoginInfo, $error))
        {
            return false;
        }

        if(!LivingGuessUtil::CreateGuessRecord($LoginInfo,$guess_type,$is_ok,$living_id,$device_type,$result,$error))
        {
            return false;
        }
        //\Yii::getLogger()->log('rstguess_:'.var_export($result,true),Logger::LEVEL_ERROR);
        //\Yii::getLogger()->log('error_guess: '.var_export($error,true),Logger::LEVEL_ERROR);
        $rstData['has_data'] = '1';
        $rstData['data_type'] = 'jsonarray';
        $rstData['data'] = $result;
        return true;
    }

    private function check_param_ok($dataProtocal,&$error='')
    {
        if(!isset($dataProtocal['data']['is_ok']))
        {
            $error = '密码结果，不能为空';
            return false;
        }
        $fields = ['unique_no','living_id','guess_type'];
        $fieldLabels = ['唯一号','直播间id','竞猜类型'];
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
} 