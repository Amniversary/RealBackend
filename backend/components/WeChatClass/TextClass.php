<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/7/1
 * Time: 下午2:42
 */

namespace backend\components\WeChatClass;


use backend\business\AuthorizerUtil;
use backend\business\JobUtil;
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
        if(!empty($query)) return null;

        foreach ($query as $item)
        {
            $flag = $item['rule'] == 1 ?
                $text == $item['keyword'] ? true:false :
                strpos($item['key_id'],$text) !== false ? true:false;
            if($flag)
            {
                //TODO:处理消息回复逻辑
                $msgData = AuthorizerUtil::getAttentionMsg($AppInfo->record_id,1,$item['key_id']);
                if(!empty($msgData))
                {
                    foreach ($msgData as $info)
                    {
                        if(!isset($info['msg_type'])) $info['msg_type'] = 1;
                        $paramData = [
                            'key_word'=>'key_word',
                            'open_id'=>$openid,
                            'authorizer_access_token'=>$AppInfo->authorizer_access_token,
                            'item'=>$info,
                        ];
                        if(!JobUtil::AddCustomJob('wechatBeanstalk','wechat',$paramData,$error)){
                            \Yii::error('keyword msg job is error :'.$error);
                        }
                    }
                }
            }
        }

        return null;
    }
}