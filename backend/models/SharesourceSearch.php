<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/19
 * Time: 17:11
 */
namespace backend\models;


use common\models\StatisticSharesource;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Query;

/**
 * Class GoodsSearch
 * @package backend\models
 */
class SharesourceSearch extends StatisticSharesource
{
    //设置时间格式为YYYY-MM-DD
    private  $patten = "/^((((1[6-9]|[2-9]\d)\d{2})-(0?[13578]|1[02])-(0?[1-9]|[12]\d|3[01]))|(((1[6-9]|[2-9]\d)\d{2})-(0?[13456789]|1[012])-(0?[1-9]|[12]\d|30))|(((1[6-9]|[2-9]\d)\d{2})-0?2-(0?[1-9]|1\d|2[0-8]))|(((1[6-9]|[2-9]\d)(0[48]|[2468][048]|[13579][26])|((16|[2468][048]|[3579][26])00))-0?2-29-))$/";
    //设置时间格式为YYYY-MM-DD_YYYY-MM-dd
    private $patten1 = "/^[0-9]{4}-(((0[13578]|(10|12))-(0[1-9]|[1-2][0-9]|3[0-1]))|(02-(0[1-9]|[1-2][0-9]))|((0[469]|11)-(0[1-9]|[1-2][0-9]|30)))_[0-9]{4}-(((0[13578]|(10|12))-(0[1-9]|[1-2][0-9]|3[0-1]))|(02-(0[1-9]|[1-2][0-9]))|((0[469]|11)-(0[1-9]|[1-2][0-9]|30)))$/";
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['date_time'], 'string', 'min' => 10,'max'=>21,
              'tooShort'=> '{attribute} 长度不能小于{min}字符，且格式为yyyy-mm-dd',
              'tooLong' => '{attribute} 长度不能大于{max}个字符,且格式为yyyy-mm-dd_yyyy-mm-dd'],
            [['date_time'], 'requiredByASpecial'],
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
            ->select(['wechat_no','qzone_no','weibo_no','qq_no','wx_circle_no','date_time'])
            ->from('mb_statistic_sharesource')
            ->orderBy('date_time desc');





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



        if(!empty($this->date_time))
        {
            //设置时间格式为YYYY-MM-DD


            if(preg_match($this->patten,$this->date_time))
            {
                $query->andFilterWhere(['like', 'date_time', $this->date_time]);
            }
            else
            {
                if(preg_match($this->patten1,$this->date_time))
                {
                    if(stripos($this->date_time,'_') == 10 && strlen($this->date_time) == 21)
                    {
                        $date_time = explode('_',$this->date_time);
                        $date_time[0] = trim($date_time[0]);
                        $date_time[1] = trim($date_time[1]);
                        $query->andFilterWhere(['between','date_time',$date_time[0],$date_time[1]]);
                    }
                }

            }

        }
        return $dataProvider;
    }

    //设置用户必须按正确格式填写，不允许出现xxxx-xx-xx
    public function requiredByASpecial($attribute)
    {
        if(!empty($this->date_time))
        {
            if(!preg_match($this->patten,$this->date_time))
            {
                if(preg_match($this->patten1,$this->date_time))
                {
                    if(!(stripos($this->date_time,'_') == 10 && strlen($this->date_time) == 21))
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