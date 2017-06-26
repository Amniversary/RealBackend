<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/4/22
 * Time: 13:15
 */

namespace frontend\business;


use common\models\Job;
use Pheanstalk\PheanstalkInterface;
use yii\log\Logger;

class JobUtil
{
    const DEAL_STATUS=1;//受理中
    const FINISH_STATUS=3;//完成
    CONST FAIL_STATUS=2;//失败
    /**
     * 获取job新模型
     * @param $attrs
     * @return Job
     */
    public static function GetNewModel($attrs)
    {
        $model = new Job();
        $model->attributes = $attrs;
        $model->create_time=date('Y-m-d H:i:s');
        return $model;
    }

    /**
     * 根据id获取任务
     * @param $jobId
     */
    public static function GetJobById($new_job_id)
    {
        return Job::findOne(['new_job_id'=>$new_job_id]);
    }

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
     * 增加job到beanstalk的，同时往本地数据库增加一条记录
     * @param $key_word
     * @param $data
     * @param $error
     * @return bool
     */
    public static function AddJob($key_word,$data,&$error)
    {
        //加入异步任务处理
        $jobId =\Yii::$app->beanstalk->putInTube($key_word,$data);
        if($jobId <= 0)
        {
            $error = '任务id异常:';
            return false;
        }
        /*$attrs = [
            'job_id'=>$jobId,
            'key_word'=>$key_word,
            'status'=>'1',
            'param_data'=>json_encode($data)
        ];
        $job = self::GetNewModel($attrs);
        if(empty($job->job_id) ||
         empty($job->key_word) ||
        empty($job->status)||
            empty($job->param_data)
        )
        {
            $error = '参数缺少';
            return false;
        }
        if(!$job->save())
        {
            $error = '保存任务失败';
            \Yii::getLogger()->log($error.' :'.var_export($job->getErrors(),true),Logger::LEVEL_ERROR);
            return false;
        }*/
        return true;
    }

    /**
     * 往mbjob任务队列中加入任务
     * @param $key_word
     * @param $data
     * @param $error
     * @return bool
     */
    public static function AddMbJob($key_word,$data,&$error)
    {
        $sendData = ['data'=>$data];
        $jobId = \Yii::$app->jobBeanstalk->putInTube($key_word,$sendData);
        if($jobId <= 0)
        {
            $error = 'mbjob任务id异常';
            return false;
        }
        return true;
    }

    /**
     * 增加job到api队列
     * @param $key_word
     * @param $data
     * @param $error
     * @return bool
     */
    public static function AddApiJob($key_word,$data,&$error)
    {
        $jobId = \Yii::$app->apiBeanstalk->putInTube($key_word,$data);
        if($jobId <= 0)
        {
            $error = 'Api任务id异常';
            return false;
        }
        return true;
    }
    /**
     * 增加job到点赞队列,同时往数据库添加一条记录
     * @param $key_word
     * @param $data
     * @param $error
     * @return bool
     */
    public static function AddClickLikeJob($key_word,$data,&$error)
    {
        $jobId = \Yii::$app->likeBeanstalk->putInTube($key_word,$data);
        if($jobId <= 0)
        {
            $error = 'ClickLike任务id异常';
            return false;
        }

        return true;
    }

    /**
     * 增加job到主播收到票数队列,同时往数据库添加一条记录
     * @param $key_word
     * @param $data
     * @param $error
     * @return bool
     */
    public static function AddGetTicketJob($key_word,$data,&$error)
    {
        $jobId = \Yii::$app->getTicketBeanstalk->putInTube($key_word,$data);
        if($jobId <= 0)
        {
            $error = 'GetTicket任务id异常';
            return false;
        }

        return true;
    }

    /**
     * 增加job到直播票数处理队列,同时往数据库添加一条记录
     * @param $key_word
     * @param $data
     * @param $error
     * @return bool
     */
    public static function AddLivingTicketJob($key_word,$data,&$error)
    {
        $jobId = \Yii::$app->livingTicketBeanstalk->putInTube($key_word,$data);
        if($jobId <= 0)
        {
            $error = 'LivingTicket任务id异常';
            return false;
        }

        return true;
    }

