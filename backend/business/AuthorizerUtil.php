<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/7/1
 * Time: 下午3:55
 */

namespace backend\business;


use common\models\Alarm;
use common\models\AttentionEvent;
use common\models\AuthorizationList;
use common\models\AuthorizationMenu;
use common\models\Client;
use common\models\QrcodeShare;
use function Qiniu\waterImg;
use yii\db\Query;

class AuthorizerUtil
{
    /**
     * 根据AppId获取公众号信息
     * @param $appid
     * @return null|AuthorizationList
     */
    public static function getAuthOne($appid)
    {
        return AuthorizationList::findOne(['authorizer_appid' => $appid, 'status' => 1]);
    }



    /**
     * 获取所有公众号昵称
     * @return mixed
     */
    public static function getAuthListName()
    {
        $query = (new Query())
            ->select(['record_id', 'nick_name'])
            ->from('wc_authorization_list')
            ->where('verify_type_info in (0,3,4,5)')
            ->all();
        $data[0] = '总数据';
        foreach ($query as $item) {
            $data[$item['record_id']] = $item['nick_name'];
        }
        return $data;
    }

    /**
     * 根据记录Id 获取公众号信息
     * @param $record_id
     * @return null|AuthorizationList
     */
    public static function getAuthByOne($record_id)
    {
        return AuthorizationList::findOne(['record_id' => $record_id, 'status' => 1]);
    }

    /**
     * 获取事件消息
     * @param $record_id
     * @return null|AttentionEvent
     */
    public static function getEventMsg($record_id)
    {
        return AttentionEvent::findOne(['record_id' => $record_id]);
    }

    /**
     * 验证公众号认证状态
     */
    public static function isVerify($num)
    {
        $num = intval($num);
        $arr = [0, 3, 4, 5];
        if (!in_array($num, $arr)) {
            return false;
        }
        return true;
    }

    /**
     * 获取微信用户基本信息
     * @param $openid
     * @param $appid
     * @return null|Client
     */
    public static function getUserForOpenId($openid, $appid)
    {
        return Client::findOne(['open_id' => $openid, 'app_id' => $appid]);
    }

    /**
     * 获取微信用户基本信息同 getUserForOpenId
     * @param $openid
     * @return array
     */
    public static function getQueryUserForOpenId($app_id, $openid)
    {
        return (new Query())->select(['*'])->from('wc_client'.$app_id)->where(['open_id'=>$openid])->all();
    }

    /**
     * 更新用户交互时间
     * @param $appId
     * @param $open_id
     */
    public static function updateUserInfo($appId, $open_id)
    {
        \Yii::$app->db->createCommand()->update('wc_client'. $appId, ['update_time'=>time()], ['open_id'=>$open_id])->execute();
    }
    /**
     * 获取用户基础信息模型
     * @param $data
     * @param $app_id
     * @param bool $flag
     * @return Client|null
     */
    public static function genModel($model, $getData)
    {
        if (empty($model) || !isset($model)) {
            unset($model);
            $model = new Client();
            if (!isset($getData['openid'])) {
                \Yii::error('not OpenId: ' . var_export($getData, true));
            }
            $model->open_id = isset($getData['openid']) ? $getData['openid'] : '';
            $model->app_id = $getData['app_id'];
            $model->create_time = date('Y-m-d H:i:s');
            $model->invitation = 0;
            $model->is_vip = 0;
        }

        $model->subscribe = isset($getData['subscribe']) ? $getData['subscribe'] : '';
        $model->nick_name = isset($getData['nickname']) ? $getData['nickname'] : '';
        $model->sex = isset($getData['sex']) ? $getData['sex'] : '';
        $model->language = isset($getData['language']) ? $getData['language'] : '';
        $model->city = isset($getData['city']) ? $getData['city'] : '';
        $model->country = isset($getData['country']) ? $getData['country'] : '';
        $model->headimgurl = isset($getData['headimgurl']) ? $getData['headimgurl'] : '';
        $model->unionid = isset($getData['unionid']) ? $getData['unionid'] : '';
        $model->remark = isset($getData['remark']) ? $getData['remark'] : '';
        $model->groupid = isset($getData['groupid']) ? $getData['groupid'] : '';
        $model->province = isset($getData['province']) ? $getData['province'] : '';
        $model->subscribe_time = isset($getData['subscribe_time']) ? $getData['subscribe_time'] : '';
        $model->update_time = date('Y-m-d H:i:s');
        return $model;
    }

