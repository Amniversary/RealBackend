<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/7/28
 * Time: 下午5:27
 */

namespace backend\controllers\BatchCustomActions;


use backend\business\AuthorizerUtil;
use yii\base\Action;

class CheckMenuSonAction extends  Action
{
    public function run($menu_id)
    {
        $rst = ['code'=>1 , 'msg'=>''];
        if(empty($menu_id)) {
            $rst['msg']= '参数Id不能为空';
            echo json_encode($rst);
            exit;
        }
        $num = AuthorizerUtil::getMenuSonCount($menu_id);
        if($num >= 5){
            $rst['msg'] = '二级菜单最多设置5个，记录已达上限，无法新增';
            echo json_encode($rst);
            exit;
        }
        $rst['code'] = 0;
        echo json_encode($rst);
    }
}