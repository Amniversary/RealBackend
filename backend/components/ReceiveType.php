<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/6/30
 * Time: 下午2:52
 */

namespace backend\components;


use backend\business\AuthorizerUtil;
use backend\business\JobUtil;
use backend\business\TemplateUtil;
use backend\components\WeChatClass\EventClass;
use backend\components\WeChatClass\ImageClass;
use backend\components\WeChatClass\TextClass;
use backend\components\WeChatClass\VideoClass;
use backend\components\WeChatClass\VoiceClass;
use common\components\UsualFunForNetWorkHelper;

class ReceiveType
{
    /**
     * 处理文本事件
     */
    public function Text($arr,$flag = 0)
    {
        $contentStr = null;
        if($arr['Content'] == 'TESTCOMPONENT_MSG_TYPE_TEXT'){ //TODO: 全网测试文本消息
            $contentStr = $arr['Content'].'_callback';
        }elseif (strpos($arr['Content'],'QUERY_AUTH_CODE:') !== false){  //TODO: 全网发布测试客服消息回复
            $postData['query_auth_code'] =  str_replace('QUERY_AUTH_CODE:', '', $arr['Content']);
            $postData['openid'] = $arr['FromUserName'];
            $url = 'http://wxmp.gatao.cn/wechat/index';
            UsualFunForNetWorkHelper::HttpsPost($url,$postData);
            $contentStr = null;
        }else{
            $Text = new TextClass($arr);
            $contentStr = $Text->Text();
        }
        $resultStr = TemplateUtil::GetMsgTemplate($arr, $contentStr);
        return $resultStr;
    }


    /**
     * 处理事件消息
     */
    public function Event($arr,$flag = 0)
    {
        $Event = new EventClass($arr);
        $contentStr = null;
        switch ($arr['Event'])
        {
            case 'subscribe':
                $contentStr = $Event->subscribe();
                break;
            case 'unsubscribe':
                $contentStr = $Event->unSubscribe();
                break;
            case 'CLICK':
                $auth = AuthorizerUtil::getAuthOne($arr['appid']);
                $contentStr = $Event->Click();
                if($arr['EventKey'] == 'get_qrcode') {
                    if(in_array($auth->record_id,\Yii::$app->params['WxAuthParams'])) {
                        $params = ['key_word' => 'get_qrcode', 'data' => $arr];
                        if(!JobUtil::AddCustomJob('imgBeanstalk','get_qrcode',$params,$error)) {
                            \Yii::error($error);
                        }
                        $contentStr = '海报生成中 ...';
                    }
                }
                break;
            case 'TEMPLATESENDJOBFINISH':
                if($arr['Status'] != 'success') {
                    \Yii::error('模板消息失败回调: '.var_export($arr,true));
                }
                $contentStr = null;
                break;
            default:
                $contentStr = null;//$arr['Event'].'from_callback';
                break;
        }
        $resultStr = TemplateUtil::GetMsgTemplate($arr, $contentStr);

        return $resultStr;
    }

    /**
     * 处理图片事件
     */
    public function Image($arr,$flag = 0)
    {
        $img = new ImageClass($arr);
        $content = $img->image();
        $resultStr = TemplateUtil::GetMsgTemplate($arr, $content);
        return $resultStr;
    }

    /**
     * 处理地理位置事件
     */
    public function Location($arr,$flag = 0)
    {
        return null;
    }

    /**
     * 处理语音消息事件
     */
    public function Voice($arr,$flag = 0)
    {
        $voice = new VoiceClass($arr);
        $content = $voice->Voice();
        $resultStr = TemplateUtil::GetMsgTemplate($arr, $content);
        return $resultStr;
    }

    /**
     * 处理视频消息事件
     */
    public function Video($arr,$flag = 0)
    {
        $video = new VideoClass($arr);
        $content = $video->Video();
        $resultStr = TemplateUtil::GetMsgTemplate($arr, $content);
        return $resultStr;
    }

    /**
     * 处理连接消息事件
     */
    public function Link($arr,$flag = 0)
    {
        return null;
    }
}