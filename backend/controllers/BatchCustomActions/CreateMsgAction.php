<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/7/4
 * Time: 下午3:50
 */

namespace backend\controllers\BatchCustomActions;


use backend\business\WeChatUserUtil;
use common\models\AttentionEvent;
use yii\base\Action;

class CreateMsgAction extends Action
{
    public function run()
    {
        $id = \Yii::$app->request->get('id');
        $menu_id = \Yii::$app->request->get('menu_id');
        $model = new AttentionEvent();
        $model->menu_id = $menu_id;
        $model->flag = 2;
        $model->msg_type = 0;
        $model->create_time = date('Y-m-d H:i:s');
        $model->order_no = 50;
        $load = \Yii::$app->request->post();
        if(!empty($load)){
            if($load['AttentionEvent']['msg_type'] == 2){
                $load['AttentionEvent']['picurl'] = $load['AttentionEvent']['picurl1'];
            }
        }
        if($model->load($load) && $model->save()){
            return $this->controller->redirect(['index_msg','menu_id'=>$menu_id,'id'=>$id]);
        }else{
            return $this->controller->render('createmsg',[
                'model'=>$model,
                'menu_id'=>$menu_id,
                'id'=>$id,
            ]);
        }
    }
}