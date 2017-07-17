<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/6/30
 * Time: 下午2:52
 */

namespace backend\components;


use backend\components\WeChatClass\EventClass;
use backend\components\WeChatClass\TextClass;
use common\components\UsualFunForNetWorkHelper;

class ReceiveType
{
    /**
     * 处理文本事件
     */
    public function Text($arr,$flag = 0)
    {
        $contentStr = null;
        if($arr['Content'] == 'TESTCOMPONENT_MSG_TYPE_TEXT'){ //TODO: 全网测试消息
            $contentStr = $arr['Content'].'_callback';
        }elseif (strpos($arr['Content'],'QUERY_AUTH_CODE:') !== false){
            $postData['query_auth_code'] =  str_replace('QUERY_AUTH_CODE:', '', $arr['Content']);
            $postData['openid'] = $arr['FromUserName'];
            $url = 'http://wxmp.gatao.cn/wechat/index';
            UsualFunForNetWorkHelper::HttpsPost($url,$postData);
            $contentStr = null;
        }else{
            $Text = new TextClass($arr);
            $contentStr = $Text->Text();
            if($contentStr['msg_type'] == 1){
                return $this->transmitNews($arr,$contentStr);
            }elseif($contentStr['msg_type'] == 2){
                return $this->transmitImg($arr,$contentStr);
            }
        }
        $resultStr = $this->transmitText($arr, $contentStr, $flag);
        return $resultStr;
    }

    /**
     * 处理图片事件
     */
    public function Image($arr,$flag = 0)
    {
        return null;
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
        return null;
    }

    /**
     * 处理视频消息事件
     */
    public function Video($arr,$flag = 0)
    {
        return null;
    }

    /**
     * 处理连接消息事件
     */
    public function Link($arr,$flag = 0)
    {
        return null;
    }

    /**
     * 处理事件消息
     */
    public function Event($arr,$flag = 0)
    {
        $Event = new EventClass($arr);
        switch ($arr['Event'])
        {
            case 'subscribe':
                $contentStr = $Event->subscribe();
                break;
            case 'unsubscribe':
                $contentStr = $Event->unSubscribe();
                break;
            case 'CLICK':
                $contentStr = null;
                break;
            default:
                $contentStr = $arr['Event'].'from_callback';
                break;
        }
        \Yii::error('subscribe:' .var_export($contentStr,true));
        if($contentStr['msg_type'] == 1){
            return $this->transmitNews($arr,$contentStr);
        }elseif($contentStr['msg_type'] == 2){
            return $this->transmitImg($arr,$contentStr);
        }
        $resultStr = $this->transmitText($arr, $contentStr);
        return $resultStr;
    }

    /**
     * 消息回复模版
     * @param $arr
     * @param $content
     * @param int $flag
     * @return string
     */
    public function transmitText($arr, $content, $flag = 0)
    {
        if($content == null){
            return null;
        }
        if(is_array($content)){
            $content = $content['content'];
        }
        if(strpos($content,'from_callback')){
            $arr['MsgType'] = 'text';
        }
        $textXml = "<xml>
                        <ToUserName><![CDATA[%s]]></ToUserName>
                        <FromUserName><![CDATA[%s]]></FromUserName>
                        <CreateTime>%s</CreateTime><MsgType><![CDATA[text]]></MsgType>
                        <Content><![CDATA[%s]]></Content>
                        <FunFlag>%s</FunFlag>
                    </xml>";
        $resultStr = sprintf($textXml, $arr['FromUserName'], $arr['ToUserName'], time(),$content,$flag);
        return $resultStr;
    }


    /**
     * 图文回复模版
     * @param $arr
     * @param $content
     * @return null|string
     */
    public function transmitNews($arr, $content){
        if($content ==null) return null;
        $count = count($content);
        $newsXml = "<xml>
                        <ToUserName><![CDATA[%s]]></ToUserName>
                        <FromUserName><![CDATA[%s]]></FromUserName>
                        <CreateTime>%s</CreateTime>
                        <MsgType><![CDATA[news]]></MsgType>
                        <ArticleCount>%s</ArticleCount>
                        <Articles>";
        foreach($content as $item){
            $newsXml .= "<item>
                            <Title><![CDATA[".$item['title']."]]></Title>
                            <Description><![CDATA[".$item['description']."]]></Description>
                            <PicUrl><![CDATA[".$item['url']."]]></PicUrl>
                            <Url><![CDATA[".$item['picurl']."]]></Url>
                        </item>";
        }
        $newsXml .= "</Articles></xml>";
        $resultStr = sprintf($newsXml,$arr['FromUserName'],$arr['ToUserName'],time(),$count);
        return $resultStr;
    }


    /**
     * 图片消息模版
     * @param $arr
     * @param $content
     * @return null|string
     */
    public function transmitImg($arr,$content){
        if($content == null) return null;
        $imgXml = "<xml>
                    <ToUserName><![CDATA[%s]]></ToUserName>
                    <FromUserName><![CDATA[%s]]></FromUserName>
                    <CreateTime>%s</CreateTime>
                    <MsgType><![CDATA[image]]></MsgType>
                    <Image>
                    <MediaId><![CDATA[%s]]></MediaId>
                    </Image>
                   </xml>";
        $resultStr = sprintf($imgXml,$arr['FromUserName'],$arr['ToUserName'],time(),$content['media_id']);
        return $resultStr;
    }
}