<?php
/**
 * 组群管理
 */

namespace frontend\business\RongCloud;

use \common\models\Client;
use \common\models\ClientFansGroup;
use \common\models\FansGroup;
use \common\models\FansApprove;
use \common\models\FansGroupMember;
use \common\models\FansGroupApplyrecord;
use yii\base\Exception;

class GroupUtil
{
    const GROUP_IS_CREATED = 1;

    const MSG_USER_IS_NOT_EXIST = '用户不存在';

    const MSG_SAVE_GROUP_MODEL_UNSUCCESS = '保存粉丝群失败';

    const MSG_GROUP_IS_CREATED = '用户群已经创建';

    const MSG_GROUP_IS_UNDEFINDE = '组群未创建';

    const MGS_USER_IS_UNAPPLY = '用户没有申请加入群';

    const MSG_JOIN_UNSUCCESS = '加入组群失败';

    // 粉丝群名的后缀
    const GROUP_NAME_SUFFIX = '的粉丝群';

    // 群主
    const OWNER_KEY = 2;

    // 管理员
    const MANAGE_KEY = 1;

    // 普通粉丝
    const FAN_KEY = 0;

    // 申请入群 未审核
    const APPLY_STATUS_WAITING = 2;

    // 申请入群 通过
    const APPLY_STATUS_ALLOW = 1;

    // 申请如泉 未通过
    const APPLY_STATUS_DISALLOW = 0;

    /**
     * @var null|\common\components\rongcloudsdk\methods\Group
     */
    private $imGroupManager = null;

    /**
     * todo: 创建新组群
     * mb_client_fans_group 表中的 is_created_group 是判断是否已经创建群的关键,
     * 如果碰到特殊情况只要修改 is_created_group 为 0就可以重新创建群
     * @param int $userId 用户的client_id
     * @param string $groupId 群id，如果不设置则使用用户的client_no，!这个id并不是mb_fans_group的group_id
     * @param string $groupName 群名称，如果不设置则使用用户昵称
     * @param bool $clearGroupMember true: 删掉所有粉丝; false: 同步到融云 !如果数量特别大可能会碰到问题
     * @throws \Exception
     * @return FansGroup
     */
    public function create($userId, $groupId = null, $groupName = null, $clearGroupMember = false)
    {
        /**
         * @var Client $userModel 用户
         */
        $userModel = Client::findOne(['client_id' => $userId]);
        if (!$userModel) {
            throw new \Exception(self::MSG_USER_IS_NOT_EXIST);
        }

        // 判断用户粉丝群表
        $clientGroupModel = ClientFansGroup::findOne(['user_id' => $userId]);
        if (!$clientGroupModel) {
            $clientGroupModel = new ClientFansGroup();
            $clientGroupModel->setAttribute('user_id', $userId);
        }
        if ($clientGroupModel->getAttribute('is_created_group') == self::GROUP_IS_CREATED) {
            throw new \Exception(self::MSG_GROUP_IS_CREATED);
        }

        // 创建融云群
        $groupId = empty($groupId) ? $userModel->getAttribute('client_no') : $groupId;
        $groupName = empty($groupName) ? $userModel->getAttribute('nick_name') . self::GROUP_NAME_SUFFIX : $groupName;
        $groupManager = $this->getImGroupManager();
        if (!$groupManager->create($userId, $groupId, $groupName)) {
            throw new \Exception($groupManager->getErrorMessage());
        }

        // 修改粉丝群表
        $groupModel = FansGroup::findOne(['group_master_id' => $userId]);
        if (!$groupModel) {
            $groupModel = new FansGroup();
            $groupModel->setAttribute('group_master_id', $userId);
        }
        $groupModel->setAttributes([
            'tx_group_id' => $groupId,
            'group_name'  => $groupName,
            'pic' => $userModel->getAttribute('pic'),
        ]);
        $groupModel->save();

        // 修改粉丝群成员表
        $ownerIsExist = false;
        if ($clearGroupMember) {
            // 清空粉丝群列表
            FansGroupMember::deleteAll( [
                'group_id' => $groupModel->getAttribute('group_id')
            ]);
        } else {
            // 同步群成员到融云
            $ownerIsExist = $this->sync($groupModel);
        }
        // 添加拥有者到粉丝群成员表
        if (!$ownerIsExist) {
            $memberModel = new FansGroupMember();
            $memberModel->setAttributes([
                'group_id' => $groupModel->getAttribute('group_id'),
                'user_id'  => $userId,
                'group_member_type' => self::OWNER_KEY,
            ]);
            $memberModel->save();
        }

        // 如果所有都操作成功，则修改用户粉丝群表的状态
        $clientGroupModel->setAttribute('is_created_group', self::GROUP_IS_CREATED);
        $clientGroupModel->save();

        return $groupModel;
    }

