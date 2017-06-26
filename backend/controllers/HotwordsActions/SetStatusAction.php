<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/1/27
 * Time: 15:10
 */

namespace backend\controllers\HotwordsActions;



use frontend\business\CarouselUtil;
use frontend\business\HotWordsUtil;
use yii\base\Action;

class SetStatusAction extends Action
{
    public function run($hot_words_id)
    {
        $rst =['message'=>'','output'=>''];
        if(empty($hot_words_id))
        {
            $rst['message'] = '热词id不能为空';
            echo json_encode($rst);
            exit;
            //ExitUtil::ExitWithMessage('用户id不能为空');
        }
        $hotwords = HotWordsUtil::GetHotWordsById($hot_words_id);
        if(!isset($hotwords))
        {
            //ExitUtil::ExitWithMessage('用户不存在');
            $rst['message'] = '热词记录不存在';
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
        $modifyData = \Yii::$app->request->post('HotWords');
        if(!isset($modifyData))
        {
            $rst['message'] = '没有HotWords模型对应的数据';
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
        $hotwords->status = $status;
        if(!HotWordsUtil::SaveHotWords($hotwords, $error))
        {
            $rst['message'] = $error;
            echo json_encode($rst);
            exit;
        }
        echo '0';
    }
} 