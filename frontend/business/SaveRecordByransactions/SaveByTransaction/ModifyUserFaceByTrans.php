<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/1
 * Time: 16:52
 */

namespace frontend\business\SaveRecordByransactions\SaveByTransaction;


use common\models\LivingPasswrodTicket;
use common\models\LivingPrivate;
use frontend\business\ApproveUtil;
use frontend\business\SaveRecordByransactions\ISaveForTransaction;
use yii\log\Logger;

/**
 * 修改人脸识别信息
 * Class ModifyUserFaceByTrans
 * @package frontend\business\SaveRecordByransactions\SaveByTransaction
 */
class ModifyUserFaceByTrans implements ISaveForTransaction
{
    private $extend_params =[];

    public function __construct($extend_params=[])
    {
        $this->extend_params = $extend_params;
    }

    function SaveRecordForTransaction(&$error,&$outInfo)
    {
        $insert_week_sql = 'insert ignore into mb_face_statistic(type,date,user_device_id,request_num) VALUES(:tp,:dt,:udid,:rnum)';
        $insert_month_sql = 'insert ignore into mb_face_statistic(type,date,user_device_id,request_num) VALUES(:tp,:dt,:udid,:rnum)';
        \Yii::$app->db->createCommand($insert_week_sql,[
            ':tp' => 1,
            ':dt' => $this->extend_params['date_week'],
            ':udid' => $this->extend_params['user_id'],
            ':rnum' => 0,
        ])->execute();
        \Yii::$app->db->createCommand($insert_month_sql,[
            ':tp' => 2,
            ':dt' => $this->extend_params['date_month'],
            ':udid' => $this->extend_params['device_no'],
            ':rnum' => 0,
        ])->execute();

        $update_week_sql = 'update mb_face_statistic set request_num=request_num+1 WHERE user_device_id=:udid and date=:dt and type=:tp';
        $update_month_sql = 'update mb_face_statistic set request_num=request_num+1 WHERE user_device_id=:udid and date=:dt and type=:tp';
        $week_update_res = \Yii::$app->db->createCommand($update_week_sql,[
            ':tp' => 1,
            ':dt' => $this->extend_params['date_week'],
            ':udid' => $this->extend_params['user_id'],
        ])->execute();
        $month_update_res = \Yii::$app->db->createCommand($update_month_sql,[
            ':tp' => 2,
            ':dt' => $this->extend_params['date_month'],
            ':udid' => $this->extend_params['device_no'],
        ])->execute();
        if(($week_update_res <= 0) && ($month_update_res <= 0))
        {
            $error = '认证失败';
            \Yii::getLogger()->log('人脸识别统计表写入信息失败  date==:'.var_export($this->extend_params,true),Logger::LEVEL_ERROR);
            return false;
        }

//        //设置缓存
        $cache_week_data = [
            'user_device_id' => $this->extend_params['user_id'],
            'date' => $this->extend_params['date_week'],
            'type' => 1,
            'request_num' => $this->extend_params['week_request_num']+1,
        ];
        $week_key = 'user_face_'.$this->extend_params['user_id'].'_'.$this->extend_params['date_week'];   //缓存ID号
        $user_face_id = \Yii::$app->cache->set($week_key,json_encode($cache_week_data),3*24*3600);

        $cache_month_data = [
            'user_device_id' => $this->extend_params['device_no'],
            'date' => $this->extend_params['date_month'],
            'type' => 2,
            'request_num' => $this->extend_params['month_request_num']+1,
        ];
        $month_key = 'user_face_'.$this->extend_params['device_no'].'_'.$this->extend_params['date_month'];   //缓存设备号
        $user_face_device = \Yii::$app->cache->set($month_key,json_encode($cache_month_data),3*24*3600);
        if((!$user_face_id) && (!$user_face_device))
        {
            $error = '认证失败';
            \Yii::getLogger()->log('人脸识别缓存信息失败  date==:'.var_export($this->extend_params,true),Logger::LEVEL_ERROR);
            return false;
        }

        return true;
    }
} 