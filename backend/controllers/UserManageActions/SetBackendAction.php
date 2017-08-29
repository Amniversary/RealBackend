<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/8/28
 * Time: 下午5:50
 */

namespace backend\controllers\UserManageActions;


use backend\business\UserMenuUtil;
use backend\business\UserUtil;
use backend\components\ExitUtil;
use common\models\BackendMenu;
use yii\base\Action;

class SetBackendAction extends Action
{
    public function run($user_id)
    {
        if (empty($user_id)) {
            ExitUtil::ExitWithMessage('用户id 不能为空');
        }
        $User = UserUtil::GetUserByUserId($user_id);
        if (!isset($User)) {
            ExitUtil::ExitWithMessage('用户不存在');
        }
        $params = \Yii::$app->request->post('title');
        if (isset($params)) {
            $rst = ['code' => '1', 'msg' => ''];
            $error = '';
            if (!UserMenuUtil::SaveUserBackendMenu($params, $user_id, $error)) {
                $rst['msg'] = $error;
                echo json_encode($rst);
                exit;
            }
            $rst['code'] = '0';
            echo json_encode($rst);
            exit;
        } else {
            (new BackendMenu())->deleteAll(['user_id' => $user_id]);//TODO: 删除用户原有权限数据
            $rst['code'] = '0';
            echo json_encode($rst);
            exit;
        }
    }
}