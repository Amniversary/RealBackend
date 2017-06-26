<?php

namespace backend\models;

use common\models\GameStatisticResult;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * AccountInfoSearch represents the model behind the search form about `common\models\AccountInfo`.
 */
class NiuNiuStatisticDateSearch extends GameStatisticResult
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['statistic_type', 'win_num', 'lose_num', 'is_win', 'win_chip_money', 'lose_chip_money', 'system_chip_money'], 'integer'],
            [['statistic_time', 'remark1', 'remark2', 'remark3', 'remark4'], 'string', 'max' => 100],
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
        $query = GameStatisticResult::find()->orderBy(['record_id'=>SORT_DESC]);

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
        if(!empty($this->statistic_time))
        {
            $statistic_time = explode('|',$this->statistic_time);
            if (isset($statistic_time[1]))
            {
                $query->andFilterWhere(['between','statistic_time',$statistic_time[0],$statistic_time[1]]);
            }
            else
            {
                $query->andFilterWhere(['statistic_time' => $this->statistic_time]);
            }
        }
        // grid filtering conditions
        $query->andFilterWhere([
            'record_id' => $this->record_id,
            'is_win' => $this->is_win,
            'statistic_type' => $this->statistic_type
        ]);

        $query->andFilterWhere(['like', 'win_num', $this->win_num])
            ->andFilterWhere(['like', 'lose_num', $this->lose_num])
            ->andFilterWhere(['like', 'lose_chip_money', $this->lose_chip_money])
            ->andFilterWhere(['like', 'system_chip_money', $this->system_chip_money])
            ->andFilterWhere(['like', 'win_chip_money', $this->win_chip_money]);

        return $dataProvider;
    }
}
