<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/26
 * Time: 14:33
 */

namespace backend\models;


use yii\base\Model;

class ActivityPrizeSendForm extends Model
{
    public $client_no;
    public $nick_name;
    public $activity_id;
    public $reward_name;
    public $prize_name;
    public $prize_type;
    public $is_winning;
    public $is_send;
    public $is_direct_send;
    public $prize_user_name;
    public $prize_user_site;
    public $express_num;
    public $create_time;
    public $title;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['activity_id', 'client_no', 'prize_type', 'is_winning', 'is_send', 'is_direct_send','express_num'], 'integer'],
            [['prize_user_name','reward_name','prize_user_site','create_time'], 'safe'],
        ];
    }
} 