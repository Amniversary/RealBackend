<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/11
 * Time: 16:34
 */
namespace backend\models;


use common\models\UserContact;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Query;

/**
 * Class GoodsSearch
 * @package backend\models
 */
class UserContactSearch extends UserContact
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id','client_no'], 'integer'],
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
            ->select(['muc.user_id','muc.user_name','muc.phone','muc.alipay','muc.address','muc.wx_number','muc.wx_name','mc.client_no','mc.nick_name'])
            ->from('mb_user_contact muc')
            ->innerjoin('mb_client mc','muc.user_id=mc.client_id');



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


        $query->andFilterWhere(['like', 'user_id', $this->user_id])
            ->andFilterWhere(['like', 'client_no', $this->client_no]);


        return $dataProvider;
    }
}