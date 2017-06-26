<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/13
 * Time: 9:22
 */
namespace backend\models;

use Yii;
use yii\base\Model;

class RobotInfo extends Model{

    public $create_robot_no;
    public $audience_robot_no;
    public $client_no;
    public $nick_name;
    public $user_id;
    public $record_id;
    public $client_id;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['client_no','audience_robot_no','create_robot_no','user_id','record_id','client_id'], 'integer'],
            [['remark1', 'remark2', 'remark3', 'remark4','nick_name'], 'string', 'max' => 100],
            [['audience_robot_no'],'integer','min'=>50,'max' => 100],
            [['create_robot_no'],'integer','min'=>5,'max' => 50]
        ];
    }

    public function  attributeLabels()
    {
        return [
            'client_id'=>'主播 ID',
            'client_no'=>'蜜播 ID',
            'create_robot_no' => '创建房间时直播机器人人数',
            'audience_robot_no'=>'观众进入时直播机器人比例数',
            'nick_name'=>'用户昵称',
            'user_id'=>'用户 ID',
            'record_id' => '自增 id'
        ];
    }
}