    /**
     * 增加job到经验队列,同时往数据库添加一条记录
     * @param $key_word
     * @param $data
     * @param $error
     * @return bool
     */
    public static function AddExpJob($key_word,$data,&$error)
    {
        $jobId = \Yii::$app->expBeanstalk->putInTube($key_word,$data);
        if($jobId <= 0)
        {
            $error = 'Exp任务id异常';
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
    public static function AddCustomJob($jobServer,$tubeName,$paramData,&$error)
    {
        $server = \Yii::$app->$jobServer;
        if(!isset($server))
        {
            $error = '队列服务不存在，服务名称：'.$jobServer;
            return false;
        }
        $jobId = \Yii::$app->$jobServer->putInTube($tubeName,$paramData);
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

    /**
     * 增加job到热门队列, 同时往数据库添加一条记录
     * @param $key_word
     * @param $data
     * @param $error
     * @return bool
     */
    public static function AddHotJob($key_word,$data,&$error)
    {
        $jobId = \Yii::$app->hotBeanstalk->putInTube($key_word,$data);
        if($jobId <= 0)
        {
            $error = 'hot任务id异常';
            return false;
        }

        return true;
    }

    /**
     * 增加job到人数队列,同时往数据库添加一条记录
     * @param $key_word
     * @param $data
     * @param $error
     * @return bool
     */
    public static function AddPeopleJob($key_word,$data,&$error)
    {

        $jobId = \Yii::$app->peopleBeanstalk->putInTube($key_word,$data);
        if($jobId <= 0)
        {
            $error = 'people任务id异常';
            return false;
        }
        return true;
    }



    /**
     * 增加job到Im队列,同时往数据库添加一条记录
     * @param $key_word
     * @param $data
     * @param $error
     * @return bool
     */
    public static function AddImJob($key_word,$data,&$error)
    {

        $jobId = \Yii::$app->ImBeanstalk->putInTube($key_word,$data);
        if($jobId <= 0)
        {
            $error = 'im任务id异常';
            return false;
        }
        return true;
    }

    /**
     * 增加job到pic队列,同时往数据库添加一条记录
     * @param $key_word
     * @param $data
     * @param $error
     * @return bool
     */
    public static function AddPicJob($key_word,$data,&$error)
    {

        $jobId = \Yii::$app->dealPicBeanstalk->putInTube($key_word,$data);
        if($jobId <= 0)
        {
            $error = 'pic任务id异常';
            return false;
        }
        return true;
    }

    /**
     * 增加job到attention队列,同时往数据库添加一条记录
     * @param $key_word
     * @param $data
     * @param $error
     * @return bool
     */
    public static function AddAttentionJob($key_word,$data,&$error)
    {
        $jobId = \Yii::$app->attentionBeanstalk->putInTube($key_word,$data);
        if($jobId <= 0)
        {
            $error = 'attention任务id异常';
            return false;
        }
        return true;
    }

    /**
     * 增加job到api_log队列,同时往数据库添加一条记录
     * @param $key_word
     * @param $data
     * @param $error
     * @return bool
     */
    public static function AddApiLogJob($key_word,$data,&$error)
    {
        $jobId = \Yii::$app->apilogBeanstalk->putInTube($key_word,$data);
        if($jobId <= 0)
        {
            $error = 'ApiLog任务id异常';
            return false;
        }

        return true;
    }

    /**
     * 增加job，统计日活/月活
     * @param $key_word
     * @param $data
     * @param $error
     * @return bool
     */
    public static function AddApiStatisticJob($key_word,$data,&$error)
    {
        $jobId = \Yii::$app->apilogstatisticBeanstalk->putInTube($key_word,$data);
        if($jobId <= 0)
        {
            $error = 'ApiLogStatistic任务id异常';
            return false;
        }

        return true;
    }

    /**
     * 增加job到礼物积分队列
     * @param $key_word
     * @param $data
     * @param $error
     * @return bool
     */
    public static function AddGiftScoreJob($key_word,$data,&$error)
    {
        $jobId = \Yii::$app->scoreboardBeanstalk->putInTube($key_word,$data);
        if($jobId <= 0)
        {
            $error = 'GiftScore任务id异常';
            return false;
        }
        return true;
    }

    /**
     * 增加job到分享信息处理队列
     * @param $key_word
     * @param $data
     * @param $error
     * @return bool
     */
    public static function AddShareLivingJob($key_word,$data,&$error)
    {
        $jobId = \Yii::$app->sharelivingBeanstalk->putInTube($key_word,$data);
        if($jobId <= 0)
        {
            $error = 'ShareLiving任务id异常';
            return false;
        }
        return true;
    }
    /**
     * 延迟1秒，增加job到Im队列,同时往数据库添加一条记录
     * @param $key_word
     * @param $data
     * @param $delay     延迟（秒）
     * @param $error
     * @return bool
     */
    public static function AddImDelayJob($key_word,$data,$delay,&$error)
    {

        $jobId = \Yii::$app->ImBeanstalk->putInTube($key_word,$data,PheanstalkInterface::DEFAULT_PRIORITY,$delay);
        if($jobId <= 0)
        {
            $error = 'im任务id异常2';
            return false;
        }
        return true;
    }

    /**
     * 增加job到发送世界礼物队列
     * @param $key_word
     * @param $data
     * @param $error
     * @return bool
     */
    public static function AddWorldGiftJob($key_word,$data,&$error)
    {
        $jobId = \Yii::$app->WorldGiftBeanstalk->putInTube($key_word,$data);
        if($jobId <= 0)
        {
            $error = 'WorldGift任务id异常';
            return false;
        }
        return true;
    }

    /**
     * 增加job到新增动态队列
     * @param $key_word
     * @param $data
     * @param $error
     * @return bool
     */
    public static function AddDynamicJob($key_word,$data,&$error)
    {
        $jobId = \Yii::$app->DynamicBeanstalk->putInTube($key_word,$data);
        if($jobId <= 0)
        {
            $error = 'Dynamic任务id异常';
            return false;
        }
        return true;
    }


    /**
     * 更新任务状态
     * @param int $status
     * @param $error
     */
    public static function UpdateJobStatus($job_id, $status=JobUtil::FINISH_STATUS,&$error)
    {
        $sql = 'update mb_job set status=:su where job_id=:jid and status < :su1';
        $rst = \Yii::$app->db->createCommand($sql,[
                 ':su'=>$status,
                ':jid'=>$job_id,
                ':su1'=>$status
            ])->execute();
        if($rst <= 0)
        {
            $error = '状态错误';
            return false;
        }
        return true;
    }
} 