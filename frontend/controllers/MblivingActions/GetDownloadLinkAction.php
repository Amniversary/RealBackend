<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/1/6
 * Time: 17:05
 */

namespace frontend\controllers\MblivingActions;


use common\models\MulitUpdateContent;
use frontend\business\RechargeListUtil;
use yii\base\Action;
use yii\log\Logger;

class GetDownloadLinkAction extends Action
{
    public function run()
    {
        $rst = ['code'=>'1', 'msg'=>''];
        $download_type = \Yii::$app->request->post('download_type');
        $app_id = \Yii::$app->request->post('app_id');

        if(isset($download_type) && isset($app_id))
        {
            $downloadlink = MulitUpdateContent::findOne(['app_id'=>$app_id,'module_id'=>$download_type]);
            $rst['code'] = 0;
            $rst['msg'] = $downloadlink->link;
            echo json_encode($rst);
            return false;
        }

        $downloadlink = MulitUpdateContent::findOne(['update_id'=>1]);
        if(!isset($downloadlink))
        {
            $rst['msg'] = '连接地址不存在';
            echo json_encode($rst);
            return false;
        }

        $rst['code'] = 0;
        $rst['msg'] = $downloadlink->link;
        echo json_encode($rst);
        return false;
    }
}