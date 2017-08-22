<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/7/16
 * Time: 下午10:26
 */

namespace backend\models;


use common\models\Keywords;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class BatchKeywordSearch extends Keywords
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['app_id','rule'], 'integer'],
            [['keyword', 'remark1', 'remark2', 'remark3', 'remark4'], 'safe'],
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
        $query = Keywords::find()->where(['global'=>1]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'rule'=>$this->rule,
        ]);


        return $dataProvider;
    }


    public function searchSign($params)
    {
        $query = Keywords::find()->where(['global'=>3]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'rule'=>$this->rule,
        ]);


        return $dataProvider;
    }
}