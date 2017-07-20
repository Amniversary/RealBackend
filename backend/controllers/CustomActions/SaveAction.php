<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/7/9
 * Time: 下午8:02
 */

namespace backend\controllers\CustomActions;


use backend\business\AuthorizerUtil;
use backend\business\WeChatUserUtil;
use common\components\UsualFunForNetWorkHelper;
use yii\base\Action;
use yii\db\Query;

class SaveAction extends Action
{
    public function run()
    {
        $rst = ['code'=>'1','msg'=>''];
        $cacheInfo = WeChatUserUtil::getCacheInfo();
        $query = AuthorizerUtil::getMenuList($cacheInfo['record_id']);
        if(!$query){
            $rst['msg'] = '菜单列表为空，请先设置菜单';
            echo json_encode($rst);
            exit;
        }
        $res = WeChatUserUtil::setMenuList($query,$cacheInfo['authorizer_access_token'],$error);
        if(!$res){
            $rst['msg'] = $error;
            echo json_encode($rst);
            exit;
        }
        if($res['errcode'] != 0 || !$res){
            $rst['msg'] = '设置失败: Code: '.$res['errcode'] . ' '. $res['errmsg'];
            echo json_encode($rst);
            exit;
        }
        $rst['code'] = '0';
        $rst['msg'] = '保存成功';
        echo json_encode($rst);
    }
}