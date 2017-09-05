<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/7/1
 * Time: 下午3:55
 */

namespace backend\business;


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
        foreach($query as $item) {
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


}