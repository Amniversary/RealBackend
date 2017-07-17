<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/7/13
 * Time: 下午4:54
 */

namespace backend\models;


use yii\base\Model;

class PublicListForm extends Model
{
    public $record_id;
    public $nick_name;
    public $service_type_info;
    public $verify_type_info;
    public $head_img;
    public $new_user;
    public $net_user;
    public $count_user;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [];
    }

    public function  attributeLabels()
    {
        return [
            'record_id'=>'公众号 ID',
            'nick_name'=>'公众号名称',
            'service_type_info'=>'公众号类型',
            'verify_type_info'=>'认证类型',
            'new_user'=>'新增人数',
            'net_user'=>'净增人数',
            'count_user'=>'总粉丝数'
        ];
    }

}