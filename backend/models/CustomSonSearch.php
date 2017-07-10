<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/7/7
 * Time: 下午5:36
 */

namespace backend\models;


use common\models\AuthorizationMenuSon;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class CustomSonSearch extends AuthorizationMenuSon
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['menu_id'], 'integer'],
            [['name', 'type', 'url','key_type', 'remark1', 'remark2'], 'safe'],
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
        $query = AuthorizationMenuSon::find()->where(['menu_id'=>$params['menu_id']]);

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