<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/7/9
 * Time: 下午8:56
 */

namespace backend\controllers\CustomActions;


use backend\business\AuthorizerUtil;
use backend\business\WeChatUserUtil;
use yii\base\Action;

class CheckMenuNumAction extends Action
{
    public function run()
    {
        $rst = ['code'=>'1','msg'=>''];
        $cacheInfo = WeChatUserUtil::getCacheInfo();
        $num = AuthorizerUtil::getMenuCount($cacheInfo['record_id']);
        if($num >= 3){
            $rst['msg'] = '一级菜单记录已达上限，无法新增';
            echo json_encode($rst);
            exit;
        }
        $rst['code'] = 0;
        echo json_encode($rst);
    }
}