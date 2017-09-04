<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/7/4
 * Time: 下午3:50
 */

namespace backend\controllers\CustomActions;


use backend\business\WeChatUserUtil;
use backend\business\WeChatUtil;
use backend\components\ExitUtil;
use common\components\UsualFunForNetWorkHelper;
use common\models\AttentionEvent;
use common\models\Keywords;
use yii\base\Action;
use yii\web\HttpException;

class CreateMsgAction extends Action
{
    public function run()
    {
        $Cache = WeChatUserUtil::getCacheInfo();
        $menu_id = \Yii::$app->request->get('menu_id');
        $model = new AttentionEvent();
        $model->app_id =  $Cache['record_id'];
        $model->menu_id = $menu_id;
        $model->flag = 2;
        $model->msg_type = 0;
        $model->create_time = date('Y-m-d H:i:s');
        $model->order_no = 50;
        $model->global = 0;
        $load = \Yii::$app->request->post();
        if(!empty($load)){
            if($load['AttentionEvent']['msg_type'] == 2){
                $load['AttentionEvent']['picurl'] = $load['AttentionEvent']['picurl1'];
            }
        }
        if($model->load($load) && $model->save()){
            return $this->controller->redirect(['custom_msg','menu_id'=>$menu_id]);
        }else{
            return $this->controller->render('createmsg',[
                'model'=>$model,
                'cache'=>$Cache,
                'menu_id'=>$menu_id
            ]);
        }
    }
}