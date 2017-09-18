<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/9/13
 * Time: 上午11:53
 */

namespace frontend\business;


use common\models\Collect;
use common\models\StudyingDynamic;
use common\models\Voice;
use yii\db\Query;

class DynamicUtil
{
    /**
     * 获取组装动态模型
     * @param $data
     * @return StudyingDynamic
     */
    public static function getDynamicModel($data)
    {
        $model = new StudyingDynamic();
        $model->attributes = $data;
        return $model;
    }

    /**
     * 根据动态id 获取记录
     * @param $dynamic_id
     * @return null|StudyingDynamic
     */
    public static function getDynamicById($dynamic_id)
    {
        return StudyingDynamic::findOne(['dynamic_id'=> $dynamic_id]);
    }

    /**
     * 获取动态音频
     * @param $dynamic_id
     * @return null|Voice
     */
    public static function getVoiceById($dynamic_id)
    {
        return Voice::findOne(['dynamic_id'=>$dynamic_id]);
    }

    /**
     * 根据类型获取动态列表
     * @param $type
     * @param $page_no
     * @param $page_size
     * @return array
     */
    public static function getDynamicList($user_id, $type , $page_no, $page_size)
    {
        $query = (new Query())
            ->select(['sd.dynamic_id', 'title', 'pic', 'count', 'comment_count', 'create_at', 'if(ifnull(user_id, null) is null,0,1) as is_collect'])
            ->from('wc_studying_dynamic sd')
            ->leftJoin('wc_collect c', 'sd.dynamic_id = c.dynamic_id and user_id = :ud', [':ud' => $user_id])
            ->where(['type'=> $type])
            ->orderBy('create_at asc')
            ->offset(($page_no -1) * $page_size)
            ->limit($page_size)
            ->all();

        return $query;
    }

    /**
     * 根据动态获取评论列表
     * @param $dynamic_id
     * @param int $parent_id
     * @param int $page_no
     * @param int $page_size
     * @return array
     */
    public static function getCommentList($dynamic_id,  $parent_id = 0, $page_no = 1, $page_size = 10)
    {
        $query = (new Query())
            ->select(['id as comment_id', 'content', 'if(ifnull(user_id,null) is null,\'匿名\',nick_name) as nick_name',
            'if(ifnull(pic,null) is null,\'http://7xld1x.com1.z0.glb.clouddn.com/FofN3Z5ZK1V2XbKwv4WwLQIi5KfS\', pic) as pic', 'create_at'])
            ->from('wc_comments c')
            ->leftJoin('wc_book_client wc', 'c.user_id = wc.client_id')
            ->where(['dynamic_id'=> $dynamic_id, 'parent_id' => $parent_id])
            ->orderBy('create_at asc')
            ->offset(($page_no -1) * $page_size)
            ->limit($page_size)
            ->all();
        $rst = [];
        foreach($query as $item) {
            $id = $item['comment_id'];
            $rst[$id] = [
                'comment_id'=>$item['comment_id'],
                'nick_name'=> $item['nick_name'],
                'pic'=>$item['pic'],
                'content'=>$item['content'],
                'create_at'=>$item['create_at'],
            ];
            $rst[$id]['comments'] = self::getCommentList($dynamic_id, $item['comment_id']);
        }
        return $rst;
    }

    /**
     * 获取用户收藏列表
     * @param $user_id
     * @return array
     */
    public static function getCollectList($user_id)
    {
        $sql = 'select dynamic_id from wc_collect where user_id = :ud';
        $condition = 'dynamic_id in ('. $sql .')';
        $query = (new Query())
            ->select(['dynamic_id' , 'title','pic','count','comment_count','create_at'])
            ->from('wc_studying_dynamic')
            ->where($condition,[':ud'=> $user_id])
            ->orderBy('create_at desc')
            ->all();

        return $query;
    }

    /**
     * 获取收藏信息
     * @param $user_id
     * @param $dynamic_id
     * @return null|Collect
     */
    public static function getCollect($user_id, $dynamic_id)
    {
        return Collect::findOne(['user_id'=> $user_id, 'dynamic_id'=> $dynamic_id]);
    }
}