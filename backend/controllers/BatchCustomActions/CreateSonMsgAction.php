<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/7/4
 * Time: 下午3:50
 */

namespace backend\controllers\BatchCustomActions;


use backend\business\WeChatUserUtil;
use backend\business\WeChatUtil;
use backend\components\ExitUtil;
use common\components\UsualFunForNetWorkHelper;
use common\models\AttentionEvent;
use common\models\Keywords;
use yii\base\Action;
use yii\web\HttpException;

class CreateSonMsgAction extends Action
{
    public function run()
    {
        $parent_id = \Yii::$app->request->get('parent_id');
        $menu_id = \Yii::$app->request->get('menu_id');
        $id = \Yii::$app->request->get('id');
        $model = new AttentionEvent();
        $model->menu_id = $menu_id;
        $model->flag = 2;
        $model->msg_type = 0;
        $model->create_time = date('Y-m-d H:i:s');
        $model->order_no = 50;
        $model->global = 1;
        $load = \Yii::$app->request->post();
        if(!empty($load)){
            if($load['AttentionEvent']['msg_type'] == 2){
                $load['AttentionEvent']['picurl'] = $load['AttentionEvent']['picurl1'];
            }
        }
        if($model->load($load) && $model->save()){
            return $this->controller->redirect(['indexson_msg','menu_id'=>$menu_id, 'parent_id'=>$parent_id,'id'=>$id]);
        }else{
            return $this->controller->render('createsonmsg',[
                'model'=>$model,
                'menu_id'=>$menu_id,
                'parent_id'=>$parent_id,
                'id'=>$id,
            ]);
        }
    }
}