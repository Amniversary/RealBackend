<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/6/26
 * Time: 下午4:39
 */

namespace backend\controllers;


use backend\components\WeChatComponent;
use common\components\wxpay\lib\WxPayConfig;
use common\models\Authorization;
use common\models\AuthorizationList;
use yii\web\Controller;

class AuthorizedController extends Controller
{
    public $enableCsrfValidation = false;

    /**
     * 测试公众号授权接口
     */
    public function actionTest(){

        $AppInfo = Authorization::findOne(['app_id'=>WxPayConfig::APPID]);
        $url = sprintf('https://mp.weixin.qq.com/cgi-bin/componentloginpage?component_appid=%s&pre_auth_code=%s&redirect_uri=%s',
            $AppInfo->app_id,
            $AppInfo->pre_auth_code,
            'http://wxmp.gatao.cn/wechat/callbackurl' //wechat/callbackurl
            );

        echo '<a href="'.$url .'">点击授权</a>';

    }


    /**
     * 解密回调URl中XML的加密信息
     * @return string
     */
    public function actionNotice()
    {
        $WeChat = new WeChatComponent();
        $data = $WeChat->decryptMsg;
        $record = Authorization::findOne(['app_id'=>$WeChat->webAppId]);
        $infoType = isset($data['InfoType']) ? $data['InfoType']: '';
        if(!empty($infoType) && $infoType == 'unauthorized'){
            $authorzer_appid = $data['AuthorizerAppid'];
            $record = AuthorizationList::findOne(['authorizer_appid'=>$authorzer_appid]);
            if(!$record){
                \Yii::error('not record:'.var_export($record,true));
                return 'success';
            }
            $record->status = 0;
            $record->update_time = date('Y-m-d H:i:s');
            if(!$record->save()){
                \Yii::error('保存取消授权信息失败 ：'.var_export($record->getErrors(),true));
            }
            return 'success';
        }
        //TODO: 保存数据到数据库
        if(isset($data['ComponentVerifyTicket'])){
            if($record){
                $record->create_time = $data['CreateTime'];
                $record->verify_ticket = $data['ComponentVerifyTicket'];
            }else{
                $record = new Authorization();
                $record->app_id = $data['AppId'];
                $record->create_time = $data['CreateTime'];
                $record->verify_ticket = $data['ComponentVerifyTicket'];
            }
            if(!$record->save()){
                \Yii::error('保存授权码Ticket失败 ：'.var_export($record->getErrors(),true));
            }
        }
        return 'success';
    }
}