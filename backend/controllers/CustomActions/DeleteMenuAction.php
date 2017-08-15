<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/7/29
 * Time: 下午5:08
 */

namespace backend\controllers\CustomActions;


use backend\business\WeChatUserUtil;
use common\models\AuthorizationMenu;
use yii\base\Action;
use yii\db\Exception;
use yii\db\Query;

class DeleteMenuAction extends Action
{
    public function run()
    {
        $rst = ['code'=>1, 'msg'=>''];
    
        $cache = WeChatUserUtil::getCacheInfo();
        $res = WeChatUserUtil::deleteWxCustomMenu($cache['authorizer_access_token']);
        if($res['errcode'] != 0) {
            $rst['msg'] = 'Code :'.$rst['errcode'] .' '.$rst['errmsg'];
            echo json_encode($rst);
            exit;
        }
        $query = (new Query())->from('wc_authorization_menu')->select(['menu_id'])->where(['app_id'=>$cache['record_id']])->all();
        try{
            AuthorizationMenu::deleteAll(['app_id'=>$cache['record_id']]);
            foreach($query as $item) {
                AuthorizationMenu::deleteAll(['parent_id'=>$item['menu_id']]);
            }
        }catch(Exception $e){
            $rst['msg'] = $e->getMessage();
            echo json_encode($rst);
            exit;
        }
        $rst['code'] = 0;
        echo json_encode($rst);
        exit;
    }
}