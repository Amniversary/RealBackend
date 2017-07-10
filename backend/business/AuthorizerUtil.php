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
use common\models\AuthorizationMenuSon;
use common\models\Client;
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
        return AuthorizationList::findOne(['authorizer_appid'=>$appid]);
    }

    /**
     * 获取微信用户基本信息
     * @param $openid
     * @param $appid
     * @return null|Client
     */
    public static function getUserForOpenId($openid,$appid)
    {
        return Client::findOne(['open_id'=>$openid,'app_id'=>$appid]);
    }

    /**
     * 获取消息列表
     * @param $id
     * @param $flag
     * @param $key
     * @return array|bool
     */
    public static function getAttentionMsg($id,$flag = 0,$key = null)
    {
        if(!empty($key)){
            $condition = sprintf('app_id=%s and flag=%s and key_id=%s',
                $id,$flag,$key);
        }else{
            $condition = sprintf('app_id=%s and flag=%s',
                $id,$flag);
        }

        $query = (new Query())
            ->select(['record_id','app_id','event_id','content','msg_type','title','description','url','picurl','media_id','update_time'])
            ->from('wc_attention_event')
            ->where($condition)->orderBy('order_no asc')->all();
        //没有消息
        if(empty($query)){
            return false;
        }

        //处理文本消息格式
        $data = [];
        $articles = [];
        $time = time();
        foreach ($query as $list){
            switch ($list['msg_type']){
                case 0:
                    $data[] = ['content'=>$list['content'],'msg_type'=>$list['msg_type']]; break;
                case 1:
                    $articles[$list['event_id']][] = [
                        'title' => $list['title'],
                        'description' => $list['description'],
                        'url' => $list['url'],
                        'picurl' => $list['picurl']
                    ]; break;
                case 2:
                    $outTime = intval(($time - $list['update_time'])/84600);
                    if($outTime >= 3){
                        $rst = (new WeChatUtil())->UploadWeChatImg($list['picurl']);
                        $model = AttentionEvent::findOne(['record_id'=>$list['record_id']]);
                        $model->media_id = $rst['media_id'];
                        $model->update_time = $rst['created_at'];
                        $model->save();
                        $data[] = ['msg_type'=>$list['msg_type'],'media_id'=>$rst['media_id']];
                    }else{
                        $data[] = ['msg_type'=>$list['msg_type'],'media_id'=>$list['media_id']];
                    }
                    break;
            }

        }

        //print_r($articles);
        if(!empty($articles)){
            foreach ($articles as $key){
                $data[] = $key;
            }
        }
        //print_r($data);
        return $data;
    }

    /**
     * 获取用户基础信息模型
     * @param $data
     * @param $app_id
     * @param bool $flag
     * @return Client|null
     */
    public static function genModel($data,$getData,$flag = false)
    {
        $model = $data;
        if(!$flag){
            $model = new Client();
            if(!isset($getData['open_id'])){
                \Yii::error('not OpenId: '.var_export($getData,true));
            }
            $model->open_id = isset($getData['open_id'])? $getData['open_id'] : '';
            $model->app_id = $getData['app_id'];
            $model->create_time = date('Y-m-d H:i:s');
        }


        $model->subscribe = isset($getData['subscribe'])? $getData['subscribe'] : '';
        $model->nick_name = isset($getData['nickname'])? $getData['nickname'] : '';
        $model->sex = isset($getData['sex'])? $getData['sex'] : '';
        $model->language = isset($getData['language'])? $getData['language'] : '';
        $model->city = isset($getData['city'])? $getData['city'] : '';
        $model->country = isset($getData['country'])? $getData['country'] : '';
        $model->headimgurl = isset($getData['headimgurl'])? $getData['headimgurl'] : '';
        $model->unionid = isset($getData['unionid'])? $getData['unionid'] : '';
        $model->remark = isset($getData['remark'])? $getData['remark'] : '';
        $model->groupid = isset($getData['groupid'])? $getData['groupid'] : '';
        $model->province = isset($getData['province'])? $getData['province'] : '';
        $model->subscribe_time = isset($getData['subscribe_time'])? $getData['subscribe_time'] : '';
        $model->update_time = date('Y-m-d H:i:s');
        return $model;
    }

    /**
     *
     * @param $app_id
     * @return array
     */
    public static function getAppMsg($app_id)
    {
        $query = (new Query())
            ->select(['key_id','keyword','rule'])
            ->from('wc_keywords')
            ->where(['app_id'=>$app_id])->all();
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
        if(!($model instanceof AttentionEvent)){
            $error = '不是消息记录对象';
            return false;
        }

        if(!$model->save()){
            $error = '保存消息排序号失败';
            \Yii::error($error . ' :' .var_export($model->getErrors(),true));
            return false;
        }
        return true;
    }

    /**
     * 保存菜单记录
     */
    public static function SaveWxMenu($model,&$error){
        if(!($model instanceof AuthorizationMenu)){
            $error = '不是菜单记录对象';
            return false;
        }
        if(!$model->save()){
            $error = '保存菜单记录失败';
            \Yii::error($error.' :'.var_export($model->getErrors(),true));
            return false;
        }
        return true;
    }

    /**
     * 根据APPid 获取菜单列表
     */
    public static function getMenuList($app_id){
        $query = (new Query())
            ->select(['menu_id','app_id','name','ifnull(type,\'\') as type','ifnull(key_type,\'\') as key_type','is_list'])
            ->from('wc_authorization_menu')
            ->where('app_id = :appid',[':appid'=>$app_id])->all();
        if(empty($query)) return false;

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
            ->select(['name','type','url','key_type'])
            ->from('wc_authorization_menu_son')
            ->where('menu_id = :md',[':md'=>$menu_id])->all();
        return $sql;
    }

    /**
     * 获取菜单记录数
     */
    public static function getMenuCount($app_id)
    {
        return AuthorizationMenu::find()->select(['count(1) as num'])->where(['app_id'=>$app_id])->limit(1)->scalar();
    }

    /**
     * 获取子菜单记录数
     */
    public static function getMenuSonCount($menu_id)
    {
        return AuthorizationMenuSon::find()->select(['count(1) as num'])->where(['menu_id'=>$menu_id])->limit(1)->scalar();
    }
}