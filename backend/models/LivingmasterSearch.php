<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/19
 * Time: 15:57
 */
namespace backend\models;


use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Query;

/**
 * Class GoodsSearch
 * @package backend\models
 */
class LivingmasterSearch extends StatisticLivingmaster
{
    //设置时间格式为YYYY-MM-DD
    private  $patten = "/^((((1[6-9]|[2-9]\d)\d{2})-(0?[13578]|1[02])-(0?[1-9]|[12]\d|3[01]))|(((1[6-9]|[2-9]\d)\d{2})-(0?[13456789]|1[012])-(0?[1-9]|[12]\d|30))|(((1[6-9]|[2-9]\d)\d{2})-0?2-(0?[1-9]|1\d|2[0-8]))|(((1[6-9]|[2-9]\d)(0[48]|[2468][048]|[13579][26])|((16|[2468][048]|[3579][26])00))-0?2-29-))$/";
    //设置时间格式为YYYY-MM-DD_YYYY-MM-dd
    private  $patten1 = "/^[0-9]{4}-(((0[13578]|(10|12))-(0[1-9]|[1-2][0-9]|3[0-1]))|(02-(0[1-9]|[1-2][0-9]))|((0[469]|11)-(0[1-9]|[1-2][0-9]|30)))_[0-9]{4}-(((0[13578]|(10|12))-(0[1-9]|[1-2][0-9]|3[0-1]))|(02-(0[1-9]|[1-2][0-9]))|((0[469]|11)-(0[1-9]|[1-2][0-9]|30)))$/";
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['share_date'], 'string', 'min' => 10,'max'=>21,
                'tooShort'=> '{attribute} 长度不能小于{min}字符，且格式为yyyy-mm-dd',
                'tooLong' => '{attribute} 长度不能大于{max}个字符,且格式为yyyy-mm-dd_yyyy-mm-dd'],
            [['client_no'], 'integer'],
            [['share_date'], 'requiredByASpecial'],
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
            ->select(['share_date','mc.nick_name','living_master_share_no','audience_share_no','total_no','mc.client_no'])
            ->from('mb_statistic_livingmaster_share sls')
            ->innerJoin('mb_client mc','sls.living_master_id=mc.client_id')
            ->orderBy('share_date desc');



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

        if(!empty($this->share_date))
        {

            if(preg_match($this->patten,$this->share_date))
            {
                $query->andFilterWhere([
                    'share_date' => $this->share_date
                ]);
            }
            else
            {
                if(preg_match($this->patten1,$this->share_date))
                {
                    if(stripos($this->share_date,'_') == 10 && strlen($this->share_date) == 21)
                    {
                        $share_date = explode('_',$this->share_date);
                        $share_date[0] = trim($share_date[0]);
                        $share_date[1] = trim($share_date[1]);
                        $query->andFilterWhere(['between','share_date',$share_date[0],$share_date[1]]);
                    }
                }

            }

        }
        $query->andFilterWhere(['like','client_no', $this->client_no]);
        return $dataProvider;
    }


    //设置用户必须按正确格式填写，不允许出现xxxx-xx-xx
    public function requiredByASpecial($attribute)
    {
        if(!empty($this->share_date))
        {
            if(!preg_match($this->patten,$this->share_date))
            {
                if(preg_match($this->patten1,$this->share_date))
                {
                    if(!(stripos($this->share_date,'_') == 10 && strlen($this->share_date) == 21))
                    {
                        $this->addError($attribute,"请按正确格式填写，正确格式为yyyy-mm-dd或者yyyy-mm-dd_yyyy-mm-dd.");
                    }
                }
                else
                {
                    $this->addError($attribute,"请按正确格式填写，正确格式为yyyy-mm-dd或者yyyy-mm-dd_yyyy-mm-dd.");
                }
            }

        }

    }
}