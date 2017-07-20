<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/6/27
 * Time: 下午4:30
 */

namespace console\controllers;


use backend\business\AuthorizerUtil;
use backend\business\SaveByTransUtil;
use backend\business\SaveRecordByTransactions\SaveByTransaction\TestTrans;
use backend\business\WeChatUserUtil;
use backend\business\WeChatUtil;
use common\models\AuthorizationList;
use common\models\FansStatistics;
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

    /**
     * 获取前一天微信粉丝统计
     */
    public function actionFansnum()
    {
        $query = (new Query())
            ->select(['record_id','authorizer_access_token','verify_type_info','nick_name'])->from('wc_authorization_list')->all();
        if(empty($query)){
            echo "没有找到公众号信息 \n";
            exit;
        }
        $num = count($query);
        $i = 0;
        $time = date('Y-m-d H:i:s');
        foreach ($query as $item){
            if(!AuthorizerUtil::isVerify($item['verify_type_info'])){
                echo "公众号未认证 ID:".$item['nick_name'] ."\n";
                continue;
            }
            if(!WeChatUserUtil::getWxFansAccumulate($item['authorizer_access_token'],$rst,$error)){
                echo "$error \n";
                continue;
            }
            $fans = $this->getFansRecord($item['record_id']);
            if(empty($fans)){
                $fans = new FansStatistics();
                $fans->app_id = $item['record_id'];
                $fans->new_user = 0;
                $fans->cancel_user = 0;
                $fans->net_user = 0;

            }
            $fans->total_user = intval($rst['list'][0]['cumulate_user']);
            $fans->statistics_date = date('Y-m-d');
            $fans->remark1 = $time;
            $fans->save();

            $i ++;
        }
        echo "一共 $num 条记录，更新成功 $i 条记录   时间: $time\n";
    }

    public function actionRefuse(){
        $query = (new Query())
            ->select(['record_id','authorizer_access_token','verify_type_info'])->from('wc_authorization_list')->all();
        if(empty($query)){
            echo "没有找到公众号信息 \n";
            exit;
        }
        $num = count($query);
        $i = 0;
        $time = date('Y-m-d H:i:s');
        foreach ($query as $item){
            $transActions[] = new TestTrans($item);
            $i ++;
        }
        if(!SaveByTransUtil::RewardSaveByTransaction($transActions,$error,$out)){
            print_r($error);
        }

        echo "一共 $num 条记录，更新成功 $i 条记录   时间: $time\n";
    }

    /**
     * @return null|FansStatistics
     */
    private function getFansRecord($record_id){
        return FansStatistics::findOne(['app_id'=>$record_id,'statistics_date'=>date('Y-m-d')]);
    }
}