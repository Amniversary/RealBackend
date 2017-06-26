<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/10
 * Time: 14:00
 */

namespace backend\models;


use common\models\SystemMessage;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\log\Logger;

/**
 * Class GoodsSearch
 * @package backend\models
 */
class SystemMessageSearch extends SystemMessage
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['status'], 'integer'],
            [['order'], 'string', 'max' => 10],
            [['remark1', 'remark2', 'remark3', 'remark4'], 'string', 'max' => 100],
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
        $query = SystemMessage::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 15,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }
        // grid filtering conditions
        $query->andFilterWhere([
            'message_id' => $this->message_id,
            'status' => $this->status,
        ]);

        if(!empty($this->create_time) && strtotime($this->create_time) !== false)
        {
            $startTime = date('Y-m-d 00:00:00', strtotime($this->create_time));
            $endTime = date('Y-m-d 23:59:59', strtotime($this->create_time));
            $query->andFilterWhere(['between','create_time',$startTime,$endTime]);
        }
        $query->andFilterWhere(['like', 'order', $this->order]);
        return $dataProvider;
    }
}