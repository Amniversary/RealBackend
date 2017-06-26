<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/1/27
 * Time: 11:21
 */

namespace backend\controllers\GiftActions;


use common\models\Gift;
use frontend\business\UpdateContentUtil;
use yii\base\Action;
use yii\log\Logger;
use frontend\business\FrontendCacheKeyUtil;
use frontend\business\LivingUtil;
class DeleteAction extends Action
{
    public function run($gift_id)
    {
        $rst=['code'=>'0','msg'=>''];
        if(empty($gift_id))
        {
            $rst['msg']='礼物id不能为空';
            echo json_encode($rst);
            exit;
        }
        $Gift = Gift::findOne(['gift_id'=>$gift_id]);
        if(!isset($Gift))
        {
            $rst['msg']='礼物不存在';
            echo json_encode($rst);
            exit;
        }

        if($Gift->delete() === false)
        {
            $rst['msg']='删除失败';
            \Yii::getLogger()->log('删除失败:'.var_export($Gift->getErrors(),true),Logger::LEVEL_ERROR);
            echo json_encode($rst);
            exit;
        }else
        {
            $key = FrontendCacheKeyUtil::FRONTEND_V2_ZHIBOGETGIFTS_LIST_ALL;
            if( \Yii::$app->cache->get($key) ){
                \Yii::$app->cache->delete($key);
                $data = LivingUtil::GetGiftsList();
                \Yii::$app->cache->set($key,$data);
            }
        }
        if(!UpdateContentUtil::UpdateGiftVersion($error))
        {
            $rst['msg'] = $error;
            echo json_encode($rst);
            exit;
        }
        $rst['msg'] = $error;
        echo json_encode($rst);
        exit;
        //return $this->controller->redirect(['/gift/index']);
//        return $this->controller->redirect('/gift/index');
    }
}