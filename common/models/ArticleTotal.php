<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%_article_total}}".
 *
 * @property integer $id
 * @property integer $app_id
 * @property string $msg_id
 * @property integer $order_no
 * @property string $title
 * @property integer $target_user
 * @property integer $int_page_read_user
 * @property integer $int_page_read_count
 * @property double $page_read_rate
 * @property integer $ori_page_read_user
 * @property integer $ori_page_read_count
 * @property double $ori_page_read_rate
 * @property integer $share_user
 * @property integer $share_count
 * @property double $share_rate
 * @property integer $add_to_fav_user
 * @property integer $add_to_fav_count
 * @property double $add_to_fav_rate
 * @property integer $int_page_from_session_read_user
 * @property integer $int_page_from_session_read_count
 * @property integer $int_page_from_hist_msg_read_user
 * @property integer $int_page_from_hist_msg_read_count
 * @property integer $int_page_from_feed_read_user
 * @property integer $int_page_from_feed_read_count
 * @property integer $int_page_from_friends_read_user
 * @property integer $int_page_from_friends_read_count
 * @property integer $int_page_from_other_read_user
 * @property integer $int_page_from_other_read_count
 * @property integer $feed_share_from_session_user
 * @property integer $feed_share_from_session_cnt
 * @property integer $feed_share_from_feed_user
 * @property integer $feed_share_from_feed_cnt
 * @property integer $feed_share_from_other_user
 * @property integer $feed_share_from_other_cnt
 * @property string $stat_date
 * @property string $remark1
 * @property string $remark2
 */
class ArticleTotal extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%_article_total}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['app_id', 'stat_date'], 'required'],
            [['app_id', 'order_no', 'target_user', 'int_page_read_user', 'int_page_read_count', 'ori_page_read_user', 'ori_page_read_count', 'share_user', 'share_count', 'add_to_fav_user', 'add_to_fav_count', 'int_page_from_session_read_user', 'int_page_from_session_read_count', 'int_page_from_hist_msg_read_user', 'int_page_from_hist_msg_read_count', 'int_page_from_feed_read_user', 'int_page_from_feed_read_count', 'int_page_from_friends_read_user', 'int_page_from_friends_read_count', 'int_page_from_other_read_user', 'int_page_from_other_read_count', 'feed_share_from_session_user', 'feed_share_from_session_cnt', 'feed_share_from_feed_user', 'feed_share_from_feed_cnt', 'feed_share_from_other_user', 'feed_share_from_other_cnt'], 'integer'],
            [['page_read_rate', 'ori_page_read_rate', 'share_rate', 'add_to_fav_rate'], 'number'],
            [['stat_date'], 'safe'],
            [['msg_id', 'title', 'remark1', 'remark2'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'app_id' => '公众号',
            'msg_id' => '文章 ID',
            'order_no' => '图文顺位',
            'title' => '文章标题',
            'target_user' => '送达人数',
            'int_page_read_user' => '阅读人数',
            'int_page_read_count' => '阅读次数',
            'page_read_rate' => '阅读率',
            'ori_page_read_user' => '阅读原文人数',
            'ori_page_read_count' => '阅读原文次数',
            'ori_page_read_rate' => '原文阅读率',
            'share_user' => '分享人数',
            'share_count' => '分享次数',
            'share_rate' => '分享率',
            'add_to_fav_user' => '收藏人数',
            'add_to_fav_count' => '收藏次数',
            'add_to_fav_rate' => '收藏率',
            'int_page_from_session_read_user' => '公众号会话阅读人数',
            'int_page_from_session_read_count' => '公众号会话阅读次数',
            'int_page_from_hist_msg_read_user' => '历史消息页阅读人数',
            'int_page_from_hist_msg_read_count' => '历史消息页阅读次数',
            'int_page_from_feed_read_user' => '朋友圈阅读人数',
            'int_page_from_feed_read_count' => '朋友圈阅读次数',
            'int_page_from_friends_read_user' => '好友转发阅读人数',
            'int_page_from_friends_read_count' => '好友转发阅读次数',
            'int_page_from_other_read_user' => '其他场景阅读人数',
            'int_page_from_other_read_count' => '其他场景阅读次数',
            'feed_share_from_session_user' => '公众号会话转发朋友圈人数',
            'feed_share_from_session_cnt' => '公众号会话转发朋友圈次数',
            'feed_share_from_feed_user' => '朋友圈转发朋友圈人数',
            'feed_share_from_feed_cnt' => '朋友圈转发朋友圈次数',
            'feed_share_from_other_user' => '其他场景转发朋友圈人数',
            'feed_share_from_other_cnt' => '其他场景转发朋友圈次数',
            'stat_date' => '发送时间',
            'remark1' => '备用字段1',
            'remark2' => '备用字段2',
        ];
    }
}
