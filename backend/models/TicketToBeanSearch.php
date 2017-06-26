<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/10
 * Time: 14:00
 */

namespace backend\models;


use common\models\TicketToBean;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * Class GoodsSearch
 * @package backend\models
 */
class TicketToBeanSearch extends TicketToBean
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'bean_rate', 'status'], 'integer'],
            [['ticket_num','bean_num'],'number'],
            [['create_time', 'check_time','op_unique_no'], 'safe'],
            [['refuesd_reason', 'remark1', 'remark2', 'remark3', 'remark4'], 'string', 'max' => 100],
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
        $query = TicketToBean::find();

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

        if(!empty($this->create_time) && strtotime($this->create_time) !== false)
        {
            $startTime = date('Y-m-d 00:00:00', strtotime($this->create_time));
            $endTime = date('Y-m-d 23:59:59', strtotime($this->create_time));
            $query->andFilterWhere(['between','create_time',$startTime,$endTime]);
        }
        // grid filtering conditions
        $query->andFilterWhere([
            'record_id' => $this->record_id,
            'status' => $this->status,
        ]);



        $query->andFilterWhere(['like', 'ticket_num', $this->ticket_num])
            ->andFilterWhere(['like','bean_num', $this->bean_num]);





        return $dataProvider;
    }
}