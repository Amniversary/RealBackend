<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/29
 * Time: 16:54
 */
namespace backend\models;


use common\models\CommonWords;
use Yii;
use yii\base\Model;
use yii\db\Query;
use yii\data\ActiveDataProvider;

class SuperAdminWordsSearch extends CommonWords
{
    /**
     * @inheritdoc
     */
    public function rules(){
        return [
            [['user_id', 'status'], 'integer'],
            [['create_at'], 'safe'],
            [['content', 'remark1', 'remark2', 'remark3', 'remark4'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios(){
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
            ->select(['user_id','cid','content','status','create_at'])
            ->from('mb_common_words')
            ->where('user_id=1');

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
            'status' => $this->status,
            'create_at' =>$this->create_at,
            'user_id' =>$this->user_id
        ]);

        return $dataProvider;
    }
}