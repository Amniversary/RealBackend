<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/29
 * Time: 17:19
 */

namespace backend\controllers\CommonWordsActions;


use common\models\CommonWords;
use yii\base\Action;
use yii\log\Logger;

class DeleteAction extends Action
{
    public function run($cid)
    {
        $rst=['code'=>'1','msg'=>''];
        if(empty($cid))
        {
            $rst['msg']='记录不能为空';
            echo json_encode($rst);
            exit;
        }
        $CommonWords = CommonWords::find()->where(['cid'=>$cid])->one();
        if(!isset($CommonWords))
        {
            $rst['msg']='该常用语不存在';
            echo json_encode($rst);
            exit;
        }


        if($CommonWords->delete() === false)
        {
            $rst['msg']='删除失败';
            \Yii::getLogger()->log('删除失败:'.var_export($CommonWords->getErrors(),true),Logger::LEVEL_ERROR);
            echo json_encode($rst);
            exit;
        }

        $sql = 'select user_id from mb_common_words where user_id > 1 GROUP BY user_id ';
        $rst = \Yii::$app->db->createCommand($sql)->queryAll();
        foreach($rst as $v)
        {
            \Yii::$app->cache->delete('set_admin_warning_'.$v['user_id']);
        }

        $rst['code'] = '0';
        echo json_encode($rst);
        exit;
    }
}