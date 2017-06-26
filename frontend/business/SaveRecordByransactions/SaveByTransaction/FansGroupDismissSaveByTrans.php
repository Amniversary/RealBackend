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
use common\components\tenxunlivingsdk\TimRestApi;
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

class FansGroupDismissSaveByTrans implements ISaveForTransaction
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



        $group_master = FansGroup::findOne(['group_id'=>$group_id]);

        if(empty($group_master)){
            $error = '群已经不存在咯';
            return false;
        }
        $outInfo['tx_group_id'] = $group_master->tx_group_id;
        if($group_master->group_master_id!=$user_id){
            $error = '您不是群主，没有权限解散群哦';
            return false;
        }

        //修改此人的is_created_group
        $record = ClientFansGroup::findOne(['user_id'=>$user_id]);
        if(!empty($record)){
            $record->is_created_group = 0;
            if(!$record->save()){
                $error = '用户粉丝群表is_created_group修改失败';
                \Yii::getLogger()->log($error.' :'.var_export($record->getErrors(),true),Logger::LEVEL_ERROR);
                return false;
            }
        }
        //从群记录中删除该群
        $sql = 'delete from mb_fans_group where group_id=:group_id';
        $query = \Yii::$app->db->createCommand($sql,[
            ':group_id' => $group_id,
        ])->execute();

        if(!$query){
            $error = '删除失败';
            return false;
        }
        //删除群中所有的成员
        $sql = 'delete from mb_fans_group_member where group_id=:group_id';
        $query = \Yii::$app->db->createCommand($sql,[
            ':group_id' => $group_id,
        ])->execute();

        if(!$query){
            $error = '删除成员失败';
            return false;
        }
        return true;
    }
} 