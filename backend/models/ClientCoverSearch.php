<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/10
 * Time: 14:00
 */
namespace backend\models;


use common\models\Client;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * Class GoodsSearch
 * @package backend\models
 */
class ClientCoverSearch extends Client
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['client_id','client_no','nick_name','sign_name'], 'string', 'max' => 100],
            [['pic','main_pic','icon_pic'], 'string', 'max' => 200]
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
        $query = Client::find()->orderBy('client_no desc');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'totalCount'=>0,
            'pagination' => [
                'pageSize' => 5,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere(['like', 'client_no', $this->client_no])
            ->andFilterWhere(['like', 'client_id', $this->client_id])
            ->andFilterWhere(['like','nick_name', $this->nick_name])
            ->andFilterWhere(['like','sign_name', $this->sign_name]);


        return $dataProvider;
    }
} 