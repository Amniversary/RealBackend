<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/1/27
 * Time: 15:10
 */

namespace backend\controllers\AdvertisingManageActions;



use common\models\AdImages;
use yii\base\Action;

class DeleteAction extends Action
{
    public function run($ad_id)
    {
        $rst=['code'=>'0','msg'=>''];
        if(empty($ad_id))
        {
            $rst['msg'] = '弹窗广告图id不能为空';
            echo json_encode($rst);
            exit;
            //ExitUtil::ExitWithMessage('用户id不能为空');
        }
        $adImages = AdImages::findOne(['ad_id' => $ad_id]);
        if(!isset($adImages))
        {
            //ExitUtil::ExitWithMessage('用户不存在');
            $rst['msg'] = '弹窗广告图记录不存在';
            echo json_encode($rst);
            exit;
        }
        if(!$adImages->delete())
        {
            $rst['msg'] = var_export($adImages->getErrors(),true);
            echo json_encode($rst);
            exit;
        }
        echo json_encode($rst);
        exit;
    }
} 