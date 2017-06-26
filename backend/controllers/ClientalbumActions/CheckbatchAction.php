<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/9
 * Time: 10:46
 */

namespace backend\controllers\ClientalbumActions;


use common\components\tenxunlivingsdk\TimRestApi;
use frontend\business\DynamicUtil;
use frontend\business\TicketToCashUtil;
use yii\base\Action;
use yii\log\Logger;

class CheckbatchAction extends Action
{
    public function run()
    {
        $ids = \Yii::$app->request->post('data');
        $ids = explode('-',$ids);

//        \Yii::getLogger()->log('id号：——'.var_export($ids,true),Logger::LEVEL_ERROR);
//        exit;

        if(empty($ids)){
            $rst['msg'] = '相册id不能空';
            echo json_encode($rst);
            exit;
        }

        $ids_len = count($ids);

        //拿到第一个用户的id
        $dynamicInfo = DynamicUtil::GetDynamicById($ids['0']);
        $user_id = $dynamicInfo['user_id'];

        //推送系统消息 跟用户说明
        $content = "温馨提醒，您有一张照片或多张照片不符合规范，已被管理员删除";

        foreach($ids as $id)
        {
            $dynamicInfos = DynamicUtil::GetDynamicById($id);
            $update = DynamicUtil::UpdateDynamicStatus($id);

            if(!$update)
            {
                $rst['msg']='删除相册失败';
                echo json_encode($rst);
                exit;
            }

            //判断删除的图片的用户和第一个用户id是否相同 如果不相同 则发送推送消息
            if($dynamicInfos['user_id'] != $dynamicInfo['user_id'])
            {
                //推送系统消息
                if(!TimRestApi::openim_send_Text_msg($dynamicInfos['user_id'],$content,$error))
                {
                    \Yii::getLogger()->log('发送腾讯云通知消息异常: '.$error,Logger::LEVEL_ERROR);
                    $error = '删除相册消息推送失败';
                    return false;
                }
            }

            //记录上一次删除图片的用户的id
            $dynamicInfo['user_id'] = $dynamicInfos['user_id'];

            $ids_len--;
        }

        //推送系统消息
        if(!TimRestApi::openim_send_Text_msg($user_id,$content,$error))
        {
            \Yii::getLogger()->log('发送腾讯云通知消息异常: '.$error,Logger::LEVEL_ERROR);
            $error = '删除相册消息推送失败';
            return false;
        }

        if(($ids_len>0) && ($ids_len === count($ids))){
            $rst['msg']='审核失败';
            echo json_encode($rst);
            exit;
        }

        $rst['code']='0';
        echo json_encode($rst);
    }
}