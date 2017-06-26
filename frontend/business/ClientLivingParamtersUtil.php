<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/7/28
 * Time: 17:25
 */

namespace frontend\business;


use common\models\ClientLivingParameters;
use yii\db\Query;
use yii\log\Logger;

class ClientLivingParamtersUtil
{
    /**
     * 根据unique_id获取七牛直播参数信息
     * @param $unique_no
     * @return array|bool
     */
    public static function  QueryClientLivingParamtersByUniqueNo($unique_no)
    {
        $query = (new Query())
            ->select(['ct.client_id','clp.fps','clp.profilelevel','clp.video_bit_rate','clp.width','clp.height','clp.parameters_more','clp.user_id'])
            ->from('mb_client ct')
            ->leftJoin('mb_client_living_parameters clp','clp.user_id = ct.client_id')
            ->where(['ct.unique_no'=>$unique_no])
            ->one();
        return $query;
    }

    /**
     * 获取client_living_parameters 表信息
     * @param $client_id
     * @return null|static
     */
    public static function GetClientLivingParamtersInfo($client_id)
    {
        return ClientLivingParameters::findOne(['user_id' => $client_id]);
    }

    /**
     * 获取七牛直播参数，如果没有加入默认
     * @param $unique_no
     * @param $error
     * @return array|bool
     */
    public static function GetClientLivingParamtersByUniqueNo($unique_no,&$error)
    {
        $model = self::QueryClientLivingParamtersByUniqueNo($unique_no);
        //\Yii::getLogger()->log('$model === :'.var_export($model,true),Logger::LEVEL_ERROR);
        if($model === false || !isset($model['client_id']))
        {
            $error = '用户不存在';
            return false;
        }
        if(!isset($model['user_id']))
        {
            $parameters_more = array(
                array('quality_id'=>1,'fps' => 15,'profilelevel' => 30,'video_bit_rate' => 300,'width' => 368,'height' => 640,'order_no'=>1),
                array('quality_id'=>2,'fps' => 15,'profilelevel' => 30,'video_bit_rate' => 500,'width' => 368,'height' => 640,'order_no'=>2),
                array('quality_id'=>3,'fps' => 25,'profilelevel' => 30,'video_bit_rate' => 800,'width' => 368,'height' => 640,'order_no'=>3),
            );
            $parameters_more = json_encode($parameters_more);
            $ml = new ClientLivingParameters();
            $ml->user_id = $model['client_id'];
            $ml->quality_id =1;
            $ml->fps = 20;
            $ml->profilelevel = 30;
            $ml->video_bit_rate = 800;
            $ml->width = 368;
            $ml->height = 640;
            $ml->parameters_more = $parameters_more;
            if(!$ml->save())
            {
                $error = '保存七牛直播参数失败';
                \Yii::getLogger()->log('保存七牛直播参数失败   error===:'.var_export($ml->getErrors(),true),Logger::LEVEL_ERROR);

                return false;
            }
            $model['fps'] = $ml->fps;
            $model['profilelevel'] = $ml->profilelevel;
            $model['video_bit_rate'] = $ml->video_bit_rate;//转为字节
            $model['parameters_more'] = $parameters_more;
        }
        else
        {
            if(empty($model['parameters_more']))
            {
                $parameters_more = array(
                    array('quality_id'=>1,'fps' => 15,'profilelevel' => 30,'video_bit_rate' => 300,'width' => 368,'height' => 640,'order_no'=>1),
                    array('quality_id'=>2,'fps' => 15,'profilelevel' => 30,'video_bit_rate' => 500,'width' => 368,'height' => 640,'order_no'=>2),
                    array('quality_id'=>3,'fps' => 25,'profilelevel' => 30,'video_bit_rate' => 800,'width' => 368,'height' => 640,'order_no'=>3),
                );
                $parameters_more = json_encode($parameters_more);
                $ml = self::GetClientLivingParamtersInfo($model['client_id']);
                $ml->user_id = $model['client_id'];
                $ml->quality_id =1;
                $ml->fps = 20;
                $ml->profilelevel = 30;
                $ml->video_bit_rate = 800;
                $ml->width = 368;
                $ml->height = 640;
                $ml->parameters_more = $parameters_more;
                if(!$ml->save())
                {
                    $error = '更新七牛直播参数失败';
                    \Yii::getLogger()->log('更新七牛直播参数失败   error===:'.var_export($ml->getErrors(),true),Logger::LEVEL_ERROR);
                    return false;
                }
                $model['fps'] = $ml->fps;
                $model['profilelevel'] = $ml->profilelevel;
                $model['video_bit_rate'] = $ml->video_bit_rate;//转为字节
                $model['parameters_more'] = $parameters_more;
            }
        }


        $params_return = json_decode($model['parameters_more'],true);
        $params_return[0]['video_bit_rate'] = intval($params_return[0]['video_bit_rate'])*1024;
        return $params_return[0];
    }

