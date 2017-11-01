<?php
/**
 * 直播监控分流
 */
namespace backend\business;

use yii\db\Query;

class LivingControl
{
    // user_id => 权重
    const KEYNAME_USER_WEIGHT = 'living_control_fls_user_weight';

    // live_id => 被谁监控中
    const KEYNAME_LIVE_INDEX = 'living_control_fls_live_index';

    // living_control_rel_{user_id} => [直播列表:[live_id => 参数]]
    const KEYNAME_REL_USER_LIVE = 'living_control_rel_';

    const CACHE_LOCK_KEYNAME = 'living_control_lock_15220';

    /**
     * @var \yii\caching\Cache $cache
     */
    private $cache;

    private $weightFlag = 1;

    private $userId;

    private $userLives;

    private $userWeight;

    private $liveIndex;

    public function __construct()
    {
        $this->cache = \Yii::$app->cache;
        // 加锁，防止并发
        $lock = 3;
        while ($lock > 0) {
            if ($this->lock()) {
                break;
            }
            $lock--;
            sleep(0.3);
            if ($lock == 0) {
                throw new \Exception('cache is locked');
            }
        }
        $this->userWeight = $this->getArrayFromCache(self::KEYNAME_USER_WEIGHT);
        $this->liveIndex = $this->getArrayFromCache(self::KEYNAME_LIVE_INDEX);
    }

    public function __destruct()
    {
        $this->unlock();
    }

    /**
     * 增加监控用户
     * @param $users
     * @return $this
     */
    public function addUsers($users)
    {
        if (!empty($users) && is_array($users)) {
            foreach ($users as $userId) {
                if (!array_key_exists($userId, $this->userWeight)) {
                    $this->userWeight[$userId] = 0;
                }
            }
            $this->save(1);
        }
        return array_keys($this->userWeight);
    }

    /**
     * 移除监控用户
     * @param $users
     */
    public function removeUsers($users)
    {
        if (!empty($users) && is_array($users)) {
            foreach ($users as $userId) {
                unset($this->userWeight[$userId]);
                foreach ($this->liveIndex as $liveId => $liveUserId) {
                    if ($userId == $liveUserId) {
                        unset($this->liveIndex[$liveId]);
                    }
                }
                $this->cache->delete(self::KEYNAME_REL_USER_LIVE . $this->userId);
            }
            $this->save(3);
        }
        return array_keys($this->userWeight);
    }

    /**
     * 重置
     */
    public function clear()
    {
        foreach ($this->userWeight as $userId => $weight) {
            $this->cache->delete(self::KEYNAME_REL_USER_LIVE . $this->userId);
        }
        $this->cache->delete(self::KEYNAME_USER_WEIGHT);
        $this->cache->delete(self::KEYNAME_LIVE_INDEX);
        $this->cache->delete(self::CACHE_LOCK_KEYNAME);

        $this->userWeight = $this->getArrayFromCache(self::KEYNAME_USER_WEIGHT);
        $this->liveIndex = $this->getArrayFromCache(self::KEYNAME_LIVE_INDEX);
    }

    /**
     * 设置当前用户
     * @param $userId
     * @return $this
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
        $key = self::KEYNAME_REL_USER_LIVE . $userId;
        $this->userLives = $this->getArrayFromCache($key);
        return $this;
    }

    public function getUserRel($userId = false)
    {
        if ($userId !== false) {
            $this->setUserId($userId);
        }
        $userId = $this->userId;
        $lives = $this->getLives();

        // 如果用户不在监控人员列表中，则直接返回所有
        if (!array_key_exists($userId, $this->userWeight)) {
            return $lives;
        }

        list($addLives, $delLives) = $this->compareLives($lives);

        // 删除停掉直播的监控
        foreach ($delLives as $liveId => $row) {
            unset($this->liveIndex[$liveId]);
            unset($this->userLives[$liveId]);
            $this->userWeight[$userId] -= $this->weightFlag;
        }

        // 增加新的直播的监控
        foreach ($addLives as $liveId => $row) {
            $this->liveIndex[$liveId] = $userId;
            $this->userLives[$liveId] = $row;
            $this->userWeight[$userId] += $this->weightFlag;
        }

        // 如果直播列表没有变化，则直接返回缓存
        if (!empty($addLives) || !empty($delLives)) {
            $this->save();
        }

        return $this->userLives;
    }

    /**
     * memcache 简易锁
     * @throws \Exception
     */
    private function lock()
    {
        $lock = $this->cache->get(self::CACHE_LOCK_KEYNAME);
        if ($lock !== false) {
            return false;
        }
        // 这种方法还是有可能导致并发
        $this->cache->set(self::CACHE_LOCK_KEYNAME, 1);
        return true;
    }

    private function unlock()
    {
        $this->cache->delete(self::CACHE_LOCK_KEYNAME);
    }

    private function save($f = 7)
    {
        if (($f & 1) == 1) {
            $this->cache->set(self::KEYNAME_USER_WEIGHT, json_encode($this->userWeight));
        }

        if (($f & 2) == 2) {
            $this->cache->set(self::KEYNAME_LIVE_INDEX, json_encode($this->liveIndex));
        }

        if (($f & 4) == 4) {
            $this->cache->set(self::KEYNAME_REL_USER_LIVE . $this->userId, json_encode($this->userLives));
        }
    }

    /**
     * 比较缓存中保存的直播列表和数据库中的直播列表
     * @param $lives
     * @return array
     */
    private function compareLives($dataLives)
    {
        $cachedUserLives = $this->userLives;
        $cachedLives = $this->liveIndex;
        $del = $cachedUserLives;
        $add = [];
        foreach ($dataLives as $row) {
            $liveId = $row['living_id'];
            if (array_key_exists($liveId, $del)) {
                unset($del[$liveId]);
            }
            if (!array_key_exists($liveId, $cachedLives)) {
                $add[$liveId] = $row;
            }
        }

        $weight = $this->userWeight;
        $length = ceil((array_sum($weight) + count($add)) / count($weight) - $weight[$this->userId]);
        $add = array_slice($add, 0, $length, true);
        return [$add, $del];
    }

    /**
     * 获取memcache缓存，如果不存在，则赋值为空数组
     * @param $key
     * @return array|mixed
     */
    private function getArrayFromCache($key)
    {
        $result = $this->cache->get($key);
        return ($result === false) ? [] : json_decode($result, true);
    }

    /**
     * 获取正在直播列表
     * @return array
     */
    private function getLives()
    {
        $query = new Query();
        $query->select('client_id, client_no, nick_name, living_id, pull_rtmp_url')
            ->from('mb_living')
            ->innerJoin('mb_client', 'mb_client.client_id = mb_living.living_master_id')
            ->where(['mb_living.status' => 2, 'living_type' => [1, 2, 3, 4]]);
        return $query->all();
    }
}