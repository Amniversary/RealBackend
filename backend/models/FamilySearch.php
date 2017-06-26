<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/23
 * Time: 15:23
 */

namespace backend\models;


use common\models\Family;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class FamilySearch extends Family
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['status'], 'integer'],
            [['family_user_name','family_name'], 'safe'],
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
        $query = Family::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'key'=>'family_id',
            'query' => $query,
            'pagination' => [
                'pageSize' => 15,
            ],

        ]);

        $this->load($params);

        if (!$this->validate())
        {
            return $dataProvider;
        }

        if(!empty($this->create_time))
        {
            $start_time = date('Y-m-d 00:00:00',strtotime($this->create_time));
            $end_time = date('Y-m-d 23:59:59',strtotime($this->create_time));
            $query->andFilterWhere(['between','create_time',$start_time, $end_time]);
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'family_id' => $this->family_id,
            'family_user_name' => $this->family_user_name,
            'family_name' =>$this->family_name,
            'family_num' =>$this->family_num,
            'pic'=>$this->pic,
            'status' => $this->status,
            'password'=>$this->password,
        ]);

        $query->andFilterWhere(['like', 'family_user_name', $this->family_user_name])
            ->andFilterWhere(['like' ,'family_name',$this->family_name]);

        $query->orderBy('status desc');

        return $dataProvider;
    }
} 