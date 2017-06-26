<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/1/27
 * Time: 15:10
 */

namespace backend\controllers\CarouselManageActions;



use frontend\business\CarouselUtil;
use frontend\business\UpdateContentUtil;
use yii\base\Action;

class SetStatusAction extends Action
{
    public function run($carousel_id)
    {
        $rst =['message'=>'','output'=>''];
        if(empty($carousel_id))
        {
            $rst['message'] = '轮播图id不能为空';
            echo json_encode($rst);
            exit;
            //ExitUtil::ExitWithMessage('用户id不能为空');
        }
        $carousel = CarouselUtil::GetCarouselById($carousel_id);
        if(!isset($carousel))
        {
            //ExitUtil::ExitWithMessage('用户不存在');
            $rst['message'] = '轮播图记录不存在';
            echo json_encode($rst);
            exit;
        }
        /*
hasEditable:1
editableIndex:0
editableKey:1
User[0][status]:0
         */
        $hasEdit = \Yii::$app->request->post('hasEditable');
        if(!isset($hasEdit))
        {
            $rst['message'] = 'hasEditable参数为空';
            echo json_encode($rst);
            exit;
        }
        if(empty($hasEdit))
        {
            $rst['message'] = '';
            echo json_encode($rst);
            exit;
        }
        $editIndex = \Yii::$app->request->post('editableIndex');
        if(!isset($editIndex))
        {
            $rst['message'] = 'editableIndex参数为空';
            echo json_encode($rst);
            exit;
        }
        $modifyData = \Yii::$app->request->post('Carousel');
        if(!isset($modifyData))
        {
            $rst['message'] = '没有Carousel模型对应的数据';
            echo json_encode($rst);
            exit;
        }

        if(!isset($modifyData[$editIndex]))
        {
            $rst['message'] = '对应的列下没有数据';
            echo json_encode($rst);
            exit;
        }
        $dataItem = $modifyData[$editIndex];
        if(!isset($dataItem['status']))
        {
            $rst['message'] = '状态值为空';
            echo json_encode($rst);
            exit;
        }
        $status = $dataItem['status'];
        $carousel->status = $status;
        if(!CarouselUtil::SaveCarousel($carousel, $error))
        {
            $rst['message'] = $error;
            echo json_encode($rst);
            exit;
        }
        UpdateContentUtil::UpdateGiftVersion($error,2);
        echo '0';
    }
} 