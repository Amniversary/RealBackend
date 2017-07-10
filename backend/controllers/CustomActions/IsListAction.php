<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/7/6
 * Time: 下午3:58
 */

namespace backend\controllers\CustomActions;


use backend\business\AuthorizerUtil;
use common\models\AttentionEvent;
use common\models\AuthorizationMenu;
use yii\base\Action;

class IsListAction extends Action
{
    public function run($menu_id)
    {
        $rst =['message'=>'','output'=>''];
        if(empty($menu_id)) {
            $rst['message'] = '菜单id 不能为空';
            echo json_encode($rst);
            exit;
        }
        $data = AuthorizationMenu::findOne(['menu_id'=>$menu_id]);
        if(!isset($data)) {
            $rst['message'] = '菜单记录不存在';
            echo json_encode($rst);
            exit;
        }
        $hasEdit = \Yii::$app->request->post('hasEditable');
        if(!isset($hasEdit)) {
            $rst['message'] = 'hasEditable参数为空';
            echo json_encode($rst);
            exit;
        }
        $editIndex = \Yii::$app->request->post('editableIndex');
        if(!isset($editIndex)) {
            $rst['message'] = 'editableIndex参数为空';
            echo json_encode($rst);
            exit;
        }
        $modifyData = \Yii::$app->request->post('AuthorizationMenu');
        if(!isset($modifyData)) {
            $rst['message'] = '没有User模型对应的数据';
            echo json_encode($rst);
            exit;
        }

        if(!isset($modifyData[$editIndex])) {
            $rst['message'] = '对应的列下没有数据';
            echo json_encode($rst);
            exit;
        }
        $dataItem = $modifyData[$editIndex];
        if(!isset($dataItem['is_list'])) {
            $rst['message'] = '状态值为空';
            echo json_encode($rst);
            exit;
        }
        $status = $dataItem['is_list'];

        $data->is_list = $status;
        if(!AuthorizerUtil::SaveWxMenu($data, $error))
        {
            $rst['message'] = $error;
            echo json_encode($rst);
            exit;
        }
        echo '0';
    }
}