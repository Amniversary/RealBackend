<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/23
 * Time: 15:23
 */

namespace backend\models;


use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Query;

class FamilySonSearch extends FinanceSonSearchForm
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['client_id','record_id'], 'integer'],
            [['client_no','nick_name'], 'string'],
            [['create_time','living_time'], 'safe'],
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

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = (new Query())
            ->select(['fm.record_id','cl.client_no','nick_name','icon_pic','fm.create_time','cl.status','lv.finish_time','ba.ticket_count','ba.ticket_count_sum','st.status as stop_status','fm.remark1','sum(lt.living_time) as living_time'])
            ->from('mb_family_member fm')
            ->innerJoin('mb_client cl','cl.client_id=fm.family_member_id')
            ->leftJoin('mb_living lv', 'fm.family_member_id=lv.living_master_id')
            ->leftJoin('mb_balance ba', 'fm.family_member_id=ba.user_id')
            ->leftJoin('mb_stop_living st', 'lv.living_id = st.living_id')
            ->where('fm.family_id=:fid',[':fid'=>$params['family_id']])
            ->groupBy('cl.client_no')
            ->orderBy('fm.create_time',SORT_DESC);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'key'=>'record_id',
            'query' => $query,
            'pagination' => [
                'pageSize' => 15,
            ],

        ]);

        $this->load($params);

        if (!$this->validate())
        {
            return $dataProvider;
        }

        if(!empty($this->create_time)) {
            if (!empty($this->create_time['from'])) {
                $fromDateTime = $this->create_time['from'] . ' 00:00:00';
                $query->andWhere(['>=', 'fm.create_time', $fromDateTime]);
            }
            if (!empty($this->create_time['to'])) {
                $toDateTime = $this->create_time['to'] . ' 23:59:59';
                $query->andWhere(['<=', 'fm.create_time', $toDateTime]);
            }
            $this->create_time = json_encode($this->create_time);
        }

        $condition = 'lt.client_no = cl.client_no and lt.statistic_type = 1';
        if(!empty($this->living_time)) {
            if (!empty($this->living_time['from']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $this->living_time['from'])) {
                $condition .= " and lt.statistic_date >= '{$this->living_time['from']}'";
            }
            if (!empty($this->living_time['to']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $this->living_time['to'])) {
                $condition .= " and lt.statistic_date <= '{$this->living_time['to']}'";
            }
            $this->living_time = json_encode($this->living_time);
        }

        $query->leftJoin('mb_statistic_living_time lt', $condition);

        $query->andFilterWhere(['like', 'client_no', $this->client_no])
            ->andFilterWhere(['like' ,'nick_name',$this->nick_name]);

        return $dataProvider;
    }
} 