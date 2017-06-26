<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/1/3
 * Time: 17:50
 */

namespace frontend\business\SaveRecordByransactions\SaveByTransaction;


use common\models\ClientActive;
use frontend\business\ExperienceLogUtil;
use frontend\business\SaveRecordByransactions\ISaveForTransaction;
use yii\base\Exception;

/**
 * 创建经验日志
 * source_type = 1需要的字段：
'device_type', 'source_type', 'living_id','change_rate', 'experience','create_time', 'starttime', 'endtime', 'owner'
source_type = 2或3  需要的字段：
'device_type', 'source_type', 'living_id','change_rate', 'experience','create_time','gift_value',  'relate_id'
 * Class CreateExperienceLogByTrans
 * @package frontend\business\SaveRecordByransactions\SaveByTransaction
 */
class CreateExperienceLogByTrans implements ISaveForTransaction
{
    private  $clientActive = null;
    private  $extend_params=[];

    /**
     * @param $record
     * @param array $extend_params
     * @throws Exception
     */
    public function __construct($record,$extend_params=[])
    {
        $this->clientActive = $record;
        $this->extend_params = $extend_params;
    }

    function SaveRecordForTransaction(&$error,&$outInfo)
    {
        if(!($this->clientActive instanceof ClientActive))
        {
            $error = '不是用户活跃记录1';
            return false;
        }

        if(!ExperienceLogUtil::CreateExperienceLog($this->extend_params,$error))
        {
            throw new Exception($error);
        }
        return true;
    }
} 