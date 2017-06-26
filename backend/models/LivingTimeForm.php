<?php
namespace backend\models;

use Yii;
use yii\base\Model;
use yii\log\Logger;

/**
 * Login form
 */
class LivingTimeForm extends Model
{
    //蜜播id、昵称、日期、是否签约、直播时间
    public $record_id;
    public $client_id;
    public $client_no;
    public $nick_name;
    public $date;
    public $is_contract;
    public $living_second;
    public $start_date;

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
            'client_id'=>'用户id',
            'client_no'=>'蜜播号',
            'nick_name'=>'昵称',
            'date'=>'日期',
            'is_contract'=>'签约',
            'living_second'=>'直播时间',
            'start_date'=>'起始时间'
        ];
    }

    /**
     * 获取状态名称
     * @return string
     */
    public static function GetIsContractName($status)
    {
        $rst = '';
        switch(intval($status))
        {
            case 1:
                $rst = '未签约';
                break;
            case 2:
                $rst = '已签约';
                break;
            default:
                $rst = '未知类型';
                break;
        }
        return $rst;
    }

}
