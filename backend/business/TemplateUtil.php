<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/7/24
 * Time: 下午12:17
 */

namespace backend\business;


use common\models\Template;

class TemplateUtil
{
    /**
     * 获取消息xml
     */
    public static function GetMsgTemplate($arr, $contentStr)
    {
        $msg_type = 0;
        if (is_array($contentStr) && isset($contentStr['msg_type'])) {
            $msg_type = $contentStr['msg_type'];
        }
        switch (intval($msg_type)) {
            case  1:
                $rst = self::transmitNews($arr, $contentStr);
                break;
            case  2:
                $rst = self::transmitImg($arr, $contentStr);
                break;
            case  3:
                $rst = self::transmitVideo($arr, $contentStr);
                break;
            default:
                $rst = self::transmitText($arr, $contentStr);
                break;
        }
        return $rst;
    }

    /**
     * 消息回复模版
     * @param $arr
     * @param $content
     * @param int $flag
     * @return string
     */
    public static function transmitText($arr, $content, $flag = 0)
    {
        if ($content == null) {
            return null;
        }
        if (is_array($content)) {
            $content = $content['content'];
        }
        if (strpos($content, 'from_callback')) {
            $arr['MsgType'] = 'text';
        }
        $textXml = "<xml>
                        <ToUserName><![CDATA[%s]]></ToUserName>
                        <FromUserName><![CDATA[%s]]></FromUserName>
                        <CreateTime>%s</CreateTime><MsgType><![CDATA[text]]></MsgType>
                        <Content><![CDATA[%s]]></Content>
                        <FunFlag>%s</FunFlag>
                    </xml>";
        $resultStr = sprintf($textXml, $arr['FromUserName'], $arr['ToUserName'], time(), str_replace("\r\n", PHP_EOL, $content), $flag);
        return $resultStr;
    }


    /**
     * 图文回复模版
     * @param $arr
     * @param $content
     * @return null|string
     */
    public static function transmitNews($arr, $content)
    {
        if ($content == null) return null;
        unset($content['msg_type']);
        $count = count($content);
        $newsXml = "<xml>
                        <ToUserName><![CDATA[".$arr['FromUserName']."]]></ToUserName>
                        <FromUserName><![CDATA[". $arr['ToUserName'] ."]]></FromUserName>
                        <CreateTime>". time() . "</CreateTime>
                        <MsgType><![CDATA[news]]></MsgType>
                        <ArticleCount>".$count."</ArticleCount>
                        <Articles>";
        foreach ($content as $item) {
            $newsXml .= "<item>
                            <Title><![CDATA[" . $item['title'] . "]]></Title>
                            <Description><![CDATA[" . $item['description'] . "]]></Description>
                            <PicUrl><![CDATA[" . $item['picurl'] . "]]></PicUrl>
                            <Url><![CDATA[" . $item['url'] . "]]></Url>
                        </item>";
        }
        $newsXml .= "</Articles></xml>";
        return $newsXml;
    }


    /**
     * 图片消息模版
     * @param $arr
     * @param $content
     * @return null|string
     */
    public static function transmitImg($arr, $content)
    {
        if ($content == null) return null;
        $imgXml = "<xml>
                    <ToUserName><![CDATA[%s]]></ToUserName>
                    <FromUserName><![CDATA[%s]]></FromUserName>
                    <CreateTime>%s</CreateTime>
                    <MsgType><![CDATA[image]]></MsgType>
                    <Image>
                    <MediaId><![CDATA[%s]]></MediaId>
                    </Image>
                   </xml>";
        $resultStr = sprintf($imgXml, $arr['FromUserName'], $arr['ToUserName'], time(), $content['media_id']);
        return $resultStr;
    }

    /**
     * 语音消息模版
     * @param $arr
     * @param $content
     * @return null|string
     */
    public static function transmitVideo($arr, $content)
    {
        if ($content == null) return null;
        $xml = "<xml>
                    <ToUserName><![CDATA[%s]]></ToUserName>
                    <FromUserName><![CDATA[%s]]></FromUserName>
                    <CreateTime>%s</CreateTime>
                    <MsgType><![CDATA[voice]]></MsgType>
                    <Voice>
                        <MediaId><![CDATA[%s]]></MediaId>
                    </Voice>
                </xml>";
        return $resultStr = sprintf($xml, $arr['FromUserName'], $arr['ToUserName'], time(), $content['media_id']);
    }

    /**
     * 根据模板id 获取模板
     * @param $id
     * @return null|Template
     */
    public static function GetTemplateById($id)
    {
        return Template::findOne(['id' => $id]);
    }
}