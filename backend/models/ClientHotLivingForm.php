<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/6/16
 * Time: 10:12
 */

namespace backend\models;


use yii\base\Model;

class ClientHotLivingForm extends Model
{
    public $client_id;
    public $living_id;
    public $client_no;
    public $living_num;
    public $living_title;
    public $nick_name;
    public $status;
    public $is_official;
    public $city;
    public $order_no;
    public $hot_num;
    public $s1;
    public $is_contract;
    public $living_type;

    public function rules()
    {
        return [
            [['living_id','client_no','status','living_type','is_contract'], 'integer'],
            [['nick_name','living_title','living_title'], 'safe'],
        ];
    }

    public function  attributeLabels()
    {
        return [
            'living_id' =>'直播ID',
            'client_id' =>'蜜播ID',
            'living_title'=>'直播标题',
            'nick_name'=>'用户昵称',
            'status'=>'直播状态',
            'is_official'=>'是否官方',
            'city'=>'城市',
            'is_contract' => '是否签约'
        ];
    }

    /**
     * 获取直播状态
     */
    public static function GetLivingStatus($status)
    {
        switch(intval($status))
        {
            case 0:
                $rst = '结束';
                break;
            case 1:
                $rst = '暂停';
                break;
            case 2:
                $rst = '直播';
                break;
            case 3:
                $rst = '禁用';
                break;
            default:
                $rst = '未知状态';
                break;
        }
        return $rst;
    }


    /**
     * 获取直播设备类型
     * @param $device_type
     */
    public static function GetDeviceType($device_type)
    {
        switch(intval($device_type))
        {
            case 1:
                $rst = 'Android';
                break;
            case 2:
                $rst = 'IOS';
                break;
            default:
                $rst = '其他';
                break;
        }
        return $rst;
    }

    /**
     * 获取用户状态
     * @param $status
     */
    public static function GetStatus($status)
    {
        switch(intval($status))
        {
            case 0:
                $rst = '禁用';
                break;
            case 1:
                $rst = '正常';
                break;
            default:
                $rst = '未知属性';
                break;
        }
        return $rst;

    }
} 