<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/4/25
 * Time: 17:26
 */

namespace frontend\business;


use common\components\DbUtil;
use common\components\tenxunlivingsdk\TimRestApi;
use common\models\AlipayForCash;
use common\models\Attention;
use common\models\BlackList;
use common\models\Client;
use common\models\ClientFansGroup;
use common\models\ClientOther;
use common\models\FansGroup;
use common\models\FansGroupApplyrecord;
use common\models\FansGroupMember;
use frontend\business\SaveRecordByransactions\SaveByTransaction\FansApplyApproveSaveByTrans;
use frontend\business\SaveRecordByransactions\SaveByTransaction\FansGroupDismissSaveByTrans;
use frontend\business\SaveRecordByransactions\SaveByTransaction\FansGroupSaveByTrans;
use frontend\business\SaveRecordByransactions\SaveByTransaction\QiNiuRegisterSaveForReward;
use yii\db\Query;
use yii\log\Logger;

class FansGroupUtil {
    /**
     * 根据user_id 创建用户自己的粉丝群
     * @param $user_id  用户id
     */
    public static function CreateFansGroup($user_id, &$out_group, &$error)
    {
        $query = (new Query)
            ->select(['is_created_group'])
            ->from('mb_client_fans_group')
            ->where('user_id = :uid', [':uid' => $user_id])
            ->one();
        if($query['is_created_group'] == 1)
        {
            //var_dump('created');
            $error = '您已经建过群啦，最多只能创建一个粉丝群';
            return false;
        }
        //建粉丝群
        $transAction = new FansGroupSaveByTrans($user_id);
        if(!$transAction->SaveRecordForTransaction($error, $group_info))
        {
            return false;
        }

        //\Yii::getLogger()->log('$group_id:'.$group_id, Logger::LEVEL_ERROR);
        $out_group = $group_info;
        return true;
    }

