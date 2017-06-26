<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/10
 * Time: 14:00
 */

namespace backend\models;


use common\models\LuckygiftParams;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * Class LuckyGiftSearch
 * @package backend\models
 */
class LuckyGiftSearch extends LuckygiftParams
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['receive_rate', 'basic_beans', 'multiple', 'status'], 'integer'],
            [['rate'], 'number'],
            [['create_time'], 'safe'],
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
        $query = LuckygiftParams::find()->orderBy(['status'=>SORT_DESC,'create_time'=>SORT_DESC]);

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
            'lucky_id' => $this->lucky_id,
            'status' => $this->status,
        ]);

        if(!empty($this->create_time) && strtotime($this->create_time) !== false)
        {
            $startTime = date('Y-m-d 00:00:00', strtotime($this->create_time));
            $endTime = date('Y-m-d 23:59:59', strtotime($this->create_time));
            $query->andFilterWhere(['between','create_time',$startTime,$endTime]);
        }

        $query->andFilterWhere(['like', 'receive_rate', $this->receive_rate])
            ->andFilterWhere(['like','basic_beans', $this->basic_beans])
            ->andFilterWhere(['like','rate', $this->rate])
            ->andFilterWhere(['like','multiple', $this->multiple]);
        return $dataProvider;
    }
}