<?php
/**
 * Created by PhpStorm.
 * User: hlq
 * Date: 2016/6/21
 * Time: 11:21
 */

namespace backend\controllers\VersionManageActions;


use frontend\business\MultiUpdateContentUtil;
use yii\base\Action;
use yii\log\Logger;

class DeleteSonAction extends Action
{
    public function run($update_id)
    {
        $rst=['code'=>'1','msg'=>''];
        if(empty($update_id))
        {
            $rst['msg']='子版本id不能为空';
            echo json_encode($rst);
            exit;
        }
        $content = MultiUpdateContentUtil::GetUpdateContentById($update_id);
        if(!isset($content))
        {
            $rst['msg']='子版本记录不存在';
            echo json_encode($rst);
            exit;
        }

        if($content->delete() === false)
        {
            $rst['msg']='删除失败';
            \Yii::getLogger()->log('删除失败:'.var_export($content->getErrors(),true),Logger::LEVEL_ERROR);
            echo json_encode($rst);
            exit;
        }
        \Yii::$app->cache->delete('app_version_info');
        $rst =['msg'=>'','code'=>0];
        echo json_encode($rst);
        exit;

        //return $this->controller->redirect(['/versionmanage/indexson','app_id'=>$content->app_id]);
    }
}