<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/8/22
 * Time: 下午5:03
 */

namespace backend\models;


use common\models\SignImage;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class SignImageSearch extends SignImage
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sign_id'], 'integer'],
            [['pic_url'], 'safe'],
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
        $query = SignImage::find()->where(['sign_id'=>$params['id']]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        return $dataProvider;
    }
}