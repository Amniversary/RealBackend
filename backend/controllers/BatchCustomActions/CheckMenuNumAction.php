<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/7/28
 * Time: 下午3:10
 */

namespace backend\controllers\BatchCustomActions;


use backend\business\AuthorizerUtil;
use yii\base\Action;

class CheckMenuNumAction extends Action
{
    public function run($id)
    {
        $rst = ['code'=>1, 'msg'=>''];
        if(empty($id)) {
            $rst['msg']= '参数Id不能为空';
            echo json_encode($rst);
            exit;
        }
         $num = AuthorizerUtil::getGlobalMenuCount($id);
        if($num >= 3){
            $rst['msg'] = '一级菜单记录已达上限，无法新增';
            echo json_encode($rst);
            exit;
        }
        $rst['code'] = 0;
        echo json_encode($rst);
    }
}