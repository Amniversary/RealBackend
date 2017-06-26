<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/1
 * Time: 17:18
 */

namespace frontend\business;

use common\models\StopLiving;
use common\models\StopLivingLog;
use common\models\Client;
use common\models\Living;
use common\models\User;
use common\components\PhpLock;
use yii\db\Query;
use yii\log\Logger;
use yii\db\Transaction;

class StopLivingUtil
{
    /*
     * 封播
     * $living_id,$manage_id,$operate_type
     * retrun true or false
     */
    public static function StopLiving($living_id,$manage_id,$operate_type,&$error){

        $phpLock = new PhpLock("stop_living_add");
        $phpLock->lock();
        $trans = \Yii::$app->db->beginTransaction(Transaction::REPEATABLE_READ);
        $stopLiving = StopLiving::findOne(['living_id'=>$living_id]);
        if ( $stopLiving )
        {
            $stopLiving->status = 1;
            $stopLiving->create_date = date('Y-m-d H:i:s');
            if( !$stopLiving->save() ){
                $trans->rollBack();
                $phpLock->unlock();
                \Yii::getLogger()->log('更改封播状态时发生了错误：=='.var_export($stopLiving->getErrors()),Logger::LEVEL_ERROR);
                return false;
            }

            $error = '直播间封播成功';
            $trans->commit();
            $phpLock->unlock();
            self::StopLivingLog( $living_id,$manage_id,$operate_type,$error );
            return true;
        }
        else
        {
            $model = new StopLiving();
            $model->living_id = $living_id;
            $model->status = 1;
            $model->create_date = date('Y-m-d H:i:s');
            if( $model->save() )
            {
                if( self::StopLivingLog( $living_id,$manage_id,$operate_type,$error ) )
                {
                    $trans->commit();
                    $phpLock->unlock();
                    return true;
                }

            }else
            {
                \Yii::getLogger()->log('更改封播状态时发生了错误：=='.var_export($model->getErrors()),Logger::LEVEL_ERROR);
                return false;
            }
        }

        $trans->commit();
        $phpLock->unlock();
        return true;
    }

    private  function  StopLivingLog( $living_id,$manage_id,$operate_type,&$error )
    {
        $query = (new Query());
        $data = $query->select(['mc.client_id', 'mc.nick_name'])
            ->from('mb_living ml')
            ->innerJoin('mb_client mc', 'ml.living_master_id = mc.client_id')
            ->where(['ml.living_id' => $living_id])
            ->one();

        $log = new StopLivingLog();
        $log->living_id = $living_id;
        $log->client_id = $data['client_id'];
        $log->nick_name = $data['nick_name'];

        if ($operate_type == 1)
        {
            $client = Client::findOne(['client_id' => $manage_id]);
            $log->manage_id = $manage_id;
            $log->manage_name = $client->nick_name;
        } else if ($operate_type == 2)
        {
            $user = User::findOne(['backend_user_id' => $manage_id]);
            $log->manage_id = $manage_id;
            $log->manage_name = $user->username;
        } else if ($operate_type == 3)
        {
            $log->manage_id = '';
            $log->manage_name = \Yii::$app->user->identity->username;
        }
        $log->manage_type = 1;
        $log->operate_type = $operate_type;
        $log->create_date = date('Y-m-d H:i:s');
        if (!$log->save())
        {
            $error = '添加封播日记时发生了错误：==' . var_export($log->getErrors());
            \Yii::getLogger()->log($error, Logger::LEVEL_ERROR);
            return false;
        }
        return true;
    }




}