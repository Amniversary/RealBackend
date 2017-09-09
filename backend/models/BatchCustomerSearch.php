<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/9/7
 * Time: 下午2:15
 */

namespace backend\models;


use common\models\BatchCustomer;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class BatchCustomerSearch extends BatchCustomer
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['status', 'create_time'], 'integer'],
            [['task_name', 'remark1', 'remark2','app_list'], 'safe'],
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
        $query = BatchCustomer::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'status' => $this->status,
        ]);

        return $dataProvider;
    }
}