    public static function GetMoreClientLivingParamtersByUniqueNo($unique_no,&$error)
    {
        $model = self::QueryClientLivingParamtersByUniqueNo($unique_no);
        if($model === false || !isset($model['client_id']))
        {
            $error = '用户不存在';
            return false;
        }

        if(!isset($model['user_id']))
        {
            $parameters_more = array(
                array('quality_id'=>1,'fps' => 15,'profilelevel' => 30,'video_bit_rate' => 300,'width' => 368,'height' => 640,'order_no'=>1),
                array('quality_id'=>2,'fps' => 15,'profilelevel' => 30,'video_bit_rate' => 500,'width' => 368,'height' => 640,'order_no'=>2),
                array('quality_id'=>3,'fps' => 25,'profilelevel' => 30,'video_bit_rate' => 800,'width' => 368,'height' => 640,'order_no'=>3),
            );

            $parameters_more = json_encode($parameters_more);
            $ml = new ClientLivingParameters();
            $ml->user_id = $model['client_id'];
            $ml->quality_id =1;
            $ml->fps = 20;
            $ml->profilelevel = 30;
            $ml->video_bit_rate = 800;
            $ml->width = 368;
            $ml->height = 640;
            $ml->parameters_more = $parameters_more;
            if(!$ml->save())
            {
                $error = '保存七牛直播参数失败';
                return false;
            }
            $model['fps'] = $ml->fps;
            $model['profilelevel'] = $ml->profilelevel;
            $model['video_bit_rate'] = $ml->video_bit_rate;//转为字节
            $model['parameters_more'] = $ml->parameters_more;
        }
        else
        {
            if(empty($model['parameters_more']))
            {
                $parameters_more = array(
                    array('quality_id'=>1,'fps' => 15,'profilelevel' => 30,'video_bit_rate' => 300,'width' => 368,'height' => 640,'order_no'=>1),
                    array('quality_id'=>2,'fps' => 15,'profilelevel' => 30,'video_bit_rate' => 500,'width' => 368,'height' => 640,'order_no'=>2),
                    array('quality_id'=>3,'fps' => 25,'profilelevel' => 30,'video_bit_rate' => 800,'width' => 368,'height' => 640,'order_no'=>3),
                );

                $parameters_more = json_encode($parameters_more);
                $ml = self::GetClientLivingParamtersInfo($model['client_id']);
                $ml->user_id = $model['client_id'];
                $ml->quality_id =1;
                $ml->fps = 20;
                $ml->profilelevel = 30;
                $ml->video_bit_rate = 800;
                $ml->width = 368;
                $ml->height = 640;
                $ml->parameters_more = $parameters_more;
                if(!$ml->save())
                {
                    $error = '更新七牛直播参数失败';
                    return false;
                }
                $model['parameters_more'] = $ml->parameters_more;
            }
        }



        return json_decode($model['parameters_more'],true);
    }
} 