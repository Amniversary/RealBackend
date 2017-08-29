<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/8/28
 * Time: 下午5:37
 */

namespace backend\controllers\UserManageActions;


use backend\business\UserMenuUtil;
use backend\business\UserUtil;
use backend\components\ExitUtil;
use common\models\Menu;
use common\models\UserMenu;
use yii\base\Action;
use yii\db\Query;

class GetBackendAction extends Action
{
    public function run($user_id)
    {
        if(empty($user_id)) {
            ExitUtil::ExitWithMessage('用户id不能为空');
        }
        $user = UserUtil::GetUserByUserId($user_id);//根据id获取用户信息
        if(!isset($user)) {
            ExitUtil::ExitWithMessage('用户不存在');
        }
        $selection = UserMenuUtil::GetUserBackendMenuByUserId($user_id);//用户已有权限
        $rights = UserMenuUtil::GetUserBackendTitle();//权限列表
        $this->controller->layout='main_empty';
        $setMenuPower = (new Query())
            ->select(['mu.menu_id'])
            ->from(Menu::tableName() . 'mu')
            ->innerJoin(UserMenu::tableName(). 'um','mu.menu_id=um.menu_id')
            ->where([
                'url'=>'usermanage/set_backend',
                'user_id' => \Yii::$app->user->identity->backend_user_id
            ])->one();
        return $this->controller->render('get_backend',[
            'user'=>$user,
            'rights'=>$rights,
            'selections' =>$selection,
            'haveSetPower' => !empty($setMenuPower)
        ]);
    }
}