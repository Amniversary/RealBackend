<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/8/26
 * Time: 下午5:50
 */

namespace backend\business\SaveRecordByTransactions\SaveByTransaction;


use backend\business\SaveRecordByTransactions\ISaveForTransaction;
use common\models\ArticleTotal;

class SaveArticleTotalByTrans implements ISaveForTransaction
{
    public $total;
    public $extend;

    public function __construct($data, $extend)
    {
        $this->total = $data;
        $this->extend = $extend;
    }

    function SaveRecordForTransaction(&$error, &$outInfo)
    {
        $Total = ArticleTotal::findOne(['msg_id' => $this->total['msgid']]);
        $db = \Yii::$app->db;
        $count = count($this->total['details']);
        $data = $this->total['details'][$count - 1];
        if (empty($Total) || !isset($Total)) {
            $inset = 'insert ignore into wc_article_total(app_id, msg_id, order_no, title, target_user,
int_page_read_user, int_page_read_count, page_read_rate, ori_page_read_user, ori_page_read_count,
ori_page_read_rate, share_user, share_count, share_rate, add_to_fav_user, add_to_fav_count, add_to_fav_rate,
int_page_from_session_read_user, int_page_from_session_read_count, int_page_from_hist_msg_read_user,
int_page_from_hist_msg_read_count, int_page_from_feed_read_user, int_page_from_feed_read_count,
int_page_from_friends_read_user, int_page_from_friends_read_count, int_page_from_other_read_user,
int_page_from_other_read_count, feed_share_from_session_user, feed_share_from_session_cnt,
feed_share_from_feed_user, feed_share_from_feed_cnt, feed_share_from_other_user, feed_share_from_other_cnt,
stat_date) VALUES (:app_id, :msg_id, :order_no, :title, :target, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0,
0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, :date)';
            $db->createCommand($inset, [
                ':app_id' => $this->extend['app_id'],
                ':msg_id' => $this->total['msgid'],
                ':target' => $data['target_user'],
                ':order_no' => explode('_', $this->total['msgid'])[1],
                ':title' => $this->total['title'],
                ':date' => $this->total['ref_date']
            ])->execute();
        }

        $update = 'update wc_article_total set
              int_page_read_user = :int_page_read_user, int_page_read_count= :int_page_read_count,
              page_read_rate     = :page_read_rate,     ori_page_read_user = :ori_page_read_user,
              ori_page_read_count= :ori_page_read_count,ori_page_read_rate = :ori_page_read_rate,
              share_user         = :share_user,         share_count        = :share_count,
              share_rate         = :share_rate,         add_to_fav_user    = :add_to_fav_user,
              add_to_fav_count   = :add_to_fav_count,   add_to_fav_rate    = :add_to_fav_rate,
              int_page_from_session_read_user   = :int_page_from_session_read_user,
              int_page_from_session_read_count  = :int_page_from_session_read_count,
              int_page_from_hist_msg_read_user  = :int_page_from_hist_msg_read_user,
              int_page_from_hist_msg_read_count = :int_page_from_hist_msg_read_count,
              int_page_from_feed_read_user      = :int_page_from_feed_read_user,
              int_page_from_feed_read_count     = :int_page_from_feed_read_count,
              int_page_from_friends_read_user   = :int_page_from_friends_read_user,
              int_page_from_friends_read_count  = :int_page_from_friends_read_count,
              int_page_from_other_read_user     = :int_page_from_other_read_user,
              int_page_from_other_read_count    = :int_page_from_other_read_count,
              feed_share_from_session_user      = :feed_share_from_session_user,
              feed_share_from_session_cnt       = :feed_share_from_session_cnt,
              feed_share_from_feed_user         = :feed_share_from_feed_user,
              feed_share_from_feed_cnt          = :feed_share_from_feed_cnt,
              feed_share_from_other_user        = :feed_share_from_other_user,
              feed_share_from_other_cnt         = :feed_share_from_other_cnt
              where app_id = :app_id and msg_id = :msg_id';

        $db->createCommand($update, [
            ':app_id' => $this->extend['app_id'],
            ':msg_id' => $this->total['msgid'],
            ':int_page_read_user' => $data['int_page_read_user'],
            ':int_page_read_count' => $data['int_page_read_count'],
            ':page_read_rate' => !empty($data['target_user']) ? sprintf('%.4f', round($data['int_page_read_user'] / $data['target_user'], 4)) * 100 : 0,
            ':ori_page_read_user' => $data['ori_page_read_user'],
            ':ori_page_read_count' => $data['ori_page_read_count'],
            ':ori_page_read_rate' => !empty($data['int_page_read_user']) ? sprintf('%.4f', round($data['ori_page_read_user'] / $data['int_page_read_user'], 4)) * 100 : 0,
            ':share_user' => $data['share_user'],
            ':share_count' => $data['share_count'],
            ':share_rate' => !empty($data['int_page_read_user']) ? sprintf('%.4f', round($data['share_user'] / $data['int_page_read_user'], 4)) * 100 : 0,
            ':add_to_fav_user' => $data['add_to_fav_user'],
            ':add_to_fav_count' => $data['add_to_fav_count'],
            ':add_to_fav_rate' => !empty($data['int_page_read_user']) ? sprintf('%.4f', round($data['add_to_fav_user'] / $data['int_page_read_user'], 4)) * 100 : 0,
            ':int_page_from_session_read_user' => $data['int_page_from_session_read_user'],
            ':int_page_from_session_read_count' => $data['int_page_from_session_read_count'],
            ':int_page_from_hist_msg_read_user' => $data['int_page_from_hist_msg_read_user'],
            ':int_page_from_hist_msg_read_count' => $data['int_page_from_hist_msg_read_count'],
            ':int_page_from_feed_read_user' => $data['int_page_from_feed_read_user'],
            ':int_page_from_feed_read_count' => $data['int_page_from_feed_read_count'],
            ':int_page_from_friends_read_user' => $data['int_page_from_friends_read_user'],
            ':int_page_from_friends_read_count' => $data['int_page_from_friends_read_count'],
            ':int_page_from_other_read_user' => $data['int_page_from_other_read_user'],
            ':int_page_from_other_read_count' => $data['int_page_from_other_read_count'],
            ':feed_share_from_session_user' => $data['feed_share_from_session_user'],
            ':feed_share_from_session_cnt' => $data['feed_share_from_session_cnt'],
            ':feed_share_from_feed_user' => $data['feed_share_from_feed_user'],
            ':feed_share_from_feed_cnt' => $data['feed_share_from_feed_cnt'],
            ':feed_share_from_other_user' => $data['feed_share_from_other_user'],
            ':feed_share_from_other_cnt' => $data['feed_share_from_other_cnt']
        ])->execute();

        return true;
    }
}