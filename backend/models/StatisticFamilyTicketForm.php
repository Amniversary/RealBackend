<?php
namespace backend\models;

use Yii;
use yii\base\Model;

/**
 */
class StatisticFamilyTicketForm extends Model
{
    public $record_id;
    public $family_name;
    public $family_id;
    public $income_ticket;
    public $ticket_to_cash;
    public $create_time;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
        ];
    }

    public function  attributeLabels()
    {
        return [
            'record_id'=>'自增id',
            'family_name'=>'家族名称',
            'family_id'=>'家族id',
            'income_ticket'=>'获取的票数',
            'ticket_to_cash'=>'提现的票数',
            'create_time'=>'统计时间',
        ];
    }

}
