<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/29
 * Time: 14:07
 */

namespace backend\controllers\ClientActions;

use common\models\Balance;
use yii\base\Action;

class SetFreezeStatusAction extends Action {
    public function run($user_id){
        $rst =['message'=>'','output'=>''];
        $user = Balance::findOne(['user_id'=>$user_id]);
        if(is_null($user))
        {
            $rst['message'] = '用户不存在';
            echo json_encode($rst);
            exit;
        }

        /*$status = $user['freeze_status'];
        $status = $status == 1 ? 2 : 1;*/
        $editableAttribute = \Yii::$app->request->post('editableAttribute');

        if ( $editableAttribute == 'bean_status' ) {
            $status = \Yii::$app->request->post('bean_status');
            $user['bean_status'] = $status;
        }else{
            $status = \Yii::$app->request->post('freeze_status');
            $user['freeze_status'] = $status;
        }
        if($user->save()){
            echo '0';
        }

    }
} 