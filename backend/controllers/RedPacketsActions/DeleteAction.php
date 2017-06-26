<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/4/1
 * Time: 10:31
 */
    namespace backend\controllers\RedPacketsActions;


use frontend\business\RedPacketsUtil;
use yii\base\Action;
use yii\log\Logger;
use yii\web\HttpException;

class DeleteAction extends Action
{
    public function run($red_packets_id)
    {
        //$rst=['code'=>'1','msg'=>''];
        if(empty($red_packets_id))
        {
            throw new HttpException(500,'红包id不能为空');
            //$rst['msg']='轮播图id不能为空';
            //echo json_encode($rst);
            exit;
        }
        $redPacketsWords = RedPacketsUtil::GetRedPacketsById($red_packets_id);
        if(!isset($redPacketsWords))
        {
            throw new HttpException(500,'红包信息不存在');
            //$rst['msg']='轮播图信息不存在';
            //echo json_encode($rst);
            exit;
        }
        if($redPacketsWords->delete() === false)
        {
            throw new HttpException(500,'删除失败');
            //$rst['msg']='删除失败';
            \Yii::getLogger()->log('删除失败:'.var_export($redPacketsWords->getErrors(),true),Logger::LEVEL_ERROR);
            //echo json_encode($rst);
            exit;
        }
        return $this->controller->redirect('index');
    }
}