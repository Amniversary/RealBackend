<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/8/21
 * Time: 下午2:51
 */

namespace backend\controllers\SignActions;


use backend\business\WeChatUserUtil;
use common\models\SignParams;
use yii\base\Action;

class CheckAction extends Action
{
    public function run()
    {
        $type = \Yii::$app->request->get('type');
        $rst = ['code'=>1 , 'msg'=> ''];
        if($type == 1){
            $count = SignParams::find()->select(['count(1)'])->where(['type'=>1])->limit(1)->scalar();
            if($count >= 7) {
                $rst['msg'] = '日期已达到上限';
                echo json_encode($rst);exit;
            }
        }else{
            $cache = WeChatUserUtil::getCacheInfo();
            $count = SignParams::find()->select('count(1)')->where(['app_id'=>$cache['record_id']])->limit(1)->scalar();
            if($count >= 7) {
                $rst['msg'] = '日期已达到上限';
                echo json_encode($rst);exit;
            }
        }

        $rst['code'] = 0;
        echo json_encode($rst);
    }
}