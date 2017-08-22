<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/8/21
 * Time: 下午3:50
 */

namespace backend\controllers\SignActions;


use backend\business\KeywordUtil;
use backend\components\ExitUtil;
use common\models\SignKeyword;
use common\models\SignParams;
use yii\base\Action;

class SetKeyAction extends Action
{
    public function run($id)
    {
        $rst = ['code'=>0, 'msg'=>''];
        if(empty($id)) {
            $rst['msg'] = '消息id不能为空';
            echo json_encode($rst);exit;
        }
        $SignParams = SignParams::findOne(['id'=>$id]);
        if(!isset($SignParams)){
            $rst['msg'] = '签到参数配置记录不存在';
            echo json_encode($rst);exit;
        }
        $params = \Yii::$app->request->post('title');
        if(isset($params))
        {
            $error = '';
            if(!KeywordUtil::SaveSignKeyWordParams($params, $id, $error)) {
                $rst['msg'] = $error;
                echo json_encode($rst);
                exit;
            }
            $rst['code'] = '0';
            echo json_encode($rst);
            exit;
        }else{
            (new SignKeyword())->deleteAll(['sign_id'=>$id]);//TODO: 删除用户原有权限数据
            $rst['code'] = '0';
            echo json_encode($rst);
            exit;
        }
    }
}