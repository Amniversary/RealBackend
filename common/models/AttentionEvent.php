<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Query;

/**
 * This is the model class for table "{{%_attention_event}}".
 *
 * @property integer $record_id
 * @property integer $event_id
 * @property integer $app_id
 * @property integer $menu_id
 * @property string $content
 * @property integer $msg_type
 * @property string $title
 * @property string $description
 * @property string $url
 * @property string $picurl
 * @property string $global
 * @property integer $update_time
 * @property string $create_time
 * @property string $video
 * @property integer $order_no
 * @property integer $flag
 * @property string $remark1
 * @property string $remark2
 * @property string $remark3
 * @property string $remark4
 */
class AttentionEvent extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%_attention_event}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['app_id', 'msg_type', 'update_time','flag', 'event_id','global','order_no','menu_id'], 'integer'],
            [['create_time','title','description','url','picurl','video','remark1'], 'safe'],
            [[ 'remark2', 'remark3', 'remark4'], 'string', 'max' => 100],
            [['content'], 'string', 'max'=>1000],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'record_id' => '记录 ID',
            'app_id' => '公众号',
            'event_id' => '事件 ID',
            'menu_id' => '菜单 ID',
            'content' => '文本内容',
            'msg_type' => '消息类型',
            'create_time' => '创建时间',
            'title' => '图文标题',
            'description' => '内容描述',
            'url' => '外链Url',
            'picurl' => '图片Url',
            'global' => '是否全局',
            'order_no'=>'排序号',
            'video'=>'音频url',
            'update_time' => '更新media_id的时间',
            'flag' => '0 关注类型  1关键词类型 2自定义菜单',
            'remark1' => 'Remark1',
            'remark2' => 'Remark2',
            'remark3' => 'Remark3',
            'remark4' => 'Remark4',
        ];
    }


    /**
     * 根据记录号获取公众号对应昵称
     * @param $record_id
     * @return string
     */
    public static function getKeyAppId($record_id)
    {
        $query = (new Query())
            ->select(['record_id','nick_name'])
            ->from('wc_authorization_list')
            ->where(['record_id'=>$record_id,'status'=>'1'])->one();

        return $query['nick_name'];
    }

    /**
     * 获取消息类型
     * @param $status
     * @return string
     */
    public function getMsgType($status)
    {
        switch (intval($status)){
            case 0: $rst = '文本消息'; break;
            case 1: $rst = '图文消息'; break;
            case 2: $rst = '图片消息'; break;
            case 3: $rst = '语音消息'; break;
            default: $rst = '未知类型'; break;
        }
        return $rst;
    }
}
