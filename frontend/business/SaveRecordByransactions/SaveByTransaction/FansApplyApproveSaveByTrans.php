<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/4/22
 * Time: 16:50
 */

namespace frontend\business\SaveRecordByransactions\SaveByTransaction;


use common\components\DbUtil;
use common\components\Des3Crypt;
use common\components\UsualFunForStringHelper;
use common\models\Balance;
use common\models\Client;
use common\models\ClientActive;
use common\models\ClientFansGroup;
use common\models\ClientOther;
use common\models\ClientQiniu;
use common\models\FansGroup;
use common\models\FansGroupApplyrecord;
use common\models\FansGroupMember;
use frontend\business\SaveRecordByransactions\ISaveForTransaction;
use yii\base\Exception;
use yii\log\Logger;

class FansApplyApproveSaveByTrans implements ISaveForTransaction
{

    private $getRecord = null;
    private $extend_params=[];

    /**
     * 注册信息保存
     * @param $record
     * @param array $extend_params
     */
    public function __construct($record,$extend_params=[])
    {
        $this->getRecord = $record;
        $this->extend_params = $extend_params;
    }


    function SaveRecordForTransaction(&$error,&$outInfo)
    {
        $group_id = $this->getRecord['group_id'];
        $user_id = $this->getRecord['user_id'];
        $apply_status = $this->getRecord['apply_status'];
        //修改记录的申请状态
        $record = FansGroupApplyrecord::findOne(['group_id'=> $group_id, 'user_id'=> $user_id,'apply_status'=>2]);
        $record_user = FansGroupMember::findOne(['group_id'=> $group_id, 'user_id'=> $user_id]);
        if(!isset($record)){
            $error = '找不到申请记录';
            return false;
        }
        if(!empty($record_user)){
            $error = '已经添加过了';
            return false;
        }
        $record->apply_status = $apply_status;

        if(!$record->save()){
            $error = '申请记录表审核状态修改失败';
            \Yii::getLogger()->log($error.' :'.var_export($record->getErrors(),true),Logger::LEVEL_ERROR);
            return false;
        }
        //成员表添加一条记录
        $data = [
            'user_id'=>$user_id,
            'group_id'=>$group_id,
            'group_member_type'=>0,
        ];

        $member = new FansGroupMember();
        $member->attributes = $data;

        if(!$member->save()){
            $error = '群成员表插入成员失败';
            \Yii::getLogger()->log($error.' :'.var_export($member->getErrors(),true),Logger::LEVEL_ERROR);
            return false;
        }
        return true;
    }
} 