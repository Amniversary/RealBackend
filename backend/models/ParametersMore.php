<?php

namespace backend\models;

use Yii;
use yii\base\Model;

class ParametersMore extends Model{

    public $order_no;
    public $quality;
    public $fps;
    public $profilelevel;
    public $video_bit_rate;
    public $width;
    public $height;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_no','fps','profilelevel','video_bit_rate','width','height','order_no'], 'integer'],
            [['quality'], 'string'],

        ];
    }

    public function  attributeLabels()
    {
        return [
            'order_no'=>'自增 ID',
            'fps' => '视频帧数',
            'profilelevel'=>'编码耗能',
            'video_bit_rate'=>'传输码率',
            'width'=>'视频宽度',
            'height' => '视频高度',
            'quality' => '参数ID',
            'order_no' => '排序号'
        ];
    }
}
