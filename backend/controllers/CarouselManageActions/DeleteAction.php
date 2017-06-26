<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/1/27
 * Time: 11:21
 */

namespace backend\controllers\CarouselManageActions;


use common\models\User;
use frontend\business\CarouselUtil;
use frontend\business\UpdateContentUtil;
use yii\base\Action;
use yii\base\Exception;
use yii\log\Logger;
use yii\web\HttpException;

class DeleteAction extends Action
{
    public function run($carousel_id)
    {
        $rst=['code'=>'1','msg'=>''];
        if(empty($carousel_id))
        {
            throw new HttpException(500,'轮播图id不能为空');
            //$rst['msg']='轮播图id不能为空';
            //echo json_encode($rst);
            exit;
        }
        $carousel = CarouselUtil::GetCarouselById($carousel_id);
        if(!isset($carousel))
        {
            throw new HttpException(500,'轮播图信息不存在');
            //$rst['msg']='轮播图信息不存在';
            //echo json_encode($rst);
            exit;
        }
        if($carousel->delete() === false)
        {
            throw new HttpException(500,'删除失败');
            //$rst['msg']='删除失败';
            \Yii::getLogger()->log('删除失败:'.var_export($carousel->getErrors(),true),Logger::LEVEL_ERROR);
            //echo json_encode($rst);
            exit;
        }
        UpdateContentUtil::UpdateGiftVersion($error,2);
        return $this->controller->redirect('index');
    }
}
//$.fn.yiiGridView.update('apply-grid');