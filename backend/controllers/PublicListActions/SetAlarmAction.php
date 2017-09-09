<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/9/8
 * Time: 下午6:22
 */

namespace backend\controllers\PublicListActions;


use backend\business\AuthorizerUtil;
use yii\base\Action;

class SetAlarmAction extends Action
{
    public function run($record_id, $status)
    {
        $rst = ['code'=>1, 'msg'=> ''];
        if(empty($record_id) || empty($status)) {
            $rst['msg'] = '参数缺少';
            echo json_encode($rst);exit;
        }
        $auth = AuthorizerUtil::getAuthByOne($record_id);
        if(empty($auth)) {
            $rst['msg']= '公众号记录信息不存在';
            echo json_encode($rst);exit;
        }

        if($status == 1) {
            $auth->alarm_status = 0;
        }else if($status == 2) {
            $auth->alarm_status = 1;
        }

        if(!$auth->save()) {
            $rst['msg'] = '修改告警状态失败';
            \Yii::error($rst['msg']. ' '.var_export($auth->getErrors(),true));
            echo json_encode($rst);exit;
        }

        $rst['code'] = 0;
        echo json_encode($rst);
    }
}