<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/12
 * Time: 17:18
 */

namespace backend\models;


use common\models\SystemParams;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class UserDeviceParamsSearch extends SystemParams
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['params_id'], 'integer'],
            [['title','description', 'code','value1', 'value2', 'value3','remark1', 'remark2', 'remark3', 'remark4'], 'safe'],
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
        $query = SystemParams::find()->where('group_id = 1');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 15,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'params_id' => $this->params_id,
            'group_id'=>$this->group_id,
        ]);

        $query->andFilterWhere(['like', 'code', $this->code])
            ->andFilterWhere(['like','value1', $this->value1])
            ->andFilterWhere(['like', 'value2', $this->value2])
            ->andFilterWhere(['like', 'value3', $this->value3])
            ->andFilterWhere(['like','title',$this->title])
            ->andFilterWhere(['like','description',$this->description]);

        return $dataProvider;
    }
} 