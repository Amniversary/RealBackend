<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/17
 * Time: 14:36
 */

namespace backend\models;


use common\models\GiftScore;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Query;

/**
 * 礼物积分
 * Class GiftScoreSearch
 * @package backend\models
 */
class GiftScoreSearch extends GiftScoreForm
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['gift_id'], 'integer'],
            [['gift_name'],'safe'],
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
        $query = (new Query())
            ->select(['record_id','gs.gift_id','pic','score','gift_name'])
            ->from('mb_gift_score gs')
            ->leftJoin('mb_gift bg','bg.gift_id = gs.gift_id');

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

        $query->andFilterWhere([
            'record_id'=>$this->record_id,
            'gs.gift_id' => $this->gift_id,
            'pic'=>$this->pic,
            'score'=>$this->score,
        ]);

        $query->andFilterWhere(['like', 'gift_name',$this->gift_name]);

        return $dataProvider;
    }
} 