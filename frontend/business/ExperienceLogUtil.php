<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/4/28
 * Time: 20:28
 */

namespace frontend\business;


use common\models\ExperienceLog;

class ExperienceLogUtil
{
    /**
     * 创建经验日志
     * @param $data
     * @param $error
     */
    public static function CreateExperienceLog($data,&$error)
    {
        $source_type = intval($data['source_type']);
        if(!isset($data['create_time']))
        {
            $data['create_time'] = date('Y-m-d H:i:s');
        }
        if(!in_array($source_type,[1,2,3,4]))
        {
            $error = '类型不正确';
            return false;
        }
        if($source_type === 1)//送礼物
        {
            $fields = ['device_type','user_id', 'source_type', 'living_before_id','change_rate', 'experience','create_time','gift_value','relate_id'];
            $fieldLabels = ['设备类别','用户id', '操作类型', '直播id','转化率', '经验值','创建时间','豆值','打赏记录id'];
        }
        else if($source_type === 4)
        {
            $fields = ['device_type','user_id', 'source_type', 'experience','create_time'];
            $fieldLabels = ['设备类别','用户id', '操作类型',  '经验值豆值','创建时间'];
        }
        else //直播
        {
            $fields = ['device_type','user_id', 'source_type', 'living_before_id','change_rate', 'experience','create_time', 'starttime', 'endtime', 'owner'];
            $fieldLabels = ['设备类别','用户id', '操作类型', '直播序号id','转化率', '经验值豆值','创建时间',  '开始时间','结束时间','直播身份'];
        }
        $len = count($fields);
        for($i = 0; $i < $len; $i ++)
        {
            if(empty($data[$fields[$i]]))
            {
                $error = $fieldLabels[$i].'不能为空';
                return false;
            }
        }
        $model = new ExperienceLog();
/*        $living_id = $data['living_id'];
        $is_to_expirence = $data['is_to_expirence'];*/
        unset($data['living_id']);
        unset($data['is_to_expirence']);
        $model->attributes = $data;
        if(!$model->save())
        {
            $error = '经验日志保存异常';
            return false;
        }
//不需要做处理
/*        if(!empty($is_to_expirence)){
            if(!LivingUtil::SetExpirenceYes($living_id)){
                $error = '经验转换失败';
                return false;
            }
        }*/

        return true;
    }
} 