<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/19
 * Time: 16:29
 */
namespace backend\models;

use Yii;
use yii\base\Model;

class StatisticLivingmaster extends Model{

    public $share_date;
    public $living_master_id;
    public $living_master_share_no;
    public $audience_share_no;
    public $total_no;

    public $client_id;
    public $client_no;
    public $nick_name;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['share_date', 'living_master_id'], 'required'],
            [['share_date'], 'safe'],
            [['living_master_id', 'living_master_share_no', 'audience_share_no', 'total_no','client_id','client_no'], 'integer'],
            [['remark1', 'remark2', 'remark3', 'remark4','nick_name'], 'string', 'max' => 100],
        ];
    }

    public function  attributeLabels()
    {
        return [
            'share_date'=>'分享日期',
            'living_master_id' => '主播ID',
            'audience_share_no'=>'观众分享次数',
            'living_master_share_no'=>'主播分享次数',
            'total_no'=>'总次数',
            'client_id' => '蜜播 ID',
            'client_no' => '主播 ID',
            'nick_name' => '主播名称',
        ];
    }
}
