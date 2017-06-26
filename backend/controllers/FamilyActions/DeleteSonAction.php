<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/23
 * Time: 16:48
 */

namespace backend\controllers\FamilyActions;


use backend\business\FamilyMemberUtil;
use common\models\Family;
use yii\base\Action;
use yii\log\Logger;

class DeleteSonAction extends Action
{
    public function run($record_id)
    {
        $rst=['code'=>'0','msg'=>''];
        if(empty($record_id))
        {
            $rst['msg']='成员id不能为空';
            echo json_encode($rst);
            exit;
        }
        $FamilyMember = FamilyMemberUtil::GetFamilyMemberById($record_id);
        if(!isset($FamilyMember))
        {
            $rst['msg']='成员信息不存在';
            echo json_encode($rst);
            exit;
        }
        $family = Family::GetFamilyById($FamilyMember->family_id);
        if(!isset($family))
        {
            $rst['msg']='家族信息不存在';
            echo json_encode($rst);
            exit;
        }
        if(FamilyMemberUtil::DeleteSaveRansactions($FamilyMember))
        {
            $rst['msg']='删除失败';
            \Yii::getLogger()->log('删除失败:'.var_export($FamilyMember->getErrors(),true),Logger::LEVEL_ERROR);
            echo json_encode($rst);
            exit;
        }

        echo json_encode($rst);
        exit;
    }
} 