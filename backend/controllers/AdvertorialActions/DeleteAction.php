<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/31
 * Time: 13:23
 */
namespace backend\controllers\AdvertorialActions;



use backend\business\AdvertorialUtil;
use yii\base\Action;
use yii\log\Logger;

class DeleteAction extends Action
{
    public function run($record_id)
    {
        $rst=['code'=>'1','msg'=>''];
        if(empty($record_id))
        {
            $rst['msg']='活动参数id不能为空';
            echo json_encode($rst);
            exit;
        }
        $model = AdvertorialUtil::AdvertorialById($record_id);
        if(!isset($model))
        {
            $rst['msg']='活动参数不存在';
            echo json_encode($rst);
            exit;
        }

        if($model->delete() === false)
        {
            $rst['msg']='活动删除失败';
            \Yii::getLogger()->log('删除失败:'.var_export($model->getErrors(),true),Logger::LEVEL_ERROR);
            echo json_encode($rst);
            exit;
        }
        //删除缓存
        \Yii::$app->cache->delete("model");
        //获取前一个页面的地址
        return $this->controller->redirect('/advertorial/index');

        $rst['code'] = '0';
        echo json_encode($rst);
        exit;
    }
}