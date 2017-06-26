<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/8
 * Time: 18:19
 */

namespace backend\controllers\ClientalbumActions;


use common\components\tenxunlivingsdk\TimRestApi;
use frontend\business\DynamicUtil;
use yii\base\Action;
use yii\log\Logger;
use yii\web\HttpException;

class DeleteAction extends Action
{
    public function run($dynamic_id,$type)
    {
        $rst=['code'=>'1','msg'=>''];
//         = \Yii::$app->request->post(['dynamic_id']);
        if(empty($dynamic_id))
        {
            $rst['msg']='相册动态id不能为空';
            echo json_encode($rst);
            exit;
        }

        //修改用户相册信息
        $dynamicInfo = DynamicUtil::GetDynamicById($dynamic_id);
        $update = DynamicUtil::UpdateDynamicStatus($dynamic_id);


        if(!$update)
        {
            $rst['msg']='删除相册失败';
            echo json_encode($rst);
            exit;
        }
        else
        {
            //推送系统消息 跟用户说明
            $content = "温馨提醒，您有一张照片不符合规范，已被管理员删除";
            if(!TimRestApi::openim_send_Text_msg($dynamicInfo['user_id'],$content,$error))
            {
                \Yii::getLogger()->log('发送腾讯云通知消息异常: '.$error,Logger::LEVEL_ERROR);
                $error = '删除相册消息推送失败';
                return false;
            }
        }


        switch($type)
        {
            case 'index':
                return $this->controller->redirect(['index']);
                break;
            case 'viwe_all':
                return $this->controller->redirect(['viwe_all']);
                break;
        }


        return true;


    }
}