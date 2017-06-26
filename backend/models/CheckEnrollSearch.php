<?php

namespace backend\models;

use common\models\EnrollInfo;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * AccountInfoSearch represents the model behind the search form about `common\models\AccountInfo`.
 */
class CheckEnrollSearch extends EnrollInfo
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'activity_id'], 'integer'],
            [['create_time'], 'safe'],
            [['client_no', 'remark1', 'remark2', 'remark3', 'remark4'], 'string', 'max' => 100],
            [['name'], 'string', 'max' => 50],
            [['phone_number'], 'string', 'max' => 11],
            [['sex'], 'string', 'max' => 10],
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
     * 未审核
     * @param $params
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = EnrollInfo::find()->where('status = 0')->orderBy(['create_time'=>SORT_DESC]);
        return $this->ReturnDataProvider($query,$params);
    }

    /**
     * 已审核/已拒绝
     * @param $params
     * @return ActiveDataProvider
     */
    public function already_search($params)
    {
        $query = EnrollInfo::find()->where('status != 0')->orderBy(['create_time'=>SORT_DESC]);
        return $this->ReturnDataProvider($query,$params);
    }

    public function ReturnDataProvider($query,$params)
    {
        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'key'=>'enroll_id',
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

        if(!empty($this->create_time))
        {
            $start_time = date('Y-m-d 00:00:00',strtotime($this->create_time));
            $end_time = date('Y-m-d 23:59:59',strtotime($this->create_time));
            $query->andFilterWhere(['between','create_time',$start_time, $end_time]);
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'user_id' => $this->user_id,
            'status' => $this->status,
            'sex'=>$this->sex,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like' ,'client_no',$this->client_no])
            ->andFilterWhere(['like', 'phone_number', $this->phone_number]);

        return $dataProvider;
    }
}
