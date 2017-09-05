<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/6/29
 * Time: 下午4:20
 */

namespace backend\controllers\KeyWordActions;


use backend\business\WeChatUserUtil;
use backend\business\WeChatUtil;
use common\models\Keywords;
use yii\base\Action;

class CreateAction extends Action
{
    public function run()
    {
        $model = new Keywords();
        $Cache = WeChatUserUtil::getCacheInfo();
        $model->app_id = $Cache['record_id'];
        $model->global = 0;
        $post = \Yii::$app->request->post();
        switch($post['Keywords']['rule']) {
            case 3: $post['Keywords']['keyword'] = '图片匹配';break;
            case 4: $post['Keywords']['keyword'] = '语音匹配';break;
            case 5: $post['keywords']['keyword'] = '视频匹配';break;
        }
        if($model->load($post) && $model->save()){
            return $this->controller->redirect('createkey');
        }else{
            return $this->controller->render('_form',[
                'model'=>$model,
                'cache'=>$Cache,
            ]);
        }
    }
}