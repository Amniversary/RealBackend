<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/9/5
 * Time: 下午4:29
 */

namespace backend\models;


use common\models\SystemTag;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class TagSearch extends SystemTag
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tag_name', 'remark1', 'remark2'], 'safe'],
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
        $query = SystemTag::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([

        ]);

        return $dataProvider;
    }
}