<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/1/27
 * Time: 15:10
 */

namespace backend\controllers\ActivityInfoActions;



use common\models\EnrollInfo;
use yii\base\Action;

/**
 * 设置审核
 * Class SetStatusAction
 * @package backend\controllers\ClientActions
 */
class SetStatusAction extends Action
{
    public function run($enroll_id)
    {
        $rst =['message'=>'','output'=>''];
        if(empty($enroll_id))
        {
            $rst['message'] = '报名用户id不存在';
            echo json_encode($rst);
            exit;
        }
        $enroll_info = EnrollInfo::GetEnrollInfoById($enroll_id);
        if(!isset($enroll_info))
        {
            $rst['message'] = '报名用户记录不存在';
            echo json_encode($rst);
            exit;
        }

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
        $modifyData = \Yii::$app->request->post('EnrollInfo');
        if(!isset($modifyData))
        {
            $rst['message'] = '没有EnrollInfo模型对应的数据';
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
        $enroll_info->status = $status;
        if(!EnrollInfo::SaveEnuollInfo($enroll_info,$error))
        {
            $rst['message'] = $error;
            echo json_encode($rst);
            exit;
        }
/*        $key = 'client_forbid_flag_'.$enroll_id;
        if($status === '1')
        {
            //启用
            \Yii::$app->cache->delete($key);
        }
        else
        {
            //禁用
            \Yii::$app->cache->set($key,'1');
        }*/
        echo '0';
    }
} 