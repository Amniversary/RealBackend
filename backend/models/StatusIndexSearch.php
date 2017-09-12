<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/9/11
 * Time: 上午9:47
 */

namespace backend\models;


use common\models\CustomerStatistics;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class StatusIndexSearch extends CustomerStatistics
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['task_id', 'app_id', 'user_count', 'user_num', 'create_time'], 'integer'],
            [['remark1'], 'safe'],
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
        $query = CustomerStatistics::find()->where(['task_id' => $params['id']]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'app_id'=>$this->app_id,
        ]);

        return $dataProvider;
    }
}