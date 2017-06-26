<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/29
 * Time: 16:55
 */
namespace backend\models;

use Yii;
use yii\base\Model;

class DailyMost extends Model{

    public $real_tickets_date;
    public $living_master_id;
    public $real_tickets_num;
    public $recharge_date;
    public $user_id;
    public $recharge_amount;
    public $send_gift_num;
    public $send_gift_date;
    public $record_id;
    public $client_no;
    public $nick_name;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['record_id','living_master_id','send_gift_num','user_id','real_tickets_num','recharge_amount'], 'integer'],
            [['real_tickets_date','recharge_date','send_gift_date'], 'safe'],
            [['client_no','nick_name'], 'string', 'max' => 100],
        ];
    }

    public function  attributeLabels()
    {
        return [
            'client_no'=>'蜜播 ID',
            'living_master_id'=>'主播 ID',
            'send_gift_num'=>'送礼物的实际票数',
            'real_tickets_num'=>'可提现票数',
            'recharge_amount'=>'充值金额',
            'real_tickets_date'=>'提现日期',
            'recharge_date'=>'充值日期',
            'send_gift_date'=>'送礼日期',
            'nick_name'=>'用户昵称',
            'user_id'=>'用户 ID',
            'record_id' => '自增 id'
        ];
    }
}
