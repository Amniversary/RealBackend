<?php
namespace backend\models;

use Yii;
use yii\base\Model;

/**
 * Login form
 */
class MasterProfitForm extends Model
{
    //蜜播id、昵称、日期、是否签约、当天实际收入票数、当天总收入票数
    public $record_id;
    public $client_id;
    public $client_no;
    public $nick_name;
    public $date;
    public $is_contract;
    public $real_ticket_profit;
    public $sum_ticket_profit;

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
            'client_no'=>'蜜播号',
            'nick_name'=>'昵称',
            'date'=>'日期',
            'is_contract'=>'签约',
            'real_ticket_profit'=>'当前收入实际票数',
            'sum_ticket_profit'=>'当前收入总票数'
        ];
    }

    /**
     * 获取状态名称
     * @return string
     */
    public static function GetStatusName($status)
    {
        $rst = '';
        switch(intval($status))
        {
            case 1:
                $rst = '已受理';
                break;
            case 2:
                $rst = '已审核';
                break;
            case 3:
                $rst = '已打款';
                break;
            case 4:
                $rst = '审核被拒绝';
                break;
            default:
                $rst = '未知类型';
                break;
        }
        return $rst;
    }

}
