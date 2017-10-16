<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/10/11
 * Time: 下午3:52
 */

namespace backend\models;


use common\models\LaterParams;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class LaterSearch extends LaterParams
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'remark1', 'remark2'], 'safe'],
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
        $query = LaterParams::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        return $dataProvider;
    }
}