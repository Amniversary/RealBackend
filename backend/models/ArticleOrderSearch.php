<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/9/9
 * Time: 下午5:37
 */

namespace backend\models;


use common\models\ArticleOrder;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class ArticleOrderSearch extends ArticleOrder
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['app_id'], 'safe'],
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
        $query = ArticleOrder::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);
        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'app_id' => $this->app_id,
        ]);


        return $dataProvider;
    }
}