    /**
     * 更新用户信息
     * @param $appId
     * @param $User
     * @throws \yii\db\Exception
     */
    public static function SaveUser($appId, $User)
    {
        $db = \Yii::$app->db;
        $time = time();
        if(empty($User)) {
            $sql = 'insert ignore into wc_client'.$appId.' (open_id, nick_name, subscribe, sex, city, language, province, country,
        headimgurl, subscribe_time, unionid, groupid, app_id, is_vip, invitation, create_time, update_time, remark) VALUES (
        :op, :nn, :sub, :sex, :city, :lgu, :pro, :cou, :img, :subt, :union, :grou, :apd, :iv, :ivt, :ct, :ut, :rm);';
            $db->createCommand($sql,[':op'=> !empty($User['openid']) ? $User['openid']:'', ':nn'=> isset($User['nickname']) ? $User['nickname'] :'', ':sub'=> isset($User['subscribe']) ? $User['subscribe'] :'', ':sex'=> isset($User['sex']) ? $User['sex']:'', ':city'=> isset($User['city']) ? $User['city']:'', ':lgu'=> isset($User['language']) ? $User['language']:'', ':pro'=> isset($User['province']) ? $User['province']:'', ':cou'=> isset($User['country']) ? $User['country']:'', ':img'=> isset($User['headimgurl']) ? $User['headimgurl']:'', ':subt'=> $User['subscribe_time'], ':union'=> isset($User['unionid']) ? $User['unionid']:'', ':grou'=> isset($User['group_id']) ?$User['group_id']:'', ':apd'=> $User['app_id'], ':iv'=> 0, ':ivt'=> 0, ':ct'=> $time, ':ut'=> $time, ':rm' => isset($User['remark']) ? $User['remark']:''])->execute();
        } else {
            $db->createCommand()->update('wc_client'. $appId, ['nick_name' => isset($User['nickname']) ? $User['nickname']:'', 'subscribe' => isset($User['subscribe']) ? $User['subscribe']:'', 'sex' => isset($User['sex']) ? $User['sex']:'', 'city' => isset($User['city']) ? $User['city']:'', 'language' => isset($User['language']) ? $User['language']:'', 'province' => isset($User['province']) ? $User['province']:'', 'country' => isset($User['country']) ? $User['country']:'', 'headimgurl' => isset($User['headimgurl']) ? $User['headimgurl']:'', 'unionid' => isset($User['unionid']) ? $User['unionid']:'', 'groupid' => isset($User['groupid']) ? $User['groupid']:'', 'update_time' => $time, 'remark' => isset($User['remark']) ? $User['remark']:'', 'subscribe_time' => isset($User['subscribe_time']) ? $User['subscribe_time']:'',], ['app_id'=>$appId, 'open_id'=> $User['openid']])->execute();
        }
    }

    /**
     * 获取关键子列表
     * @param $app_id
     * @return array
     */
    public static function getKeyword($app_id)
    {
        $query = (new Query())
            ->select(['key_id', 'keyword', 'rule', 'global'])
            ->from('wc_keywords')
            ->where('app_id = :appid or key_id in (select key_id from wc_batch_keyword_list where app_id = :appid)', [':appid' => $app_id])->all();
        return $query;
    }


    /**
     * 保存消息数据模型
     * @param $model
     * @param $error
     * @return bool
     */
    public static function SaveAttentionEven($model, &$error)
    {
        if (!($model instanceof AttentionEvent)) {
            $error = '不是消息记录对象';
            return false;
        }

        if (!$model->save()) {
            $error = '保存消息排序号失败';
            \Yii::error($error . ' :' . var_export($model->getErrors(), true));
            return false;
        }
        return true;
    }

    /**
     * 保存菜单记录
     */
    public static function SaveWxMenu($model, &$error)
    {
        if (!($model instanceof AuthorizationMenu)) {
            $error = '不是菜单记录对象';
            return false;
        }
        if (!$model->save()) {
            $error = '保存菜单记录失败';
            \Yii::error($error . ' :' . var_export($model->getErrors(), true));
            return false;
        }
        return true;
    }

    /**
     * 根据APPid 获取菜单列表
     */
    public static function getMenuList($app_id)
    {
        $query = (new Query())
            ->select(['menu_id', 'app_id', 'name', 'ifnull(type,\'\') as type', 'ifnull(key_type,\'\') as key_type', 'url', 'is_list'])
            ->from('wc_authorization_menu')
            ->where(['app_id' => $app_id, 'parent_id' => 0])->all();
        if (empty($query)) return false;

        return $query;
    }

    /**
     * 根据全局配置Id 获取菜单列表
     */
    public static function getGlobalMenuList($global)
    {
        $query = (new Query())
            ->select(['menu_id', 'app_id', 'name', 'ifnull(type,\'\') as type', 'ifnull(key_type,\'\') as key_type', 'url', 'is_list'])
            ->from('wc_authorization_menu')
            ->where(['global' => $global, 'parent_id' => 0])->all();
        if (empty($query)) return false;
        return $query;
    }


    /**
     * 根据一级菜单ID获取子菜单列表
     * @param $menu_id
     * @return array
     */
    public static function getMenuSonList($menu_id)
    {
        $sql = (new Query())
            ->select(['name', 'type', 'url', 'key_type'])
            ->from('wc_authorization_menu')
            ->where(['parent_id' => $menu_id])->all();
        return $sql;
    }

    /**
     * 获取菜单记录数
     */
    public static function getMenuCount($app_id)
    {
        return AuthorizationMenu::find()->select(['count(1) as num'])->where(['app_id' => $app_id, 'parent_id' => 0])->limit(1)->scalar();
    }

    /**
     * 获取子菜单记录数
     */
    public static function getMenuSonCount($menu_id)
    {
        return AuthorizationMenu::find()->select(['count(1) as num'])->where(['parent_id' => $menu_id])->limit(1)->scalar();
    }

    /**
     * 获取全局菜单记录数
     */
    public static function getGlobalMenuCount($global)
    {
        return AuthorizationMenu::find()->select(['count(1) as num'])->where(['global' => $global, 'parent_id' => 0])->limit(1)->scalar();
    }

    /**
     * 保存粉丝基础信息
     * @param $access_token
     * @param $openid
     * @param $appid
     * @return bool
     */
    public static function SaveUserInfo($access_token, $openid, $appid, $UserInfo)
    {
        $getData = WeChatUserUtil::getUserInfo($access_token, $openid); //TODO: 请求获取用户信息
        if (!$getData) return false;
        $getData['appid'] = $appid;
        $model = AuthorizerUtil::genModel($UserInfo, $getData);
        if (!$model->save()) {
            \Yii::error('保存微信用户信息失败：' . var_export($model->getErrors(), true));
            return false;
        }
        return true;
    }

    /**
     * 获取用户是否被邀请关注
     * @param $attention_id
     * @return bool|string
     */
    public static function isAttention($attention_id)
    {
        $num = QrcodeShare::find()->select(['count(1) as num'])->limit(1)->where(['other_user_id' => $attention_id])->scalar();
        if ($num > 0) {
            return false;
        }
        return true;
    }

    /**
     * @param $appid
     * @throws \yii\db\Exception
     */
    public static function CreateClient($appid)
    {
        $sql = 'CREATE TABLE IF NOT EXISTS `wc_client'. $appid .'` (
                `client_id` int(11) NOT NULL AUTO_INCREMENT,
                `open_id` varchar(100) DEFAULT NULL,
                `nick_name` varchar(20) DEFAULT NULL,
                `subscribe` int (11) DEFAULT NULL,
                `sex` int(11) DEFAULT NULL,
                `language` varchar(20) DEFAULT NULL,
                `province` varchar(50) DEFAULT NULL,
                `country` varchar(50) DEFAULT NULL,
                `headimgurl` varchar(200) DEFAULT NULL,
                `subscribe_time` int(11) DEFAULT NULL,
                `unionid` varchar(100) DEFAULT NULL,
                `groupid` varchar(20) DEFAULT NULL,
                `app_id` int(11) DEFAULT NULL,
                `is_vip` int(11) DEFAULT NULL,
                `invitation` int(11) DEFAULT NULL,
                `create_time` int(11) DEFAULT NULL,
                `update_time` int(11)  DEFAULT NULL,
                `remark` varchar(100) DEFAULT NULL,
                `remark1` varchar(100) DEFAULT NULL,
                `remark2` varchar(100) DEFAULT NULL,
                PRIMARY KEY (`client_id`),
                UNIQUE KEY `openid_or_appid` (`open_id`, `app_id`) USING BTREE,
                KEY `is_vip` (`is_vip`) USING BTREE,
                KEY `app_id` (`app_id`) USING BTREE,
                KEY `nick_name` (`nick_name`) USING BTREE,
                KEY `create_time` (`create_time`) USING BTREE,
                KEY `update_time` (`update_time`) USING BTREE,
                KEY `subscribe` (`subscribe`) USING BTREE
                ) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4;';
        \Yii::$app->db->createCommand($sql)->execute();
    }

    /**
     * 是否告警
     * @param $rst
     * @param $app_id
     * @return bool
     */
    public static function isAlarms($rst, $app_id, $text = '', $toUser = null)
    {
        if (in_array($rst['errcode'], \Yii::$app->params['WxError'])) {
            //TODO: 判断公众号是否开启告警 这里每次都要重新获取数据
            $auth = AuthorizerUtil::getAuthByOne($app_id);
            if ($auth->alarm_status == 1) {
                $alarm = Alarm::findOne(['app_id' => $auth->record_id, 'create_time' => date('Y-m-d')]);
                if (!empty($alarm)) {
                    if ($alarm['alarm_num'] >= 3) return false;
                    $time = intval((time() - strtotime($alarm['alarm_time'])) / 60);
                    if ($time < 30) return false;
                    $alarm['alarm_num'] += 1;
                    $alarm['alarm_time'] = date('Y-m-d H:i:s');
                    $alarm->save();
                } else {
                    $model = new Alarm();
                    $model->app_id = $auth->record_id;
                    $model->alarm_num = 1;
                    $model->alarm_time = date('Y-m-d H:i:s');
                    $model->create_time = date('Y-m-d');
                    $model->save();
                }
                $remark = $text."接口告警 :\n" . "消息发送失败 : \nCode :" . $rst['errcode'] . ' errmsg :' . $rst['errmsg'];
                if(empty($toUser)) {
                    $toUser = \Yii::$app->params['toUser'];
                }
                if (!WeChatUserUtil::WeChatAlarmNotice($remark, $auth->nick_name, $toUser)) {
                    return false;
                }
            }
            return false;
        }
        return true;
    }
}