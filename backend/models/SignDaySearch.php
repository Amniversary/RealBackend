<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/8/21
 * Time: 下午2:07
 */

namespace backend\models;


use backend\business\WeChatUserUtil;
use common\models\SignParams;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class SignDaySearch extends SignParams
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['app_id', 'day_id', 'type'], 'integer'],
            [['remark1', 'remark2'], 'safe'],
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
        $cacheInfo = WeChatUserUtil::getCacheInfo();
        $query = SignParams::find()->where(['app_id'=>$cacheInfo['record_id'], 'type'=>0]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'day_id'=>$this->day_id
        ]);

        $query->orderBy('day_id asc');
        return $dataProvider;
    }

    public function searchBatch($params)
    {
        $query = SignParams::find()->where(['type'=>1]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'day_id'=>$this->day_id
        ]);

        $query->orderBy('day_id asc');
        return $dataProvider;
    }
}