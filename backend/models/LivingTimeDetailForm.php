<?php
namespace backend\models;

use Yii;
use yii\base\Model;
use yii\log\Logger;

/**
 * 主播直播时间详细model
 */
class LivingTimeDetailForm extends Model
{
    public $client_id;
    public $client_no;
    public $nick_name;
    public $living_before_id;
    public $create_time;
    public $finish_time;
    public $living_time;

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
            'living_before_id'=>'开播次数',
            'create_time'=>'开播时间',
            'finish_time'=>'结束时间',
            'living_time'=>'直播时间'
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
