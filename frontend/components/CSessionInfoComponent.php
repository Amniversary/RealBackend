<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/9/26
 * Time: 下午3:28
 */

namespace frontend\components;


use common\models\CSessionInfo;
use frontend\business\SaveByTransUtil;
use frontend\business\SaveRecordByTransactions\SaveByTransaction\AddUserInfoSaveByTrans;


class CSessionInfoComponent
{
    /**
     * 登录用户信息处理
     * @param $params
     * @return bool|string
     * @throws \yii\db\Exception
     */
    public function ChangeSessionInfo($params)
    {
        $this->CheckAuth($params['id']);  //TODO: 检测对应小程序用户表是否存在
        $User = $this->GetUserByOpenId($params['id'], $params['openid']);
        $decode = json_decode(base64_decode($params['user_info']),true);
        if (!empty($User)) {
            //$params['uuid'] = $User->uuid;
            //TODO: 更新用户信息
            if(!$this->UpdateUser($params, $decode)){
                return false;
            }
            return $User['uuid'];
        }
        //TODO: 新增用户信息
        if(!$this->SaveUser($params, $decode)) {
            return false;
        }
        return true;
    }


    /**
     * 根据openid 获取 uuid
     * @param $id
     * @param $openId
     * @return array|false
     */
    public function GetUserByOpenId($id, $openId)
    {
        $sql = 'select * from cClient'.$id.' where open_id = "'.$openId.'"';
        return \Yii::$app->db->createCommand($sql)->queryOne();
    }

    /**
     *  根据UUid , skey 获取用户信息
     * @param $id
     * @param $uuid
     * @param $skey
     * @return array|false
     */
    public function GetSessionInfo($id ,$uuid, $skey)
    {
        $sql = 'select * from cClient'.$id.' where uuid = :uuid and skey = :skey;';
        return \Yii::$app->db->createCommand($sql, [
            ':uuid' => $uuid,
            ':skey' => $skey
        ])->queryOne();
//        return CSessionInfo::find()->where(['uuid' => $uuid, 'skey' => $skey])->one();
    }

    /**
     * 验证用户登录身份信息
     * @param $params
     * @return bool|string
     */
    public function CheckSessionForAuth($params)
    {
        $SessionInfo = $this->GetSessionInfo($params['id'], $params['uuid'], $params['skey']);
        if (empty($SessionInfo) || $SessionInfo == false) {
            return false;
        }
        $now_time = time();
        $create_time = strtotime($SessionInfo['create_time']);
        $last_visit_time = strtotime($SessionInfo['last_visit_time']);
        if(($now_time-$create_time) / 86400 > $params['login_duration']) return false;
        if(($now_time-$last_visit_time) > $params['session_duration']) return false;
//        $SessionInfo->last_visit_time = date('Y-m-d H:i:s', $now_time);
//        $SessionInfo->save();
        $sql = 'update cClient'.$params['id'].' set last_visit_time = :date where id = :id';
        \Yii::$app->db->createCommand($sql,[':date'=>date('Y-m-d H:i:s', $now_time), ':id'=> $SessionInfo['id']])->execute();
        return $SessionInfo;
    }

    /**
     * 插入用户信息
     * @param $params
     * @param $decode
     * @return bool
     * @throws \yii\db\Exception
     */
    private function SaveUser($params, $decode)
    {
        $transActions[] = new AddUserInfoSaveByTrans($params, $decode);
        if(!SaveByTransUtil::SaveByTransaction($transActions, $error, $out)) {
            \Yii::error($error);
            return false;
        }
        return true;
    }

    /**
     * 检测对应小程序用户表是否存在  (没有则创建)
     * @param $appid
     * @throws \yii\db\Exception
     */
    private function CheckAuth($appid)
    {
        $sql = 'CREATE TABLE IF NOT EXISTS `cClient'. $appid .'` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `nickName` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
              `open_id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
              `uuid` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
              `avatarUrl` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
              `real_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
              `phone` varchar(100) COLLATE utf8mb4_unicode_ci,
              `language` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
              `gender` int(11) DEFAULT NULL,
              `province` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
              `country` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
              `user_info` varchar(2048) COLLATE utf8mb4_unicode_ci NOT NULL,
              `city` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
              `skey` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
              `session_key` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
              `create_time` datetime NOT NULL,
              `last_visit_time` datetime NOT NULL,
              `remark1` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
              `remark2` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
              PRIMARY KEY (`id`),
              UNIQUE KEY `open_id` (`open_id`) USING BTREE,
              KEY `auth` (`uuid`,`skey`),
              KEY `weixin` (`open_id`,`session_key`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT=\'会话管理用户信息'.$appid .'\';
        COMMIT;';
        \Yii::$app->db->createCommand($sql)->execute();
    }

    /**
     * 更新用户信息
     * @param $params
     * @param $decode
     * @return bool
     * @throws \yii\db\Exception
     */
    private function UpdateUser($params, $decode)
    {
        $sql = 'update cClient'.$params['id'].' set nickName = "'.$decode['nickName'].'",avatarUrl = "'
            .$decode['avatarUrl'].'",language = "'.$decode['language'].'",city = "'.$decode['city']
            .'",gender = '.$decode['gender'].',province = "'.$decode['province'].'",country = "'
            .$decode['country'].'",session_key = "'.$params['session_key'].'",last_visit_time = "'
            .$params['last_visit_time'].'",skey = "'.$params['skey'].'",user_info = "'.
            $params['user_info'].'" where open_id = "'.$params['openid'].'";';
        $rst = \Yii::$app->db->createCommand($sql)->execute();
        if ($rst <= 0) {
            \Yii::error('更新小程序用户信息失败 : OpenId : ' . $params['openid'] . 'AppId : '. $params['id']);
            \Yii::error(\Yii::$app->db->createCommand($sql)->rawSql);
            return false;
        }
        return true;
    }
}