<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/4
 * Time: 14:45
 */

namespace backend\models;


use frontend\business\ClientQiNiuUtil;
use yii\base\Model;

class QiniuClientParamsForm extends Model
{
    public $relate_id;
    public $client_no;
    public $user_id;
    public $quality_id;
    public $fps;
    public $profilelevel;
    public $video_bit_rate;
    public $width;
    public $height;
    public $parameters_more;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id' ], 'integer'],
            [['user_id','client_no'], 'safe'],
        ];
    }


    public function attributeLabels()
    {
        return [
            'relate_id' => '主键自增',
            'user_id' => '用户 ID',
            'client_no'=>'用户账号',
            'quality_id' => '参数模型 ID',
            'fps' => '视频帧数',
            'profilelevel' => '平均编码码率',
            'video_bit_rate' => '传输码率',
            'width' => '屏幕宽度',
            'height' => '屏幕高度',
            'parameters_more' => '更多参数'
        ];
    }

    /**
     * 获取用户对应参数模型
     * @param $quality_id
     * @return mixed
     */
    public static function GetLivingParams($quality_id)
    {
        $living_params = ClientQiNiuUtil::GetLivingParameters();
        return $living_params[$quality_id];
    }
} 