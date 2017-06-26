<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\User;

/**
 * BackendUserSearch represents the model behind the search form about `common\models\User`.
 */
class BackendUserSearch extends User
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['backend_user_id', 'status', 'create_at', 'update_at'], 'integer'],
            [['username', 'pwd_hash', 'pwd_reset_token', 'email', 'auth_key', 'password', 'pic', 'remark1', 'remark2'], 'safe'],
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
        $query = User::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'backend_user_id' => $this->backend_user_id,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'username', $this->username])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'pic', $this->pic]);


        return $dataProvider;
    }
}
