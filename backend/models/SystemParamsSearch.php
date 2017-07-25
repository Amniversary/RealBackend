<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/7/24
 * Time: 上午11:52
 */

namespace backend\models;


use common\models\SystemParams;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class SystemParamsSearch extends SystemParams
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['group_id'], 'integer'],
            [['code', 'title', 'description', 'value1', 'value2', 'value3', 'remark1', 'remark2', 'remark3', 'remark4'], 'safe'],
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
        $query = SystemParams::find();

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

        $query->andFilterWhere([

        ]);
        $query->andFilterWhere(['code' => $this->remark3]);



        return $dataProvider;
    }
}