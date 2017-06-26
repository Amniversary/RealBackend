<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/26
 * Time: 21:37
 */

namespace backend\models;


use yii\base\Model;

class ActivityStatisticForm extends Model
{
    public $activity_id;
    public $user_number;
    public $record_number;
    public $title;

    public function rules()
    {
        return [
            [['activity_id','user_number','record_number'], 'integer'],
        ];
    }

} 