<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2015/12/18
 * Time: 10:06
 */

namespace frontend\business;
use common\components\PhpLock;
use common\models\City;

class CityUtil {

    /**
     * 获取省的下拉框数据
     */
    public static function GetProvinceListForDropdownList($flush =false)
    {
        $cityListStr = \Yii::$app->cache->get('dropdownlist_province');
        if($cityListStr === false || $flush)
        {
            $lock = new PhpLock('dropdownlist_province');
            $lock->lock();
            $cityListStr = \Yii::$app->cache->get('dropdownlist_province');
            if($cityListStr === false || $flush)
            {
                $cityList = City::find()->select(['city_id','city_name'])->where(['city_type'=>'1'])->all();
                $rst = [];
                foreach($cityList as $city)
                {
                    $rst[$city->city_id] = $city->city_name;
                }
                $cityListStr = serialize($rst);
                \Yii::$app->cache->set('dropdownlist_province',$cityListStr);
            }
            $lock->unlock();
        }
        return unserialize($cityListStr);
    }

    /**
     * 根据省获取市
     * @param $province_id
     * @return mixed
     */
    public static function GetCityDataForDropdownList($province_id,$flush =false)
    {
        $key = 'dropdownlist_city_'.$province_id;
        $cityListStr = \Yii::$app->cache->get($key);
        if($cityListStr === false || $flush)
        {
            $lock = new PhpLock($key);
            $lock->lock();
            $cityListStr = \Yii::$app->cache->get($key);
            if($cityListStr === false ||$flush)
            {
                $cityList = City::find()->select(['city_id','city_name'])->where(['city_type'=>'2','pid'=>$province_id])->all();
                $rst = [];
                foreach($cityList as $city)
                {
                    $rst[$city->city_id] = $city->city_name;
                }
                $cityListStr = serialize($rst);
                \Yii::$app->cache->set($key,$cityListStr);
            }
            $lock->unlock();
        }
        return unserialize($cityListStr);
    }

    /**
     * 获取城市下拉框明细数据
     * @param $province_id
     */
    public static function GetCityDropdownItems($province_id,$flush =false)
    {
        $key = 'dropdownlist_city_items_'.$province_id;
        $str = \Yii::$app->cache->get($key);
        if($str === false || $flush)
        {
            $lock = new PhpLock($key);
            $lock->lock();
            $str = \Yii::$app->cache->get($key);
            if($str === false || $flush)
            {
                $dataList = self::GetCityDataForDropdownList($province_id, $flush);
                $str = '';
                foreach($dataList as $item_key => $item_value)
                {
                    $str .= sprintf('<option value="%s">%s</option>',$item_key,$item_value);
                }
                \Yii::$app->cache->set($key,$str);
            }
            $lock->unlock();
        }
        return $str;
    }

    /**
     * 根据市获取区信息
     * @param $province_id
     * @return mixed
     */
    public static function GetAreaDataForDropdownList($city_id,$flush =false)
    {
        $key = 'dropdownlist_area_'.$city_id;
        $cityListStr = \Yii::$app->cache->get($key);
        if($cityListStr === false || $flush)
        {
            $lock = new PhpLock($key);
            $lock->lock();
            $cityListStr = \Yii::$app->cache->get($key);
            if($cityListStr === false || $flush)
            {
                $cityList = City::find()->select(['city_id','city_name'])->where(['city_type'=>'4','pid'=>$city_id])->all();
                $rst = [];
                foreach($cityList as $city)
                {
                    $rst[$city->city_id] = $city->city_name;
                }
                $cityListStr = serialize($rst);
                \Yii::$app->cache->set($key,$cityListStr);
            }
            $lock->unlock();
        }
        return unserialize($cityListStr);
    }


    /**
     * 获取区域下拉框明细数据
     * @param $province_id
     */
    public static function GetAreaDropdownItems($city_id,$flush =false)
    {
        $key = 'dropdownlist_area_items_'.$city_id;
        $str = \Yii::$app->cache->get($key);
        if($str === false || $flush)
        {
            $lock = new PhpLock($key);
            $lock->lock();
            $str = \Yii::$app->cache->get($key);
            if($str === false || $flush)
            {
                $dataList = self::GetAreaDataForDropdownList($city_id,$flush);
                $str = '';
                foreach($dataList as $item_key => $item_value)
                {
                    $str .= sprintf('<option value="%s">%s</option>',$item_key,$item_value);
                }
                \Yii::$app->cache->set($key,$str);
            }
            $lock->unlock();
        }
        return $str;
    }

    /**
     * 获取省市区记录
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function GetCityList()
    {
        return City::find()->all();
    }

    /**
     * 格式化省市区内容
     * @param $cityRecordList
     * @return array
     */
    public static function GetFormateCity($cityRecordList)
    {
        $out=[];
        if(!isset($cityRecordList) || empty($cityRecordList))
        {
            return $out;
        }
        foreach($cityRecordList as $oneCity)
        {
           // $oneCity = new City();
            $ary =[
                'city_id'=>$oneCity->city_id,
                'city_name'=>$oneCity->city_name,
                'pid'=>$oneCity->pid,
                'help_code'=>$oneCity->help_code,
                'all_code'=>$oneCity->all_code,
                'city_type'=>$oneCity->city_type,
                'longitude'=>$oneCity->longitude,
                'latitude'=>$oneCity->latitude
            ];

            $out[] = $ary;
        }
        return $out;
    }
} 