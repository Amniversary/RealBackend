<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/1/27
 * Time: 11:21
 */

namespace backend\controllers\ReportManageActions;


use common\models\User;
use frontend\business\HotWordsUtil;
use yii\base\Action;
use yii\log\Logger;
use yii\web\HttpException;

class DeleteAction extends Action
{
    public function run($hot_words_id)
    {
        //$rst=['code'=>'1','msg'=>''];
        if(empty($hot_words_id))
        {
            throw new HttpException(500,'热词id不能为空');
            //$rst['msg']='轮播图id不能为空';
            //echo json_encode($rst);
            exit;
        }
        $hotWords = HotWordsUtil::GetHotWordsById($hot_words_id);
        if(!isset($hotWords))
        {
            throw new HttpException(500,'热词信息不存在');
            //$rst['msg']='轮播图信息不存在';
            //echo json_encode($rst);
            exit;
        }
        if($hotWords->delete() === false)
        {
            throw new HttpException(500,'删除失败');
            //$rst['msg']='删除失败';
            \Yii::getLogger()->log('删除失败:'.var_export($carousel->getErrors(),true),Logger::LEVEL_ERROR);
            //echo json_encode($rst);
            exit;
        }
        return $this->controller->redirect('index');
    }
}
//$.fn.yiiGridView.update('apply-grid');