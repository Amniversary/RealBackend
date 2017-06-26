<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2015/12/29
 * Time: 13:09
 */

namespace frontend\business;


use common\models\SchoolInfo;

class SchoolInfoUtil
{
    /**
     * 根据姓名返回学校信息
     * @param $school_name
     */
    public static function GetSchoolInfoByName($school_name)
    {
        return SchoolInfo::findOne(['school_name'=>$school_name]);
    }

    /**
     * 获取所有学校记录
     * @return static[]
     */
    public static function GetAllSchool()
    {
        return SchoolInfo::findAll(['status'=>'1']);
    }

    /**
     * 格式化学校信息
     * @param $schoolList
     */
    public static function GetFormateSchoolList($schoolList)
    {
        $out = [];
        if(empty($schoolList))
        {
            return $out;
        }
        foreach($schoolList as $school)
        {
            $ary = [
                'school_id'=>$school->school_id,
                'school_name'=>$school->school_name,
                'province'=>$school->province,
                'city'=>$school->city
            ];
            $out[] = $ary;
        }
        return $out;
    }
} 