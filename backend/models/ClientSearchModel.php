<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/4/6
 * Time: 11:14
 */

namespace backend\models;


use yii\base\Model;

class ClientSearchModel extends Model
{
    public $status;
    public $is_inner;
    public $account_id;
    public $nick_name;
    public $phone_no;
    public $centification_level;
    public $create_time;
    public $balance;
    public $account_info_id;

    public function rules()
    {
        return [
            [['account_id', 'centification_level','account_info_id','status', 'is_inner'], 'integer'],
            [['nick_name','phone_no', 'create_time','balance'], 'safe'],
        ];
    }

    public static function GetStatusName($status)
    {
        switch(intval($status))
        {
            case 0:
                $rst = '禁用';
                break;
            case 1:
                $rst = '正常';
                break;
            case 2:
                $rst = '审核中';
                break;
            default:
                $rst = '未知';
                break;
        }
        return $rst;
    }

    public static function GetInnerName($is_inner)
    {
        switch(intval($is_inner))
        {
            case 1:
                $rst = '否';
                break;
            case 2:
                $rst = '是';
                break;
            default:
                $rst = '未知';
                break;
        }
        return $rst;
    }

    /**
     * 获取认证等级名称
     * @param $centification_level
     * @return string
     */
    public static function GetLevelName($centification_level)
    {
        $rst = '';
        switch(intval($centification_level))
        {
            case 0:
                $rst = '未认证';
                break;
            case 1:
                $rst = '初级认证';
                break;
            case 2:
                $rst = '中级认证';
                break;
            case 3:
                $rst = '高级认证';
                break;
            default:
                $rst = '未认证';
                break;
        }
        return $rst;
    }
} 