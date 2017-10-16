<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/10/12
 * Time: 下午2:43
 */

namespace frontend\business\SaveRecordByTransactions\SaveByTransaction;


use common\components\UsualFunForStringHelper;
use common\models\CBalance;
use frontend\business\SaveRecordByTransactions\ISaveForTransaction;

class AddUserInfoSaveByTrans implements ISaveForTransaction
{
    public $data;
    public $extend;

    public function __construct($data, $extend = [])
    {
        $this->data = $data;
        $this->extend = $extend;
    }

    function SaveRecordForTransaction(&$error, &$outInfo)
    {
        $sql = 'insert into cClient'.$this->data['id'].' set nickName = :nN, avatarUrl = :pic, language = :lang, gender = :sex, province = :province, country = :country, city =:city, uuid = :uuid, skey = :skey, create_time = :creTime,last_visit_time = :lastTime,
open_id = :openid,session_key= :session ,user_info=:userinfo, real_name = "", phone = ""';
        $rst = \Yii::$app->db->createCommand($sql,[
            ':nN' => $this->extend['nickName'],
            ':pic' => $this->extend['avatarUrl'],
            ':lang' => $this->extend['language'],
            ':city' => $this->extend['city'],
            ':sex' => $this->extend['gender'],
            ':province' => $this->extend['province'],
            ':country' => $this->extend['country'],
            ':uuid' => $this->data['uuid'],
            ':skey' => $this->data['skey'],
            ':creTime' => $this->data['create_time'],
            ':lastTime' => $this->data['last_visit_time'],
            ':openid' => $this->data['openid'],
            ':session' => $this->data['session_key'],
            ':userinfo' => $this->data['user_info']
        ])->execute();
        if ($rst <= 0 || $rst == false) {
            $error = '系统错误, 初始化用户信息失败 ';
            \Yii::error($error . ' ' . \Yii::$app->db->createCommand($sql,[
                    ':nN' => $this->extend['nickName'],
                    ':pic' => $this->extend['avatarUrl'],
                    ':lang' => $this->extend['language'],
                    ':city' => $this->extend['city'],
                    ':sex' => $this->extend['gender'],
                    ':province' => $this->extend['province'],
                    ':country' => $this->extend['country'],
                    ':uuid' => $this->data['uuid'],
                    ':skey' => $this->data['skey'],
                    ':creTime' => $this->data['create_time'],
                    ':lastTime' => $this->data['last_visit_time'],
                    ':openid' => $this->data['openid'],
                    ':session' => $this->data['session_key'],
                    ':userinfo' => $this->data['user_info']
                ])->rawSql);
            return false;
        }

        $user_id = \Yii::$app->db->lastInsertID;

        $model = new CBalance();
        $model->user_id = $user_id;
        $model->balance = 0;
        $model->recharge_num = 0;
        $model->reward_num = 0;
        $model->status = 1;
        $model->rand_str = UsualFunForStringHelper::mt_rand_str(40);
        if(!$model->save()) {
            $error = '系统错误, 用户账户信息初始化失败 ';
            \Yii::error($error . ' ' . var_export($model->getErrors(),true));
            return false;
        }

        $update = 'update cBalance set sign=MD5(
                   CONCAT(
                      \'balance_id=\', id,
                      \'&user_id=\', user_id,
                      \'&balance=\', balance,
                      \'&recharge_num=\', recharge_num,
                      \'&reward_num=\', reward_num,
                      \'&rand_str=\', rand_str,
                      \'chise1bht0z=lkc12i8xzh4wnmz90qnmxca2zqwdc9wqxxzjstlq\'
                   ))
                   WHERE user_id = :uid';
        $rst = \Yii::$app->db->createCommand($update, [':uid'=> $user_id])->execute();
        if($rst <= 0 || $rst == false) {
            $error = '系统错误, 用户账户余额签名更新失败';
            \Yii::error($error . ' ' . \Yii::$app->db->createCommand($update,[':uid'=>$user_id])->rawSql);
            return false;
        }

        return true;
    }
}