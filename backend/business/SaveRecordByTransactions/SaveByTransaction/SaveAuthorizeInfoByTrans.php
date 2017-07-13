<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/7/12
 * Time: 上午11:57
 */

namespace backend\business\SaveRecordByTransactions\SaveByTransaction;



use backend\business\AuthorizerUtil;
use backend\business\SaveRecordByTransactions\ISaveForTransaction;
use backend\business\WeChatUserUtil;
use common\models\AuthorizationList;

class SaveAuthorizeInfoByTrans implements ISaveForTransaction
{
    public $AuthInfo;
    public $extend;

    public function __construct($AuthInfo,$extend_params = [])
    {
        $this->AuthInfo = $AuthInfo;
        $this->extend = $extend_params;
    }

    function SaveRecordForTransaction(&$error,&$outInfo)
    {
        //TODO: 保存公众号授权基本信息
        $model = AuthorizationList::findOne(['authorizer_appid'=>$this->AuthInfo['authorizer_appid']]);
        if($model){
            $model->authorizer_access_token = $this->AuthInfo['authorizer_access_token'];
            $model->authorizer_refresh_token = $this->AuthInfo['authorizer_refresh_token'];
            $model->func_info = json_encode($this->AuthInfo['func_info']);
            $model->nick_name = $this->extend['nick_name'];
            $model->head_img = $this->extend['head_img'];
            $model->service_type_info = $this->extend['service_type_info']['id'];
            $model->verify_type_info = $this->extend['verify_type_info']['id'];
            $model->alias = $this->extend['alias'];
            $model->qrcode_url = $this->extend['qrcode_url'];
            $model->business_info = json_encode($this->extend['business_info']);
            $model->signature = $this->extend['signature'];
            $model->authorization_info = json_encode($this->AuthInfo);
            $model->update_time = date('Y-m-d H:i:s');
        }else{
            $model = new AuthorizationList();
            $model->authorizer_appid = $this->AuthInfo['authorizer_appid'];
            $model->authorizer_access_token = $this->AuthInfo['authorizer_access_token'];
            $model->authorizer_refresh_token = $this->AuthInfo['authorizer_refresh_token'];
            $model->func_info = json_encode($this->AuthInfo['func_info']);
            $model->status = 1;
            $model->user_id = \Yii::$app->user->id;
            $model->nick_name = $this->extend['nick_name'];
            $model->head_img = $this->extend['head_img'];
            $model->service_type_info = $this->extend['service_type_info']['id'];
            $model->verify_type_info = $this->extend['verify_type_info']['id'];
            $model->user_name = $this->extend['user_name'];
            $model->alias = $this->extend['alias'];
            $model->qrcode_url = $this->extend['qrcode_url'];
            $model->business_info = json_encode($this->extend['business_info']);
            $model->idc = $this->extend['idc'];
            $model->principal_name = $this->extend['principal_name'];
            $model->signature = $this->extend['signature'];
            $model->authorization_info = json_encode($this->AuthInfo);
            $model->create_time = date('Y-m-d H:i:s');
            $model->update_time = '';
        }

        if(!$model->save()) {
            $error = '保存授权公众号信息失败';
            \Yii::error($error .' ：' .var_export($model->getErrors(),true));
            return false;
        }
        $time = date('Y-m-d');
        $temp = AuthorizerUtil::isVerify($model->verify_type_info);

        $sql = 'insert ignore into wc_fans_statistics(app_id,new_user,cancel_user,net_user,total_user,statistics_date) VALUES(:ap,0,0,0,0,:tm)';
        \Yii::$app->db->createCommand($sql,[
            ':ap'=>$model->record_id,
            ':tm'=>$time
        ])->execute();

        $insql = 'insert ignore into wc_statistics_count(app_id,count_user,cumulate_user,update_time) VALUES (:app,:total,:late,:tim)';
        \Yii::$app->db->createCommand($insql,[
            ':app'=>$model->record_id,
            ':total'=>0,
            ':late'=>0,
            ':tim'=>date('Y-m-d H:i:s')
        ])->execute();

        if($temp) {
            //TODO： 获取公众号粉丝数
            if(!WeChatUserUtil::getWxFansAccumulate($model->authorizer_access_token,$res,$error)){
                \Yii::error('获取粉丝数失败 :'.$error);
                return false;
            }
            $data = $res['list'][0];
            $upsql = 'update wc_fans_statistics set total_user = :total WHERE app_id = :appid and statistics_date = :date';
            $rst = \Yii::$app->db->createCommand($upsql,[
                ':appid'=>$model->record_id,
                ':total'=>$data['cumulate_user'],
                ':date'=>$time
            ])->execute();
            if($rst <= 0){
                $error = '更新每日统计粉丝信息失败';
                \Yii::error($error . ' : '.\Yii::$app->db->createCommand($upsql,[
                        ':total'=>$data['cumulate_user'],
                        ':date'=>$time])->rawSql);
                return false;
            }

            $update = 'update wc_statistics_count set count_user = :total,cumulate_user = :late,update_time = :up WHERE app_id = :app';
            $excute = \Yii::$app->db->createCommand($update,[
                ':app'=>$model->record_id,
                ':total' =>$data['cumulate_user'],
                ':late' => $data['cumulate_user'],
                ':up'=>date('Y-m-d H:i:s'),
            ])->execute();
            if($excute <= 0){
                $error = '更新公众号粉丝统计数失败';
                \Yii::error($error .' : '.\Yii::$app->db->createCommand($update,[
                        ':total' =>$data['cumulate_user'],
                        ':late' => $data['cumulate_user'],
                        ':up'=>date('Y-m-d H:i:s'),
                    ])->rawSql);
                return false;
            }
        }
        return true;
    }
}