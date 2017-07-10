<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/7/1
 * Time: 下午2:42
 */

namespace backend\components\WeChatClass;


use backend\business\AuthorizerUtil;
use backend\business\WeChatUserUtil;
use backend\components\ReceiveType;
use common\components\UsualFunForNetWorkHelper;
use common\models\Keywords;
use yii\db\Query;

class TextClass
{
    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * 处理文本事件
     */
    public function Text()
    {
        $openid = $this->data['FromUserName'];
        $appid = $this->data['appid'];
        $text = $this->data['Content'];
        $AppInfo = AuthorizerUtil::getAuthOne($appid);
        $query = AuthorizerUtil::getAppMsg($AppInfo->record_id);
        $flag = null;
        if(!empty($query))
        {
            foreach ($query as $item){
                $flag = false;
                if($item['rule'] == 1){
                    if($text == $item['keyword']) $flag = true;
                }else{
                    if(strpos($item['keyword'],$text) !== false) $flag = true;
                }
                if($flag)
                {
                    //TODO:处理消息回复逻辑
                    $msgData = AuthorizerUtil::getAttentionMsg($AppInfo->record_id,1,$item['key_id']);
                    if(!empty($msgData))
                    {
                        foreach ($msgData as $info)
                        {
                            if(!isset($info['msg_type']))
                                $info['msg_type'] = 1;
                            WeChatUserUtil::sendCustomerMsg($AppInfo->authorizer_access_token,$openid,$info);
                        }
                    }
                }
            }
        }

        return null;
    }
}