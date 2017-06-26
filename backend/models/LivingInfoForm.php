<?php
namespace backend\models;

use Yii;
use yii\base\Model;

/**
 *  form
 */
class LivingInfoForm extends Model
{
    public $client_id;
    public $unique_no;
    public $client_no;
    public $living_id;
    public $push_url;
    public $pull_http_url;
    public $pull_rtmp_url;
    public $pull_hls_url;
    public $qiniu_info;

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
            'client_id' => '用户ID',
            'unique_no' => '用户唯一号',
            'client_no' => '蜜播ID',
            'living_id' => '直播ID',
            'push_url' => '推流地址',
            'pull_http_url' => 'pull_http_url',
            'pull_rtmp_url' => 'pull_rtmp_url',
            'pull_hls_url' => 'pull_hls_url',
            'qiniu_info' => '七牛流'
        ];
    }

}
