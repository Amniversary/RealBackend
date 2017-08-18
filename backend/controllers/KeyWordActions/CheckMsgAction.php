<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/7/4
 * Time: 下午5:46
 */

namespace backend\controllers\KeyWordActions;


use backend\business\WeChatUserUtil;
use common\models\Keywords;
use yii\base\Action;

class CheckMsgAction extends Action
{
    public function run()
    {
        $rst = ['code'=>'1','msg'=>''];
        //$Cache = WeChatUserUtil::getCacheInfo();
        /*$model = Keywords::findOne(['app_id'=>$Cache['record_id']]);
        if(empty($model)){
            $rst['msg']= '请先创建关键词';
            echo json_encode($rst);
            exit;
        }*/
        $rst['code'] = '0';
        echo json_encode($rst);
        exit;
    }
}