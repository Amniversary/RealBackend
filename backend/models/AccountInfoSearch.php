<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\AccountInfo;

/**
 * AccountInfoSearch represents the model behind the search form about `common\models\AccountInfo`.
 */
class AccountInfoSearch extends AccountInfo
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['account_id', 'centification_level', 'user_type', 'status', 'is_inner'], 'integer'],
            [['nick_name', 'pic', 'sign_name', 'phone_no', 'device_no', 'sex', 'occupation', 'interest', 'emotional_state', 'email', 'create_time', 'remark1', 'remark2', 'remark3', 'remark4', 'school_name', 'school_area', 'hometown'], 'safe'],
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
        $query = AccountInfo::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        if(!empty($this->create_time))
        {
            $start_time = date('Y-m-d 00:00:00',strtotime($this->create_time));
            $end_time = date('Y-m-d 23:59:59',strtotime($this->create_time));
            $query->andFilterWhere(['between','create_time',$start_time, $end_time]);
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'account_id' => $this->account_id,
            'centification_level' => $this->centification_level,
            'user_type' => $this->user_type,
            'status' => $this->status,
            'is_inner' => $this->is_inner,
        ]);

        $query->andFilterWhere(['like', 'nick_name', $this->nick_name])
            //->andFilterWhere(['like', 'pic', $this->pic])
            ->andFilterWhere(['like', 'sign_name', $this->sign_name])
            ->andFilterWhere(['like', 'phone_no', $this->phone_no])
           // ->andFilterWhere(['like', 'device_no', $this->device_no])
            ->andFilterWhere(['like', 'sex', $this->sex])
           // ->andFilterWhere(['like', 'occupation', $this->occupation])
           // ->andFilterWhere(['like', 'interest', $this->interest])
           // ->andFilterWhere(['like', 'emotional_state', $this->emotional_state])
           // ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'remark1', $this->remark1])
            ->andFilterWhere(['like', 'remark2', $this->remark2])
            ->andFilterWhere(['like', 'remark3', $this->remark3])
            ->andFilterWhere(['like', 'remark4', $this->remark4])
            ->andFilterWhere(['like', 'school_name', $this->school_name])
            ->andFilterWhere(['like', 'school_area', $this->school_area])
            ->andFilterWhere(['like', 'hometown', $this->hometown]);

        return $dataProvider;
    }
}
