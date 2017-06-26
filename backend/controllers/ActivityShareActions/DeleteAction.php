<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/28
 * Time: 16:40
 */

namespace backend\controllers\ActivityShareActions;


use frontend\business\ShareUtil;
use yii\base\Action;
use yii\log\Logger;

class DeleteAction extends Action
{
    public function run($share_id)
    {
        $rst=['code'=>'1','msg'=>''];
        if(empty($share_id))
        {
            $rst['msg']='分享记录id不能为空';
            echo json_encode($rst);
            exit;
        }
        $share = ShareUtil::GetShareInfo(['share_id'=>$share_id]);
        if(!isset($share))
        {
            $rst['msg']='分享信息不存在';
            echo json_encode($rst);
            exit;
        }

        if($share->delete() === false)
        {
            $rst['msg']='删除失败';
            \Yii::getLogger()->log('删除失败:'.var_export($share->getErrors(),true),Logger::LEVEL_ERROR);
            echo json_encode($rst);
            exit;
        }
        $rst['code'] = '0';
        echo json_encode($rst);
        exit;
    }
} 