<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/7/17
 * Time: 上午2:06
 */

namespace backend\controllers\BatchCustomActions;


use backend\business\KeywordUtil;
use backend\components\ExitUtil;
use common\models\Keywords;
use common\models\MenuList;
use common\models\SystemMenu;
use yii\base\Action;

class GetAuthListAction extends Action
{
    public function run($id)
    {
        if(empty($id)) {
            ExitUtil::ExitWithMessage('配置id不能为空');
        }
        $menu_list = SystemMenu::findOne(['id'=>$id]);
        if(!isset($menu_list)){
            ExitUtil::ExitWithMessage('配置记录不存在');
        }
        $selection = KeywordUtil::GetCustomAuthById($id);//TODO:公众号已有配置
        $rights = KeywordUtil::GetAuthParams();//TODO: 配置列表
        $this->controller->layout='main_empty';
        return $this->controller->render('setauthlist',[
            'menu_list'=>$menu_list,
            'rights'=>$rights,
            'selections' =>$selection,
        ]);
    }
}