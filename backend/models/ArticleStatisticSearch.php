<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/8/28
 * Time: 下午1:59
 */

namespace backend\models;


use common\models\ArticleTotal;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class ArticleStatisticSearch extends ArticleTotal
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['app_id', 'order_no', 'target_user', 'int_page_read_user', 'int_page_read_count', 'ori_page_read_user', 'ori_page_read_count', 'share_user', 'share_count', 'add_to_fav_user', 'add_to_fav_count', 'int_page_from_session_read_user', 'int_page_from_session_read_count', 'int_page_from_hist_msg_read_user', 'int_page_from_hist_msg_read_count', 'int_page_from_feed_read_user', 'int_page_from_feed_read_count', 'int_page_from_friends_read_user', 'int_page_from_friends_read_count', 'int_page_from_other_read_user', 'int_page_from_other_read_count', 'feed_share_from_session_user', 'feed_share_from_session_cnt', 'feed_share_from_feed_user', 'feed_share_from_feed_cnt', 'feed_share_from_other_user', 'feed_share_from_other_cnt'], 'integer'],
            [['page_read_rate', 'ori_page_read_rate', 'share_rate', 'add_to_fav_rate'], 'number'],
            [['stat_date', 'title' , 'msg_id' , 'stat_date'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $stat = date('Y-m-d', strtotime('-3 day'));
        $end = date('Y-m-d');
        $query = ArticleTotal::find()->where(['between','stat_date', $stat, $end]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        if(!empty($this->stat_date)){
            $start_time = date('Y-m-d 00:00:00',strtotime($this->stat_date));
            $end_time = date('Y-m-d 23:00:00',strtotime($this->stat_date));
            $query->andFilterWhere(['between' , 'stat_date', $start_time, $end_time]);
        }
        $query->andFilterWhere([
            'app_id'=> $this->app_id,
        ]);

        $query->orderBy('stat_date desc');
        return $dataProvider;
    }
}