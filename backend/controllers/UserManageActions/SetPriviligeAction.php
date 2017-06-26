<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/2/23
 * Time: 15:58
 */
    namespace backend\controllers\UserManageActions;

    use backend\business\UserMenuUtil;
    use backend\business\UserUtil;
    use common\models\UserMenu;
    use yii\base\Action;
    use backend\components\ExitUtil;
    use yii\log\Logger;


    class SetpriviligeAction extends Action
    {
        public function run($user_id)
        {
            if(empty($user_id)) {
                ExitUtil::ExitWithMessage('用户id不能为空');
            }
            $user = UserUtil::GetUserByUserId($user_id);//TODO: 根据id获取用户信息
            if(!isset($user)) {
                ExitUtil::ExitWithMessage('用户不存在');
            }

            $params = \Yii::$app->request->post('title');
            $selection = UserMenuUtil::GetUserMenuByUserID($user_id);//TODO:用户已有权限
            $rights = UserMenuUtil::GetUserMenuTitle();//TODO:权限列表
            if(isset($params))
            {
                $rst = ['code' => '1', 'msg' => ''];
                $error = '';
                if(!UserMenuUtil::SaveUserMenus($params,$user_id,$error)) {
                    $rst['msg'] = $error;
                    echo json_encode($rst);
                    exit;
                }
                $key = 'user_menu_'.strval($user_id);
                \Yii::$app->cache->delete($key);
                $key_power = 'user_power_'.$user_id;
                \Yii::$app->cache->delete($key_power);
                $rst['code'] = '0';
                echo json_encode($rst);
                exit;
            }
            $this->controller->layout='main_empty';
            return $this->controller->render('setpriviligelist',[
                'user'=>$user,
                'rights'=>$rights,
                'selections' =>$selection
            ]);
        }
    }