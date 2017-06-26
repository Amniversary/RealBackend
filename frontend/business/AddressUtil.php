<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2015/12/24
 * Time: 9:38
 */

namespace frontend\business;


use common\models\UserAddress;
use Faker\Provider\nl_BE\Address;
use yii\base\Exception;
use yii\log\Logger;

class AddressUtil
{

    /**
     * 获取区域信息
     */
    public static function GetAreaInfo()
    {

    }

    /**
     * 获取用户默认地址
     * @param $user_id
     * @return null|static
     */
    public static function GetDefaultAddressByUserId($user_id)
    {
        return UserAddress::findOne(['user_id'=>$user_id,'is_default'=>'1']);
    }

    /**
     * 获取默认地址，排除现有的id
     * @param $user_id
     * @param $self_address_id
     * @return null|static
     */
    public static function GetDefaultAddressByUserIdExcludeSelf($user_id, $self_address_id)
    {
        return UserAddress::findOne(['and',['user_id'=>$user_id,'is_default'=>'1'],'user_address_id<>'.$self_address_id]);
    }

    /**
     * 获取一个用户地址
     * @param $user_id
     */
    public static function GetOneAddressByUserId($user_id)
    {
        return UserAddress::findOne(['user_id'=>$user_id]);
    }

    /**
     * 删除地址
     * @param $user_address_id 地址id
     * @param $error
     */
    public static function DelAddress($user_address_id, &$error)
    {
        $address = self::GetAddressInfoById($user_address_id);
        if(!isset($address))
        {
            $error = '记录不存在';
            return false;
        }
        if($address->delete() === false)
        {
            $error = '删除异常';
            return false;
        }
        return true;
    }

    /**
     * 根据id获取地址信息
     * @param $address_id
     * @return null|static
     */
    public static function GetAddressInfoById($address_id)
    {
        return UserAddress::findOne([
            'user_address_id'=>$address_id
        ]);
    }

    /**
     * 修改地址信息
     * @param $attrs 属性
     * @param $error 返回的错误
     * @return bool
     */
    public static function ModifyAddress($attrs,&$error)
    {
        $address_id = $attrs['user_address_id'];
        $addressInfo = self::GetAddressInfoById($address_id);
        if(!isset($addressInfo))
        {
            $error = '该地址记录不存在';
            \Yii::getLogger()->log($error.' id:'.$address_id, Logger::LEVEL_ERROR);
            return false;
        }
        $is_default = $attrs['is_default'];
        if($is_default == '1')
        {
            $existDefaultAddress = AddressUtil::GetDefaultAddressByUserIdExcludeSelf($addressInfo->user_id,$address_id);
        }
        unset($attrs['user_address_id']);
        $addressInfo->attributes = $attrs;
        $trans = \Yii::$app->db->beginTransaction();
        try
        {
            if(isset($existDefaultAddress))
            {
                $existDefaultAddress->is_default = 0;
                if(!$existDefaultAddress->save())
                {
                    \Yii::getLogger()->log(var_export($existDefaultAddress->getErrors(), true),Logger::LEVEL_ERROR);
                    throw new Exception('修改默认地址失败');
                }
            }
            if(!$addressInfo->save())
            {
                \Yii::getLogger()->log(var_export($addressInfo->getErrors(), true),Logger::LEVEL_ERROR);
                throw new Exception('地址信息修改失败');
            }
            $trans->commit();
        }
        catch(Exception $e)
        {
            $error = $e->getMessage();
            $trans->rollBack();
            return false;
        }

        return true;
    }

    /**
     * 新增地址
     * @param $attrs，地址属性
     * @param $user_id 用户id
     * @param $error
     */
    public static function AddAddress($attrs,$user_id,&$error)
    {
        $attrs['user_id'] = $user_id;
        $ad = new UserAddress();
        $ad->attributes = $attrs;
        $is_default = $ad->is_default;
        if($is_default == '1')
        {
            $existDefaultAddress = AddressUtil::GetDefaultAddressByUserId($user_id);
        }
        $trans = \Yii::$app->db->beginTransaction();
        try
        {
            if(isset($existDefaultAddress))
            {
                $existDefaultAddress->is_default = 0;
                if(!$existDefaultAddress->save())
                {
                    \Yii::getLogger()->log(var_export($existDefaultAddress->getErrors(), true),Logger::LEVEL_ERROR);
                    throw new Exception('修改非默认地址失败');
                }
            }
            if(!$ad->save())
            {
                \Yii::getLogger()->log(var_export($ad->getErrors(), true),Logger::LEVEL_ERROR);
                throw new Exception('地址保存失败');
            }
            $trans->commit();
        }
        catch(Exception $e)
        {
            $error = $e->getMessage();
            $trans->rollBack();
            return false;
        }
        return true;
    }
    /**
     * 获取地址记录
     */
    public static function GetAddressListByUserId($user_id)
    {
        return UserAddress::findAll(['user_id'=>$user_id]);
    }

    /**
     * 获取已经格式化的用户记录
     * @param $user_id
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function GetFormatedAddressListByUserId($user_id)
    {
        return UserAddress::find()->select(['user_address_id','province','city','area','address','contract_user','contract_call','is_default'])
            ->where(['user_id'=>$user_id])->all();
    }

    /**
     * 格式化地址信息，输出到列表
     */
    public static function GetFormateAddress($recordList)
    {
        $out = [];
        if(empty($recordList))
        {
            return $recordList;
        }
        foreach($recordList as $oneRecord)
        {
            $ary=[
                'user_address_id'=>$oneRecord->user_address_id,
                'province'=>$oneRecord->province,
                'city'=>$oneRecord->city,
                'area'=>$oneRecord->area,
                'address'=>$oneRecord->address,
                'contract_user'=>$oneRecord->contract_user,
                'contract_call'=>$oneRecord->contract_call,
                'is_default'=>$oneRecord->is_default
            ];
            $out[] = $ary;
        }
        return $out;
            /*
adress_id
province
city
area
address
contract_name
contract_phone
is_default
             */
    }
} 