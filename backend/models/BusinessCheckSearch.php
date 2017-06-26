<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\BusinessCheck;

/**
 * BusinessCheckSearch represents the model behind the search form about `common\models\BusinessCheck`.
 */
class BusinessCheckSearch extends BusinessCheck
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['business_check_id', 'relate_id', 'business_type', 'status', 'check_result_status', 'check_user_id', 'create_user_id'], 'integer'],
            [['create_time', 'check_time', 'check_user_name', 'create_user_name', 'remark1', 'remark2', 'remark3', 'remark4'], 'safe'],
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
        $query = BusinessCheck::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
          'pagination' => [
              'pageSize' => 15,
          ],
        ]);

        $this->load($params);

        if (!$this->validate())
        {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        $user_id = \Yii::$app->user->id;
        $data = \Yii::$app->cache->get('backend_user_check_no'.strval($user_id));

        if($data !== false)
        {
            $checkNoAry = json_decode($data,true);
            $query->andFilterWhere(['between','check_no',$checkNoAry['start_no'],$checkNoAry['end_no']]);
        }
        else
        {
            $query->andFilterWhere(['check_no'=>'-1']);
        }

        if(!empty($this->create_time) && strtotime($this->create_time) !== false)
        {
            $startTime = date('Y-m-d 00:00:00', strtotime($this->create_time));
            $endTime = date('Y-m-d 23:59:59', strtotime($this->create_time));
            $query->andFilterWhere(['between','create_time',$startTime,$endTime]);
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'business_check_id' => $this->business_check_id,
            'relate_id' => $this->relate_id,
            'business_type' => $this->business_type,
            'status' => $this->status,
            'check_result_status' => $this->check_result_status,
            'check_time' => $this->check_time,
            'check_user_id' => $this->check_user_id,
            'create_user_id' => $this->create_user_id,
        ]);
        $query->andFilterWhere(['like', 'check_user_name', $this->check_user_name])
            ->andFilterWhere(['like', 'create_user_name', $this->create_user_name])
            ->andFilterWhere(['<', 'business_type', 5]);

        return $dataProvider;
    }
}