    /**
     * todo: 同步粉丝群成员
     * @param FansGroup $groupId 粉丝群模型
     * @throws \Exception
     * @return bool !返回的值并不是说明操作是否成功，而是拥有者时候是否在粉丝群列表中
     */
    public function sync(FansGroup $groupModel)
    {
        $ownerIsExist   = false;
        $fansWithUserId = [];
        $fans = FansGroupMember::findAll([
            'group_id' => $groupModel->group_id
        ]);
        foreach ($fans as $fan) {
            if (!$ownerIsExist && $fan->user_id == $groupModel->group_matser_id) {
                $ownerIsExist = true;
            } else {
                $fansWithUserId[] = $fan->user_id;
            }
        }
        if (!empty($fansWithUserId)) {
            $groupManager = $this->getImGroupManager();
            if (!$groupManager->sync($fansWithUserId, ['id' => $groupModel->group_id])) {
                throw new \Exception($groupManager->getErrorMessage());
            }
        }
        return $ownerIsExist;
    }

    /**
     * todo: 加入群组
     * @param int $userId 用户id
     * @param int $adminId 操作员id
     * @param int $groupId 群id,mb_fans_group的group_id
     * @param int $status 0: 不允许; 1: 允许
     * @param bool $needApply 是否需要先申请再加群
     * @throws \Exception
     */
    public function apply($userId, $adminId, $groupId, $status, $needApply = true)
    {
        /**
         * @var FansGroupApplyrecord $groupMember
         */
        $groupMember = FansGroupApplyrecord::findOne([
            'user_id'  => $userId,
            'group_id' => $groupId,
        ]);
        if ($needApply) {
            if (!$groupMember) {
                throw new \Exception(self::MGS_USER_IS_UNAPPLY);
            }
        } else {
            if (empty($groupMember)) {
                $groupMember = new FansGroupApplyrecord();
                $groupMember->setAttributes([
                    'user_id' => $userId,
                    'group_id' => $groupId,
                    'remark1' => $adminId
                ]);
                $groupMember->setAttribute('apply_status', self::APPLY_STATUS_WAITING);
                $groupMember->setAttribute('apply_time', date('Y-m-d H:i:s'));
            }
        }

        if ($groupMember->getAttribute('apply_status') != self::APPLY_STATUS_WAITING) {
            throw new \Exception(self::MSG_JOIN_UNSUCCESS);
        }

        switch ($status) {
            case self::APPLY_STATUS_ALLOW:
                $groupMember->setAttribute('apply_status', self::APPLY_STATUS_ALLOW);
                $this->join($userId, $groupId);
                break;
            case self::APPLY_STATUS_DISALLOW:
            default:
                $groupMember->setAttribute('apply_status', self::APPLY_STATUS_DISALLOW);
                break;
        }
        $groupMember->setAttribute('remark1', (string)$adminId);
        $groupMember->save();
    }

    /**
     * todo: 加入组群
     * @param $userId
     * @param $groupId
     * @throws \Exception
     */
    public function join($userId, $groupId)
    {
        /**
         * @var FansGroupMember $groupModel
         */
        $groupModel = FansGroup::findOne([
            'group_id' => $groupId,
        ]);
        if (!$groupModel) {
            throw new \Exception(self::MSG_GROUP_IS_UNDEFINDE);
        }
        $groupManager = $this->getImGroupManager();
        if (!$groupManager->join(
            $userId,
            $groupModel->getAttribute('tx_group_id'),
            $groupModel->getAttribute('group_name')
        )) {
            throw new \Exception($groupManager->getErrorMessage());
        }

        // 将用户添加到粉丝群列表
        $memberModel = FansGroupMember::findOne([
            'user_id' => $userId,
            'group_id' => $groupId
        ]);
        if (!$memberModel) {
            $memberModel = new FansGroupMember();
            $memberModel->setAttributes([
                'user_id' => $userId,
                'group_id' => $groupId,
                'group_member_type' => self::FAN_KEY,
            ]);
        }

        $memberModel->save();
    }

    /**
     * todo: 退出群
     * @param $userId
     * @param $groupId
     * @throws \Exception
     * @throws \yii\db\Exception
     */
    public function quit($userId, $groupId)
    {
        /**
         * @var FansGroup $groupModel
         */
        $groupModel = FansGroup::findOne(['group_id' => $groupId]);
        if (empty($groupModel)) {
            throw new Exception(self::MSG_GROUP_IS_UNDEFINDE);
        }

        // 退出融云群组
        $groupManager = $this->getImGroupManager();
        if (!$groupManager->quit($userId, $groupModel->getAttribute('tx_group_id'))) {
            throw new \Exception($groupManager->getErrorMessage());
        }

        $conditions = [
            'user_id'  => $userId,
            'group_id' => $groupId,
        ];
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            // 删除粉丝群成员表
            FansGroupMember::deleteAll($conditions);
            // 删除粉丝群申请审核表
            FansApprove::deleteAll($conditions);
            // 删除粉丝群成员申请记录表
            FansGroupApplyrecord::deleteAll($conditions);
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    /**
     * @return \common\components\rongcloudsdk\methods\Group
     */
    private function getImGroupManager()
    {
        if (empty($this->imGroupManager)) {
            $this->imGroupManager = \Yii::$app->im->Group();
        }
        return $this->imGroupManager;
    }

    /**
     * todo: 测试
     * @param $groupId
     * @return array|false
     */
    public function testGetGroupMember($groupId)
    {
        return $this->getImGroupManager()->queryUser($groupId);
    }
}
