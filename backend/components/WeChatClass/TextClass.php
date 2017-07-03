<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/7/1
 * Time: 下午2:42
 */

namespace backend\components\WeChatClass;


use backend\components\ReceiveType;
use common\components\UsualFunForNetWorkHelper;

class TextClass
{
    /**
     * 处理文本事件
     */
    public function Text($arr,$flag = 0)
    {
        $contentStr = $arr['Content'];
        if($arr['Content'] == 'TESTCOMPONENT_MSG_TYPE_TEXT'){ //TODO: 全网测试消息
            $contentStr = $arr['Content'].'_callback';
        }elseif (strpos($arr['Content'],'QUERY_AUTH_CODE:') !== false){
            $postData['query_auth_code'] =  str_replace('QUERY_AUTH_CODE:', '', $arr['Content']);
            $postData['openid'] = $arr['FromUserName'];
            $url = 'http://wxmp.gatao.cn/wechat/index';
            UsualFunForNetWorkHelper::HttpsPost($url,$postData);
            $contentStr = null;
        }
        $resultStr = ReceiveType::transmitText($arr, $contentStr, $flag);
        return $resultStr;
    }
}