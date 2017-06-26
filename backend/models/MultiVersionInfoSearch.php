<?php

namespace backend\models;

use common\models\MultiVersionInfo;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * AccountInfoSearch represents the model behind the search form about `common\models\AccountInfo`.
 */
class MultiVersionInfoSearch extends MultiVersionInfo
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['app_type','status'], 'integer'],
            [['app_id', 'remark1', 'remark2', 'remark3', 'remark4'], 'string', 'max' => 100],
            [['app_id'], 'unique'],
            [['app_name'], 'string'],
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
        $query = MultiVersionInfo::find()->orderBy('record_id desc');

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

        // grid filtering conditions
        $query->andFilterWhere([
            'record_id' => $this->record_id,
            'app_type'=>$this->app_type,
            'status'=>$this->status
        ]);

        $query->andFilterWhere(['like', 'app_id', $this->app_id])
            ->andFilterWhere(['like' ,'app_name',$this->app_name]);

        return $dataProvider;
    }
}
