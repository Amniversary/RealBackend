<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/10/11
 * Time: 下午5:37
 */

namespace backend\models;


use common\models\LaterImage;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class LaterImageSearch extends LaterImage
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['later_id', 'pic_url', ], 'safe'],
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
        $query = LaterImage::find()->where(['later_id'=>$params['id']]);

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