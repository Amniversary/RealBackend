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

class ScoreGift extends Model{

    public $activity_id;
    public $title;
    public $start_time;
    public $end_time;
    public $activity_status;
    public $template_id;
    public $template_title;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['start_time', 'end_time'], 'safe'],
            [['activity_status', 'template_id','activity_id'], 'integer'],
            [['title','template_title'], 'string', 'max' => 100],
        ];
    }

    public function  attributeLabels()
    {
        return [
            'start_time'=>'开始时间',
            'end_time' => '结束时间',
            'activity_status'=>'活动状态',
            'template_id'=>'排行榜模板ID',
            'title'=>'活动标题',
            'activity_id' => '活动 ID',
            'template_title' => '排行榜模板标题'
        ];
    }
}
