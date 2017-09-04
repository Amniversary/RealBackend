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

    public function __construct($AuthInfo, $extend_params = [])
    {
        $this->AuthInfo = $AuthInfo;
        $this->extend = $extend_params;
    }

    function SaveRecordForTransaction(&$error, &$outInfo)
    {
        //TODO: 保存公众号授权基本信息
        $model = AuthorizationList::findOne(['authorizer_appid' => $this->AuthInfo['authorizer_appid']]);
        if ($model) {
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
        } else {
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

        if (!$model->save()) {
            $error = '保存授权公众号信息失败';
            \Yii::error($error . ' ：' . var_export($model->getErrors(), true));
            return false;
        }
        $time = date('Y-m-d');
        $date = date('Y-m-d H:i:s');
        $temp = AuthorizerUtil::isVerify($model->verify_type_info);

        $sql = 'insert ignore into wc_fans_statistics(app_id,new_user,cancel_user,net_user,total_user,statistics_date) VALUES(:ap,0,0,0,0,:tm)';
        \Yii::$app->db->createCommand($sql, [
            ':ap' => $model->record_id,
            ':tm' => $time
        ])->execute();

        $insersql = 'insert ignore into wc_statistics_count(app_id,count_user,cumulate_user,update_time) VALUES (:ap,0,0,:date)';
        \Yii::$app->db->createCommand($insersql, [
            ':ap' => $model->record_id,
            ':date' => $date,
        ])->execute();

        if ($temp) {
            //TODO： 获取公众号粉丝数
            if (!WeChatUserUtil::getWxFansAccumulate($model->authorizer_access_token, $res, $error)) {
                \Yii::error('获取粉丝数失败 :' . $error);
                return false;
            }
            $data = $res['list'][0];
            $upsql = 'update wc_fans_statistics set total_user = :total, remark1 = :dt WHERE app_id = :appid and statistics_date = :date';
            $rst = \Yii::$app->db->createCommand($upsql, [
                ':appid' => $model->record_id,
                ':total' => intval($data['cumulate_user']),
                ':dt' => $date,
                ':date' => $time
            ])->execute();
            if ($rst <= 0) {
                $error = '更新每日统计粉丝信息失败';
                \Yii::error($error . ' : ' . \Yii::$app->db->createCommand($upsql, [
                        ':appid' => $model->record_id,
                        ':total' => intval($data['cumulate_user']),
                        ':dt' => $date,
                        ':date' => $time])->rawSql);
                return false;
            }

            $up_count = 'update wc_statistics_count set count_user = :cu,cumulate_user = :cumu,remark1= :dt WHERE app_id = :apd';
            $result = \Yii::$app->db->createCommand($up_count, [
                ':cu' => intval($data['cumulate_user']),
                ':cumu' => intval($data['cumulate_user']),
                ':apd' => $model->record_id,
                ':dt' => $date,
            ])->execute();
            if ($result <= 0) {
                $error = '更新粉丝累计统计失败:';
                \Yii::error($error . ' :' . \Yii::$app->db->createCommand($up_count, [
                        ':cu' => intval($data['cumulate_user']),
                        ':cumu' => intval($data['cumulate_user']),
                        ':apd' => $model->record_id,
                        ':dt' => $date,
                    ])->rawSql);
                return false;
            }
        }
        return true;
    }
}