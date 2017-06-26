<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/29
 * Time: 16:37
 */

namespace backend\models;


use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Query;

/**
 * Class GoodsSearch
 * @package backend\models
 */
class DailyMostSearch extends DailyMost
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['real_tickets_date','recharge_date','send_gift_date'], 'safe'],
            [['client_no'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function SearchWhere($query,$params){
        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 50,
            ],
            'totalCount' => 1,
        ]);

        $this->load($params);

        if (!$this->validate())
        {
            return $dataProvider;
        }

        return $dataProvider;
    }

    /**
     * 收到票数榜
     */
    public function search($params)
    {
        $query = (new Query())
            ->select(['real_tickets_date','bc.client_no','bc.nick_name','real_tickets_num'])
            ->from('mb_statistic_daily_living_master bm')
            ->innerJoin('mb_client bc','bm.living_master_id = bc.client_id')
            ->orderBy('real_tickets_num desc');
        $dataProvider = $this->SearchWhere($query,$params);

        $query->andFilterWhere([
            'client_no' => $this->client_no,
            'real_tickets_date' => $this->real_tickets_date,
        ]);

        return  $dataProvider;
    }

    //充值榜
    public function RechargeSearch($params)
    {
        $query = (new Query())
            ->select(['recharge_date','bc.client_no','bc.nick_name','recharge_amount'])
            ->from('mb_statistic_daily_recharge br')
            ->innerJoin('mb_client bc','br.user_id = bc.client_id')
            ->orderBy('recharge_amount desc');
        $dataProvider = $this->SearchWhere($query,$params);

        $query->andFilterWhere([
            'client_no' => $this->client_no,
            'recharge_date' => $this->recharge_date,
        ]);
        return  $dataProvider;
    }

    //送礼物榜
    public function GiftSearch($params)
    {
        $query = (new Query())
            ->select(['send_gift_date','bc.client_no','bc.nick_name','send_gift_num'])
            ->from('mb_statistic_daily_send_gift bg')
            ->innerJoin('mb_client bc','bg.living_master_id = bc.client_id')
            ->orderBy('send_gift_num desc');
        $dataProvider = $this->SearchWhere($query,$params);

        $query->andFilterWhere([
            'client_no' => $this->client_no,
            'send_gift_date' => $this->send_gift_date,
        ]);
        return  $dataProvider;
    }

}