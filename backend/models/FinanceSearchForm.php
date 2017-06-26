<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/25
 * Time: 16:18
 */

namespace backend\models;


use yii\base\Model;

class FinanceSearchForm extends Model
{
    public $client_id;
    public $client_no;
    public $nick_name;
    public $bean_balance;
    public $virtual_bean_balance;
    public $ticket_count;
    public $ticket_real_sum;
    public $ticket_count_sum;
    public $virtual_ticket_count;
    public $send_ticket_count;
    public $status;
    public $freeze_status;
    public $bean_status;
    public $ticket_status;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['client_no','status','client_id','bean_balance',''], 'integer'],
            [['nick_name'], 'safe'],
        ];
    }

    public function  attributeLabels()
    {
        return [
            'client_id'=>'自增 ID',
            'client_no' => '蜜播 ID',
            'nick_name'=>'用户昵称',
            'bean_balance'=>'实际豆余额',
            'virtual_bean_balance'=>'虚拟豆余额',
            'ticket_count' => '可提现剩余票数',
            'ticket_real_sum' => '可提现总票数',
            'ticket_count_sum' => '累计票数',
            'virtual_ticket_count' => '虚拟票数',
            'send_ticket_count' => '送出总票数',
            'status' => '状态',
            'freeze_status' => '冻结状态',
            'bean_status' => '鲜花状态',
            'ticket_status' => '余额状态',
        ];
    }
} 