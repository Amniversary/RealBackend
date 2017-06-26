<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/10
 * Time: 14:00
 */

namespace backend\models;


use common\models\EnterRoomNote;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\log\Logger;

/**
 * Class GoodsSearch
 * @package backend\models
 */
class EnterRoomNoteSearch extends EnterRoomNote
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['level_no_start', 'level_no_end'], 'integer'],
            [['remark1', 'remark2', 'remark3', 'remark4'], 'string', 'max' => 100],
            [['level_no_start', 'level_no_end'], 'unique', 'targetAttribute' => ['level_no_start', 'level_no_end'], 'message' => 'The combination of Level No Start and Level No End has already been taken.'],
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
            $query = EnterRoomNote::find();

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
            'record_id' => $this->record_id,
        ]);

        $query->andFilterWhere(['like', 'level_no_start', $this->level_no_start])
            ->andFilterWhere(['like', 'level_no_end', $this->level_no_end]);
        return $dataProvider;
    }

}