<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/12
 * Time: 16:46
 */

namespace backend\controllers\SendMsgActions;

use yii\base\Action;
use common\models\SendMsg;
use yii\log\Logger;
use common\components\getui\GeTuiUtil;
use common\components\UsualFunForStringHelper;
use common\models\Client;
use yii\base\Exception;
use frontend\business\SendMsgUtil;

class SendAction extends Action
{
    public function run()
    {
        $this->controller->getView()->title = '推送管理';
        $model = new SendMsg();
        if( \Yii::$app->request->isPost ){

            $data = \Yii::$app->request->post('SendMsg');
            if( $data['send_time'] )
            {
                $curTime = date("Y-m-d H:i:00");

                if( $data['send_time'] <= $curTime  )
                {
                    $error =  '定时推送的时间不能小于等于当前时间';
                    \Yii::getLogger()->log($error,Logger::LEVEL_ERROR );
                    \Yii::$app->getSession()->setFlash('error',$error);
                    $this->controller->redirect('/sendmsg/index');
                }
            }

            $sendMsg = self::SaveSendInfo( $data );

            if( $sendMsg )
            {
                //实时推送
                if( $sendMsg['send_time_type'] == 1 )
                {
                    if( $sendMsg['send_type'] == 1 )
                    {
                        //全量推送,向所有的app推送
                        SendMsgUtil::SendMsgToAllApp( $sendMsg['title'],$sendMsg['cotent'],$error );
                        $info = "实时全量推送因推送数据量大，推送中.....";
                        \Yii::$app->getSession()->setFlash( 'success',$info );
                        $this->controller->redirect('/sendmsg/index');
                    }
                    else if( $sendMsg['send_type'] == 2 )
                    {
                        //定向推送,
                        SendMsgUtil::GuidingToSendMsg( $sendMsg['msg_id'],$sendMsg['title'],$sendMsg['content'],$sendMsg['target'],$error );
                        $info = "实时推送下的定向推送，推送中.....";
                        \Yii::$app->getSession()->setFlash( 'success',$info );
                        $this->controller->redirect('/sendmsg/index');
                    }
                }
                else if( $sendMsg['send_time_type'] == 2 )
                {
                    $info = "定时推送已成功加入任务队列";
                    \Yii::$app->getSession()->setFlash('success',$info);
                    $this->controller->redirect('/sendmsg/index');
                }
            }
        }
        else
        {
            return $this->controller->render('send', [
                'model' => $model,
            ]);
        }
    }

    private static function SaveSendInfo( $data )
    {
        $model = new SendMsg();
        if( !$data['title']  )
        {
            \Yii::getLogger()->log('推送标题不能为空',Logger::LEVEL_ERROR);
            return false;
        }

        if( strlen( $data['title'] )>255  )
        {
            \Yii::getLogger()->log('推送标题不能大于255个字符',Logger::LEVEL_ERROR);
            return false;
        }

        if( !$data['content']  )
        {
            \Yii::getLogger()->log('推送内容不能为空',Logger::LEVEL_ERROR);
            return false;
        }

        if( strlen( $data['content'] ) >255 )
        {
            \Yii::getLogger()->log('推送内容不能大于255个字符',Logger::LEVEL_ERROR);
            return false;
        }

        if( $data['send_type']==2 && $data['target'] )
        {
            $target =  self::ReadTargetFile( $data['target'] );
        }

        $msg_id = UsualFunForStringHelper::CreateGUID();
        $model->msg_id = $msg_id;
        $model->backend_user_id = 1;
        $model->title   = "26-".$data['title'];
        $model->content = $data['content'];
        if( empty($data['send_time_type'] ) )
        {
            $model->send_time_type = 1;
        }
        else
        {
            $model->send_time_type = $data['send_time_type'];
        }

        $model->send_time = $data['send_time'];
        $model->send_url  = $data['send_url'];

        if( empty($data['send_type']) )
        {
            $model->send_type = 1;
        }
        else
        {
            $model->send_type = $data['send_type'];
        }

        if( $target )
        {
            $model->target = $target;
        }
        else
        {
            $model->target = "";
        }
        $model->send_status = 1;
        $model->create_time = date('Y-m-d H:i:s');
        $model->pagevisit = 0;
        $model->remark1 = $data['target'];

        if( !$model->save() )
        {
            \Yii::getLogger()->log('推送数据保存失败：'.var_export($model->getErrors(),true),Logger::LEVEL_ERROR);
            return false;
        }

        return SendMsg::findOne(['msg_id'=>$msg_id]);
    }

    function ReadTargetFile( $path )
    {
        if( $path ){
            $content = file_get_contents( $path );
            if( $content )
            {
                $content = preg_replace('/\n/', ',', $content);
                $content = preg_replace('/\s/', '', $content);
                $content = explode( ',',$content );
                $target = "";
                foreach ( $content as $val )
                {
                    if( empty( $target ) )
                    {
                        $target = "'$val'";
                    }
                    else
                    {
                        $target.= ','."'$val'";
                    }

                }
                return $target;
            }
        }
    }

}