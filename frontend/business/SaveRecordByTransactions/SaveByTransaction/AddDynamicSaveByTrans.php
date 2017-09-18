<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/9/13
 * Time: 下午2:57
 */

namespace frontend\business\SaveRecordByTransactions\SaveByTransaction;


use common\models\StudyingDynamic;
use common\models\Voice;
use frontend\business\SaveRecordByTransactions\ISaveForTransaction;

class AddDynamicSaveByTrans implements ISaveForTransaction
{
    public $data;
    public $extend;

    public function __construct($data, $extend = [])
    {
        $this->data = $data;
        $this->extend = $extend;
    }

    function SaveRecordForTransaction(&$error,&$outInfo)
    {
        if(!($this->data instanceof StudyingDynamic)) {
            $error = '不是动态记录对象';
            return false;
        }
        if (!$this->data->save()) {
            $error = '保存动态记录失败';
            \Yii::error($error . ' ' . var_export($this->data->getErrors(), true));
            return false;
        }
        if($this->extend['type'] == 1) {
            $voice = new Voice();
            $voice->dynamic_id = $this->data->dynamic_id;
            $voice->voice = $this->extend['voice'];
            if(!$voice->save()) {
                $error = '保存音频记录失败';
                \Yii::error($error .' '.var_export($voice->getErrors(),true));
                return false;
            }
        }
        return true;
    }
}