<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/7/27
 * Time: 下午1:50
 */

namespace backend\controllers\BatchCustomActions;


use backend\business\KeywordUtil;
use backend\components\ExitUtil;
use common\models\AttentionEvent;
use common\models\BatchKeywordList;
use common\models\Keywords;
use common\models\MenuList;
use common\models\SystemMenu;
use yii\base\Action;

class SetAuthListAction extends Action
{
    public function run($id)
    {
        if(empty($id)) {
            ExitUtil::ExitWithMessage('配置id不能为空');
        }
        $system = SystemMenu::findOne(['id'=>$id]);
        if(!isset($system)){
            ExitUtil::ExitWithMessage('配置记录不存在');
        }
        $params = \Yii::$app->request->post('title');
        if(isset($params))
        {
            $rst = ['code' => '1', 'msg' => ''];
            $error = '';
            if(!KeywordUtil::SaveMenuAuthParams($params,$id,$error)) {
                $rst['msg'] = $error;
                echo json_encode($rst);
                exit;
            }
            $rst['code'] = '0';
            $rst['msg'] = $error;
            echo json_encode($rst);
            exit;
        }else{
            (new MenuList())->deleteAll(['deploy_id'=>$id]);//TODO: 删除用户原有权限数据
            $rst['code'] = '0';
            echo json_encode($rst);
            exit;
        }
    }
}