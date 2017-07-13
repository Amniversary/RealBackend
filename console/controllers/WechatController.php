<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/6/27
 * Time: 下午4:30
 */

namespace console\controllers;


use backend\business\WeChatUtil;
use common\models\AuthorizationList;
use common\models\User;
use common\models\UserMenu;
use yii\console\Controller;
use yii\db\Query;

class WechatController extends Controller
{
    /**
     * 定时获取微信授权Token
     */
    public function actionGettoken()
    {
        $wechat = new WeChatUtil();
        if(!$wechat->getToken($error)){
            echo "$error \n";
            exit;
        }
        $time = date('Y-m-d H:i:s');

        echo "get Token success time:$time \n";
    }

    /**
     * 定时刷新用户授权公众号access_token  并覆盖缓存
     */
    public function actionRefreshauthtoken()
    {
        $wechat = new WeChatUtil();
        $wechat->refreshAuthToken();
        $query = (new Query())->select(['backend_user_id'])->from('wc_user')->all();
        foreach ($query as $item) {
            $get = \Yii::$app->cache->get('app_backend_'.$item['backend_user_id']);
            if($get != false){
                $decode = json_decode($get,true);
                if(!empty($decode)){
                    $AuthInfo = AuthorizationList::findOne(['record_id'=>$decode['record_id']]);
                    $data = $AuthInfo->attributes;
                    $data['backend_user_id'] = $decode['backend_user_id'];
                    \Yii::$app->cache->set('app_backend_'.$decode['backend_user_id'],json_encode($data));
                }
            }
        }
    }
}