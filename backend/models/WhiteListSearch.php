<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/19
 * Time: 19:39
 */

namespace backend\models;


use common\models\OffUserLiving;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class WhiteListSearch extends OffUserLiving
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['client_no', 'remark1', 'remark3','remark4'], 'string'],
            [['remark2'], 'safe']
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
        $query = OffUserLiving::find();

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

        $query->andFilterWhere(['like', 'client_no', $this->client_no]);
        $query->andFilterWhere(['remark3' => $this->remark3]);

        if (!empty($this->remark2)) {
            if (!empty($this->remark2['from'])) {
                $fromDateTime = $this->remark2['from'] . ' 00:00:00';
                $query->andWhere(['>=', 'remark2', $fromDateTime]);
            }
            if (!empty($this->remark2['to'])) {
                $toDateTime = $this->remark2['to'] . ' 23:59:59';
                $query->andWhere(['<=', 'remark2', $toDateTime]);
            }
            $this->remark2 = json_encode($this->remark2);
        }

        return $dataProvider;
    }
}