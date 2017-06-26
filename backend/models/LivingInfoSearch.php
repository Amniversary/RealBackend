<?php

namespace backend\models;

use backend\models\LivingInfoForm;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\AccountInfo;
use yii\db\Query;

/**
 * AccountInfoSearch represents the model behind the search form about `common\models\AccountInfo`.
 */
class LivingInfoSearch extends LivingInfoForm
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['client_id','unique_no','client_no','living_id'], 'integer'],
            [['push_url','pull_http_url','pull_rtmp_url','pull_hls_url','qiniu_info'], 'safe'],
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
        $query = new Query();
        $query->select(['c.unique_no', 'c.client_no', 'l.living_id', 'l.push_url','l.pull_http_url', 'l.pull_rtmp_url', 'l.pull_hls_url','q.qiniu_info'])
        ->from('mb_client c')
        ->leftJoin('mb_living l', 'c.client_id=l.living_master_id')
        ->leftJoin('mb_client_qiniu q', 'c.client_id=q.user_id')
        ->where('c.client_id > 0');
        $count = $query->count('c.client_id');

        $dataProvider = new ActiveDataProvider([
            'key'=>'client_id',
            'totalCount'=>$count,
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
            'unique_no' => $this->unique_no,
            'client_no' => $this->client_no,
            'living_id'=>$this->living_id,
        ]);

        return $dataProvider;
    }
}
