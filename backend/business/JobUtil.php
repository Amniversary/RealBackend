<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/4/22
 * Time: 13:15
 */

namespace backend\business;


use common\models\Job;
use Pheanstalk\PheanstalkInterface;
use yii\log\Logger;

class JobUtil
{
    const DEAL_STATUS=1;//受理中
    const FINISH_STATUS=3;//完成
    CONST FAIL_STATUS=2;//失败

    const DEFAULT_PRIORITY = 1024;
    const DEFAULT_DELAY = 0;
    const DEFAULT_TTR = 60;

    /**
     * 生成job模型
     * @param $jobId
     * @param $new_job_id
     * @param $key_word
     * @param $data
     * @return Job
     */
    public static function GenJobModel($jobId,$new_job_id,$key_word,$data)
    {
        $attrs = [
            'job_id'=>$jobId,
            'new_job_id' => $new_job_id,
            'key_word'=>$key_word,
            'status'=>'1',
            'param_data'=>json_encode($data)
        ];
        $model = self::GetNewModel($attrs);
        return $model;
    }

    /**
     * 将任务添加到数据库
     * @param $jobId
     * @param $key_word
     * @param $data
     * @param $error
     * @return bool
     */
    public static function AddJobToDb($jobId,$new_job_id,$key_word,$data,&$error,&$outInfo)
    {
        $attrs = [
            'job_id'=>$jobId,
            'new_job_id' => $new_job_id,
            'key_word'=>$key_word,
            'status'=>'1',
            'param_data'=>json_encode($data)
        ];
        $outInfo = self::GetNewModel($attrs);
        if(empty($outInfo->job_id) ||
            empty($outInfo->key_word) ||
            empty($outInfo->status)||
            empty($outInfo->new_job_id)||
            empty($outInfo->param_data)
        )
        {
            $error = '参数缺少';
            return false;
        }

        if(!$outInfo->save())
        {
            $error = '保存任务失败';
            \Yii::getLogger()->log($error.' :'.var_export($outInfo->getErrors(),true),Logger::LEVEL_ERROR);
            return false;
        }
        return true;
    }


    /**
     * 加入任务
     * @param $jobServer //队列服务器名称，在common/config/main.php的components节下的beanstalk服务器名称
     * @param $tubeName //队列名称
     * @param $paramData  //数据
     * @param $error
     * @return bool
     */
    public static function AddCustomJob($jobServer,$tubeName,$paramData,&$error, $ttr = self::DEFAULT_TTR)
    {
        $server = \Yii::$app->$jobServer;
        if(!isset($server))
        {
            $error = '队列服务不存在，服务名称：'.$jobServer;
            return false;
        }
        $jobId = \Yii::$app->$jobServer->putInTube($tubeName,$paramData,self::DEFAULT_PRIORITY, self::DEFAULT_DELAY, $ttr);
        if($jobId <= 0)
        {
            $error = '任务id异常';
            return false;
        }
        return true;
    }

    /**
     * 取出并删除任务
     * @param $jobServer
     * @param $tubeName
     * @param $paramData
     * @param $error
     * @return bool
     */
    public static function GetCustomJob($jobServer,$tube,&$rebot,&$error)
    {
        $server = \Yii::$app->$jobServer;
        if(!isset($server))
        {
            $error = '队列服务不存在，服务名称：'.$jobServer;
            return false;
        }
        $bean = $server->watch($tube);
        if(!$bean)
        {
            $error = 'tube not exists';
            return false;
        }
        $job = $bean->reserve(0);//不进行等待
        if(!$job)
        {
            $error = '没有任务了';
            return false;
        }
        $rebot = $job->getData();
        //删除job
        $bean->delete($job);
        return true;
    }

} 