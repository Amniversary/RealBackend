<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2015/12/25
 * Time: 10:52
 */

namespace frontend\business;


use common\components\StatusUtil;
use common\models\Message;
use yii\base\Exception;

class MessageUtil
{

    /**
     * 获取消息名称
     * @param $type
     * @return mixed
     */
    public static function GetMessageTypeName($type)
    {
        $configFile = __DIR__.'/../../common/config/MessageTypeNameConfig.php';
        $typeMsgAry =require($configFile);
        return $typeMsgAry[strval($type)];
    }

    /*
     * 是否有未读消息
     */
    public static function HasUnreadNote($user_id)
    {
        $note = Message::find()->where(['user_id'=>$user_id,'is_read'=>0])->select(['msg_id'])->limit(1)->scalar();
        return $note !== false;
    }

    /**
     * 设置消息已读，批量设置
     * @param $user_id
     * @param $error
     */
    public static function SetMessageRead($user_id, &$error)
    {
            $msgList = self::GetUnReadMsgByUserId($user_id);
        $sql = '';
        $sqlTemplate='update my_message set is_read=1 where msg_id=%s and is_read=0;';
        foreach($msgList as $msg)
        {
            $sql .= sprintf($sqlTemplate, $msg->msg_id);
        }
        if(!empty($sql))
        {
            try
            {
                \Yii::$app->db->createCommand($sql)->execute();
            }
            catch(Exception $e)
            {
                $error = '设置已读执行sql错误';
                \Yii::getLogger()->log($error . ' error_detail:'.$e->getMessage(),Logger::LEVEL_ERROR);
                return false;
            }
        }
        return true;
    }

    /**
     * 根据id获取未读消息
     * @param $user_id
     */
    public static function GetUnReadMsgByUserId($user_id)
    {
        $msgList = Message::findAll([
            'user_id'=>$user_id,
            'is_read'=> '0',
        ]);
        return $msgList;
    }
    /**
     * 获取消息列表
     * @param $flag 标记
     * @param $start_id  记录id
     * @param $msg_type 消息类型，数字，需要解析
     * @param $user_id 用户id
     */
        public static function GetMessageList($flag,$start_id,$msg_type,$user_id)
        {
            if(empty($msg_type))
            {
                $condition = ['and',['user_id'=>$user_id]];
            }
            else
            {
                $typeList = StatusUtil::GetStatusList(intval($msg_type),10);
                $condition = ['and',['user_id'=>$user_id],['in','msg_type',$typeList]];
            }

            switch($flag)
            {
                case 'up':
                    $condition[]=['>','msg_id',$start_id];
                    break;
                case 'down':
                    $condition[]=['<','msg_id',$start_id];
                    break;
                default:
                    break;
            }
            $rsList = Message::find()->limit(10)->orderBy('msg_id desc')->where($condition)->all();
            return $rsList;
        }

    /**
     * 格式化消息
     * @param $msgList
     * @return array
     */
    public static function GetFormateMsg($msgList)
    {
        $out = [];
        if(empty($msgList))
        {
            return $out;
        }
        foreach($msgList as $msg)
        {
            $ary = [
                'msg_id'=>$msg->msg_id,
                'content'=>$msg->content,
                'create_time'=>$msg->create_time,
                'is_read' => $msg->is_read,
                'msg_type'=>$msg->remark1,//remark1是消息显示的类型
                'pic' => self::GetMsgPicByType($msg->msg_type),
            ];
            $out[] = $ary;
        }
        return $out;
    }

    /**
     * 获取消息类型
     * @param $msg_type
     */
    public static function GetMsgTypeName($msg_type)
    {
        /*
发现 1、打赏 2、系统活动4、收到红包8、借贷16、评论回复32 、签到红包64
         */
        $rst = '';
        switch(strval($msg_type))
        {
            case '1':
                $rst = '发现';
                break;
            case '1':
                $rst = '打赏';
                break;
            case '4':
                $rst = '系统活动';
                break;
            case '8':
                $rst = '收到红包';
                break;
            case '16':
                $rst = '美愿基金借款';
                break;
            case '64':
                $rst = '愿望评论';
                break;
            default:
                $rst = '';
                break;
        }
        return $rst;
    }

    /**
     * 获取消息图片
     * @param $msg_type
     */
    public static  function GetMsgPicByType($msg_type)
    {
        $rst = '';
        switch(strval($msg_type))
        {
            case '1':
                $rst = 'http://oss.aliyuncs.com/meiyuan/wish_type/msgdefault.png';
                break;
            case '1':
                $rst = 'http://oss.aliyuncs.com/meiyuan/wish_type/msgdefault.png';
                break;
            case '4':
                $rst = 'http://oss.aliyuncs.com/meiyuan/wish_type/msgdefault.png';
                break;
            case '8':
                $rst = 'http://oss.aliyuncs.com/meiyuan/wish_type/msgdefault.png';
                break;
            case '16':
                $rst = 'http://oss.aliyuncs.com/meiyuan/wish_type/msgdefault.png';
                break;
            case '64':
                $rst = 'http://oss.aliyuncs.com/meiyuan/wish_type/msgdefault.png';
                break;
            default:
                $rst = 'http://oss.aliyuncs.com/meiyuan/wish_type/msgdefault.png';
                break;
        }
        return $rst;
    }

    /**
     * 获取消息新模型
     * @param $msg_type
     * @param $content
     * @param $user_id
     * @return Message
     */
    public static function GetMsgNewModel($msg_type,$content,$user_id)
    {
        $msg = new Message();
        $msg->user_id = intval($user_id);
        $msg->content = $content;//sprintf('%s打赏了愿望【%s】',$user->nick_name,$wishRecord->wish_name);
        //$msg->cur_money = $pay_money;
        $msg->create_time = date('Y-m-d H:i:s',time());
        $msg->status = '1';
        $msg->msg_type = $msg_type;
        $msg->is_read = '0';
        $msg->remark1 = self::GetMessageTypeName($msg_type);
        return $msg;
    }

} 