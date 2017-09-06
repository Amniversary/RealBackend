<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/9/6
 * Time: 下午12:22
 */

namespace backend\controllers\TagActions;


use backend\business\TagUtil;
use common\models\SystemTag;
use common\models\SystemTagMenu;
use yii\base\Action;

class SetAuthAction extends Action
{
    public function run($id)
    {
        $rst = ['code' => 1, 'msg' => ''];
        if (empty($id)) {
            $rst['msg'] = '标签id ,不能为空';
            echo json_encode($rst);exit;
        }
        $tag = SystemTag::findOne(['id' => $id]);
        if (empty($tag) || !isset($tag)) {
            $rst['msg'] = '标签记录信息不存在';
            echo json_encode($rst);exit;
        }
        $params = \Yii::$app->request->post('title');
        if(isset($params)) {
            $error = '';
            if(!TagUtil::SaveTagParams($params, $id, $error)){
                $rst['msg'] = $error;
                echo json_encode($rst);
                exit;
            }
            $rst['code'] = 0;
            echo json_encode($rst);
            exit;
        }else{
            (new SystemTagMenu())->deleteAll(['tag_id'=> $id]);
            $rst['code'] = 0;
            echo json_encode($rst);
        }
    }
}