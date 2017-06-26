<?php
/**
 * Created by PhpStorm.
 * User: hlq
 * Date: 2016/6/21
 * Time: 11:21
 */

namespace backend\controllers\VersionManageActions;


use frontend\business\MultiUpdateContentUtil;
use frontend\business\MultiVersionInfoUtil;
use yii\base\Action;
use yii\log\Logger;

class DeleteAction extends Action
{
    public function run($record_id)
    {
        $rst=['code'=>'1','msg'=>''];
        if(empty($record_id))
        {
            $rst['msg']='版本id不能为空';
            echo json_encode($rst);
            exit;
        }
        $version = MultiVersionInfoUtil::GetVersionById($record_id);
        if(!isset($version))
        {
            $rst['msg']=' 版本记录不存在';
            echo json_encode($rst);
            exit;
        }

        $content = MultiUpdateContentUtil::CheckAppIdIsContent($version->app_id);
        if(!empty($content->app_id))
        {
            $rst['msg']='请先删除子版本';
            echo json_encode($rst);
            exit;
        }
        if($version->delete() === false)
        {
            $rst['msg']='删除失败';
            \Yii::getLogger()->log('删除失败:'.var_export($version->getErrors(),true),Logger::LEVEL_ERROR);
            echo json_encode($rst);
            exit;
        }
        //$url = \Yii::$app->request->getAbsoluteUrl();
        //return $this->controller->redirect('/versionmanage/index');
        \Yii::$app->cache->delete('app_version_info');
       $rst =['msg'=>'','code'=>0];
       echo json_encode($rst);

        //return $this->controller->redirect(['/versionmanage/indexson','app_id'=>$version->app_id]);
    }
}