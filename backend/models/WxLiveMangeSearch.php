<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/24
 * Time: 13:30
 */

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\db\Query;
use yii\data\ActiveDataProvider;
use common\models\WxLiveManage;
/**
 * WxLiveMangeSearch represents the model behind the search form about `common\models\WxLiveManage`.
 */
class WxLiveMangeSearch extends WxLiveManage
{
    /**
     * @inheritdoc
     */
    public function rules(){
        return [
            [['client_no', 'status'], 'integer'],
            [['name'], 'string', 'max' => 20],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios(){
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
    public function search($params){
        $query = WxLiveManage::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 15,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'client_no'            => $this->client_no,
            'status'       => $this->status
        ]);

        return $dataProvider;
    }

}
