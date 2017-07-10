<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/6/28
 * Time: 上午9:05
 */

namespace backend\controllers;


use backend\business\WeChatUtil;
use backend\components\ExitUtil;
use backend\components\ReceiveType;
use backend\components\WeChatComponent;
use common\components\UsualFunForNetWorkHelper;
use yii\web\Controller;

class WechatController extends Controller
{
    public $enableCsrfValidation = false;

    public $error = '公众号授权异常：';

    public function actionTest(){
        $postData['query_auth_code'] =  111;
        $postData['openid'] = 'dsadsadsadas';
        $url = 'http://wxmp.gatao.cn/wechat/index';
        print_r($postData);
        echo "<br />";
        print_r(json_encode($postData));
        UsualFunForNetWorkHelper::HttpsPost($url,$postData);
        echo "ok";
    }
    /**
     * 全网发布测试 发送客服信息
     */
    public function actionIndex()
    {
        $post = \Yii::$app->request->post();
        $query_auth_code = $post['query_auth_code'];
        $openid = $post['openid'];
        //\Yii::error('POST::'.var_export($post,true));
        $WeChat = new WeChatUtil();
        $WeChat->getQueryAuth($query_auth_code,$rst,$error);
        //\Yii::error('AUTH_INFO::'.var_export($rst['authorization_info'],true));
        $AuthInfo = $rst['authorization_info'];
        $content = $query_auth_code.'_from_api';
        $url = sprintf('https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=%s',
            $AuthInfo['authorizer_access_token']);
        $data = [
            'touser'=>$openid,
            'msgtype'=>'text',
            'text'=>['content'=>$content]
        ];
        //\Yii::error('curlData::'.var_export($data,true));
        $json = json_encode($data);
        $rst = UsualFunForNetWorkHelper::HttpsPost($url,$json);
        \Yii::error('全网回调：'.var_export($rst,true));
    }

    /**
     *  接收微信事件回调
     */
    public function actionCallback()
    {
        $WeChat = new WeChatComponent();
        $Receive = new ReceiveType();
        $data = $WeChat->decryptMsg;
        $data['appid'] = $WeChat->AppId;
        \Yii::error('data:'.var_export($data,true));
        switch ($WeChat->MsgType)
        {
            case 'text':
                $resultXml = $Receive->Text($data);
                break;
            case 'image':
                $resultXml = $Receive->Image($data);
                break;
            case 'location':
                $resultXml = $Receive->Location($data);
                break;
            case 'voice':
                $resultXml = $Receive->Voice($data);
                break;
            case 'video':
                $resultXml = $Receive->Video($data);
                break;
            case 'link':
                $resultXml = $Receive->Link($data);
                break;
            case 'event':
                $resultXml = $Receive->Event($data);
                break;
            default:
                $resultXml = null;
                break;
        }

        if($resultXml == null) return null;
        if(!$WeChat->encryptMsg($resultXml,$encryptMsg)){
            $errMsg = $WeChat->getErrorMsg($WeChat->errorCode);
            \Yii::error('errMsg : '. $errMsg);
            return null;
        }
        \Yii::error('encrypt:'.$resultXml);
        return $encryptMsg;
    }

    /**
     * 微信公众号授权成功回调接口
     * @return \yii\web\Response
     */
    public function actionCallbackurl()
    {
        $data = $_REQUEST;
        if(empty($data['auth_code'])){
            \Yii::error('auth_code is empty :' . var_export($data,true));
            ExitUtil::ExitWithMessage('获取auth_code失败，auth_code为空');
        }
        $WeChat = new WeChatUtil();
        //TODO: 获取授权公众号的授权数据
        if(!$WeChat->getQueryAuth($data['auth_code'],$res,$error)){
            ExitUtil::ExitWithMessage($error);
        }
        $AuthInfo = $res['authorization_info'];
        empty($AuthInfo['authorizer_appid'])?ExitUtil::ExitWithMessage($this->error.'无法获取授权AppId'):'';
        empty($AuthInfo['authorizer_access_token'])?ExitUtil::ExitWithMessage($this->error.'无法获取授权凭证access_token'):'';
        empty($AuthInfo['authorizer_refresh_token'])?ExitUtil::ExitWithMessage($this->error.'无法获取授权刷新凭证refresh_token'):'';

        //TODO: 获取授权人帐号基本信息和公众号的基本信息
        if(!$WeChat->getAuthorizeInfo($AuthInfo['authorizer_appid'],$outInfo,$error)){
            ExitUtil::ExitWithMessage($error);
        }
        $authorizer_info = $outInfo['authorizer_info'];
        //TODO: 保存授权数据
        if(!$WeChat->SaveAuthInfo($AuthInfo,$authorizer_info,$error)){
            ExitUtil::ExitWithMessage($error);
        }
        return $this->redirect(['publiclist/index']);
    }

    /**
     * 截取微信回调的动态路由
     * @param $rules //请求Url地址 去掉host test：wechat/wx1283196321321/callback
     * @return string  //AppId 微信公众号的原始ID
     */
    private function getRulesAppid($rules)
    {
        $strstr = strstr($rules,"/");
        $strrpos = strtok($strstr,"/");
        return $strrpos;
    }
}
