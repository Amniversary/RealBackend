<?php

namespace backend\models;

use common\models\MultiUpdateContent;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * AccountInfoSearch represents the model behind the search form about `common\models\AccountInfo`.
 */
class MultiUpdateContentSearch extends MultiUpdateContent
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['discribtion', 'update_content','module_id'], 'string'],
            [['force_update','status'], 'integer'],
            [['app_id', 'app_version_inner', 'remark1', 'remark2', 'remark3', 'remark4'], 'string', 'max' => 100],
            [['link'], 'string', 'max' => 200],
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
        $query = MultiUpdateContent::find()->where(['app_id'=>$params['app_id']])->orderBy('update_id desc');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'key'=>'update_id',
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

        // grid filtering conditions
        $query->andFilterWhere([
            'update_id' => $this->update_id,
            'app_id'=>$this->app_id,
            'status'=>$this->status,
            'force_update'=>$this->force_update,
        ]);

        $query->andFilterWhere(['like', 'module_id', $this->module_id])
            ->andFilterWhere(['like' ,'app_version_inner',$this->app_version_inner])
            ->andFilterWhere(['like' ,'link',$this->link]);

        return $dataProvider;
    }
}