    /**
     * 根据群id 获取群成员
     * @param $group_id  粉丝群id
     * @param $page  当前页
     * @param $page_size  每页多少条
     */
    public static function GetGroupMemberList($group_id, $user_id, $page, $page_size)
    {

        if(is_null($page_size))
        {
            //默认每页30条
            $page_size = 30;
        }
        //var_dump($user_id);
        $query = (new Query)
            ->select(['group_id', 'group_member_type', 'c.nick_name', 'c.pic','c.sex', 'c.sign_name', 'client_id as user_id', 'a.record_id'])
            ->from('mb_fans_group_member fgb')
            ->innerJoin('mb_client c', 'c.client_id=fgb.user_id')
            ->leftJoin('mb_attention a', 'c.client_id = a.user_id and a.friend_user_id='.$user_id)
            ->where('group_id = :gid', [':gid' => $group_id])
            ->orderBy('group_member_type desc')
            ->offset(($page-1) * $page_size)
            ->limit($page_size)
            ->all();
        foreach($query as $list)
        {
            $s = array_search($list,$query);
            $list['is_attention'] = 1;
            if(empty($list['record_id']))
            {
                $list['is_attention'] = 0;
            }
            $query[$s] = $list;
        }
        return $query;

    }
    /**
     * 根据群id获取粉丝群信息
     * @param $group_id  群id
     */
    public static function GetFansGroupInfoByGroupID($group_id, $tx_group_id, $user_id, &$error)
    {
        if(!empty($group_id))
        {
            $count = (new Query)
                ->select(['count(user_id) as number'])
                ->from('mb_fans_group_member')
                ->where('group_id = :gid', [':gid' => $group_id])
                ->one();
            $count = $count['number'];
            $member_type = (new Query)
                ->select(['group_member_type'])
                ->from('mb_fans_group_member')
                ->where('group_id = :gid and user_id = :uid', [':gid' => $group_id, ':uid' => $user_id])
                ->one();
            $query = (new Query)
                ->select(['group_id', 'group_name', 'pic', 'advance_notice', 'tx_group_id'])
                ->from('mb_fans_group')
                ->where('group_id = :gid', [':gid' => $group_id])
                ->one();
        }
        else if(!empty($tx_group_id))
        {
            $query = (new Query)
                ->select(['group_id', 'group_name', 'pic', 'advance_notice', 'tx_group_id'])
                ->from('mb_fans_group')
                ->where('tx_group_id = :gid', [':gid' => $tx_group_id])
                ->one();
            $group_ids = $query['group_id'];
            $count = (new Query)
                ->select(['count(user_id) as number'])
                ->from('mb_fans_group_member')
                ->where('group_id = :gid', [':gid' => $group_ids])
                ->one();

            $count = $count['number'];
            $member_type = (new Query)
                ->select(['group_member_type'])
                ->from('mb_fans_group_member')
                ->where('group_id = :gid and user_id = :uid', [':gid' => $group_ids, ':uid' => $user_id])
                ->one();
        }
        else
        {
            $error = '没有群ID和腾讯群ID';
        }
        $query['member_number'] = $count;
        if(isset($member_type)){
            $query['group_member_type'] = $member_type['group_member_type'];
        }

        if(empty($query)){
            $error = '群信息获取失败';
            \Yii::getLogger()->log($error.' :'.var_export($query->getErrors(),true),Logger::LEVEL_ERROR);
            return false;
        }

        return $query;
    }
    /**
     * 修改粉丝群信息
     * @param $group_id  群id
     * @param $group_name  群名称
     * @param $pic  群头像
     * @param $advance_notice  直播预告
     */
    public static function updateFansGroupInfo($data, &$error)
    {
        $group_id = $data['data']['group_id'];
        $group = FansGroup::findOne(['group_id'=>$group_id]);

        $unique_no = $data['data']['unique_no'];
        if(!ApiCommon::GetLoginInfo($unique_no, $sysLoginInfo, $error))
        {
            return false;
        }
        //获取请求人的user_id
        $user_id = $sysLoginInfo['user_id'];
        //获取群主的id
        $group_master_id = $group->group_master_id;

        //如果请求人不是群主,则没有权限修改
        if($user_id!=$group_master_id)
        {
            $error = '您不是群主，修改群信息失败';
            \Yii::getLogger()->log($error.' :'.var_export($group->getErrors(),true),Logger::LEVEL_ERROR);
            return false;
        }

        $group_name = $data['data']['group_name'];
        if(!empty($group_name))
        {
            $group['group_name'] = $group_name;
        }
        $pic = $data['data']['pic'];
        if(!empty($pic))
        {
            $group['pic'] = $pic;
        }
        $advance_notice = $data['data']['advance_notice'];
        if(!empty($advance_notice))
        {
            $group['advance_notice'] = $advance_notice;
        }
        if(!$group->save()){
            $error = '群信息修改失败';
            \Yii::getLogger()->log($error.' :'.var_export($group->getErrors(),true),Logger::LEVEL_ERROR);
            return false;
        }
        return true;
    }
    /**
     * 申请入群
     * @param $group_id  群id
     * @param $user_id  用户id
     */
    public static function FansGroupApply($group_id, $user_id, &$error)
    {
        $user = FansGroupMember::findOne(['user_id'=>$user_id, 'group_id'=>$group_id]);
        if(!empty($user))
        {
            $error = '您已经是群成员了';
            return false;
        }
        //查看该成员是否已经申请过
        $user = FansGroupApplyrecord::findOne(['user_id'=>$user_id, 'group_id'=>$group_id, 'apply_status'=>2]);
        if(!empty($user)){
            $error = '您已经申请过了';
            return false;
        }
        //群成员插入一条数据
        $data = [
            'user_id'=>$user_id,
            'group_id'=>$group_id,
        ];

        $record = new FansGroupApplyrecord();
        $record->attributes = $data;
        $record['apply_time'] = date('Y-m-d h:i:s', time());
        if(!$record->save()){
            $error = '申请入群失败';
            \Yii::getLogger()->log($error.' :'.var_export($record->getErrors(),true),Logger::LEVEL_ERROR);
            return false;
        }

        $approveArr = (new Query())
            ->select(['mm.user_id as approve_id','ma.record_id'])
            ->from('mb_fans_group_member mm')
            ->innerJoin('mb_fans_group_applyrecord ma','mm.group_id = ma.group_id')
            ->where('mm.group_id=:gid and mm.group_member_type > 0 and ma.apply_status = 2 and ma.user_id = :uid',[':gid' => $group_id,':uid'=>$user_id])
            ->all();

        /**
         * 向申请审核表里面插入信息
         */
        $sql = 'insert into mb_fans_approve (user_id,group_id,approve_id,apply_id) values';

        $params = [];
        $i = 1;
        $max = count($approveArr);

        foreach($approveArr as $v)
        {
            $params[':uid'.$i] = $user_id;
            $params[':gid'.$i] = $group_id;
            $params[':aid'.$i] = $v['approve_id'];
            $params[':apid'.$i] = $v['record_id'];
            $sql .= sprintf('(:uid%d,:gid%d,:aid%d,:apid%d)',$i,$i,$i,$i);
            if($i === $max)
            {
                $sql .= ';';
            }
            else
            {
                $sql .= ',';
            }
            $i++;
        }

        \Yii::$app->db->createCommand($sql,$params)->execute();

        return true;
    }
    /**
     * 根据用户获取群列表
     * @param $user_id  用户id
     */
    public static function GetFansGroupListByUserID($user_id, &$error)
    {
        $query = (new Query)
            ->select(['fgb.group_id', 'group_name', 'pic', 'advance_notice', 'tx_group_id'])
            ->from('mb_fans_group_member fgb')
            ->innerJoin('mb_fans_group fg', 'fg.group_id=fgb.group_id')
            ->where('user_id = :gid', [':gid' => $user_id])
            ->all();
        return $query;
    }
    /**
     * 根据群id获取成员申请列表
     * @param $group_id  群id
     */
    public static function GetFansApplyList($user_id, &$error)
    {
        $query = (new Query())
            ->select(['a.group_id', 'a.user_id', 'c.nick_name', 'c.pic', 'a.apply_time', 'g.tx_group_id', 'g.pic as group_pic', 'g.group_name'])
            ->from('mb_fans_approve m')
            ->innerJoin('mb_fans_group g', 'm.group_id = g.group_id')
            ->innerJoin('mb_client c', 'c.client_id = m.user_id')
            ->innerJoin('mb_fans_group_applyrecord a','a.apply_status = 2 and m.apply_id = a.record_id')
            ->where('m.approve_id = :aid', [':aid'=>$user_id])
            ->orderBy('a.apply_time desc')
            ->all();

        return $query;
    }
    /**
     * 根据群id获取成员申请列表
     * @param $group_id  群id
     */
    public static function GroupMemberPrivilege($data, &$error)
    {
        $group_id = $data['data']['group_id'];
        $user_id = $data['data']['user_id'];
        if (!is_array($user_id)) {
            $error = 'user_id参数类型错误，必须是array';
            return false;
        }
        $group_member_type = $data['data']['group_member_type'];
        $user_id = implode(',',$user_id);


        $sql = 'update mb_fans_group_member set group_member_type =:group_member_type where group_id=:group_id and user_id in ('.$user_id.')';
        $query = \Yii::$app->db->createCommand($sql,[
            ':group_member_type'=> $group_member_type,
            ':group_id' => $group_id,
        ])->execute();

        if(!$query){
            if($group_member_type==1){
                $error = '添加管理员失败';
            }else{
                $error = '取消管理员失败';
            }
            //\Yii::getLogger()->log($error.' :'.var_export($query->getErrors(),true),Logger::LEVEL_ERROR);
            return false;
        }

        return true;
    }
    /**
     * 踢人
     * @param $group_id  群id
     * @param $user_id  用户id
     */
    public static function KickingFans($group_id, $user_id, &$error)
    {
        if (!is_array($user_id)) {
            $error = 'user_id参数类型错误，必须是array';
            return false;
        }
        $user_id = implode(',',$user_id);
        //var_dump($user_id);
        $sql = 'delete from mb_fans_group_member where group_id=:group_id and find_in_set(user_id, :user_id)';
        $query = \Yii::$app->db->createCommand($sql,[
            ':group_id' => $group_id,
            ':user_id' => $user_id
        ])->execute();

        $sqls = 'delete from mb_fans_group_applyrecord where group_id=:group_id and user_id=:user_id';
        $querys = \Yii::$app->db->createCommand($sqls,[
            ':group_id' => $group_id,
            ':user_id' => $user_id
        ])->execute();

        $sqlss = 'delete from mb_fans_approve where group_id=:group_id and user_id=:user_id';
        $queryss = \Yii::$app->db->createCommand($sqlss,[
            ':group_id' => $group_id,
            ':user_id' => $user_id
        ])->execute();

        //腾讯云踢人
        $tx_group = FansGroup::findOne(['group_id'=>$group_id]);
        if(!TimRestApi::group_delete_group_member($tx_group->tx_group_id, $user_id, '1', $error)){
            return false;
        }

        if(!$query){
            $error = '粉丝群人员删除失败';
            return false;
        }
        if(!$querys){
            $error = '粉丝群人员申请记录删除失败';
            return false;
        }
        if(!$queryss){
            $error = '粉丝群人员申请审核删除失败';
            return false;
        }
        return true;
    }
    /**
     * 申请审核
     * @param $group_id  群id
     * @param $apply_status  群主审核的结果 0拒绝入群 1同意入群
     */
    public static function FansApplyApprove($group_id, $user_id, $apply_status, &$error)
    {

        $data = [
            'group_id' => $group_id,
            'user_id' => $user_id,
            'apply_status' =>$apply_status
        ];
        //\Yii::getLogger()->log($error.' zff-zpprove22:'.var_export($data,true),Logger::LEVEL_ERROR);
        //添加成员
        $transAction = new FansApplyApproveSaveByTrans($data);
        if(!$transAction->SaveRecordForTransaction($error, $info))
        {
            $error = '群添加成员失败';
            return false;
        }
        //腾讯群添加成员
        $tx_group = FansGroup::findOne(['group_id'=>$group_id]);
        if(!TimRestApi::group_add_group_member($tx_group->tx_group_id, $user_id, '1', $error)){
            $error = '腾讯群添加成员失败';
            return false;
        }
        return true;
    }
    /**
     * 解散群
     * @param $group_id  群id
     */
    public static function FansGroupDismiss($group_id, $user_id, &$error)
    {

        $data = [
            'group_id' => $group_id,
            'user_id' => $user_id
        ];
        //添加成员
        $transAction = new FansGroupDismissSaveByTrans($data);
        if(!$transAction->SaveRecordForTransaction($error, $info))
        {
            return false;
        }

        if(!TimRestApi::group_destroy_group($info['tx_group_id'], $error)){
            return false;
        }
        return true;
    }


    /**
     * 判断用户是否加入群
     * @param $group_id
     * @param $my_user_id
     * @param $user_id
     * @return int
     */
    public static function IsJoinFansGroup($group_id,$my_user_id,$user_id)
    {
        if($my_user_id == $user_id)
        {
            return 1;
        }
        $query = FansGroupMember::find()->where(['group_id'=>$group_id, 'user_id'=>$my_user_id])->one();
        if(empty($query))
        {
            return 0;
        }

        return 1;
    }

    /**
     * 通讯录获取粉丝群列表
     * @param $user_id
     * @return array
     */
    public static function GetContactsFansGroup($user_id)
    {
        $query =(new Query())
            ->select(['tx_group_id','cl.client_id as user_id','group_name as nick_name','fg.pic','cl.client_no','advance_notice as sign_name'])
            ->from('mb_fans_group_member fgm')
            ->innerJoin('mb_fans_group fg','fg.group_id=fgm.group_id')
            ->innerJoin('mb_client cl','cl.client_id=fg.group_master_id')
            ->where('group_member_type!=2 and fgm.user_id=:uid',[':uid' => $user_id])
            ->all();
        return $query;
    }
} 