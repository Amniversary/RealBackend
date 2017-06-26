<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 15-12-21
 * Time: 下午10:16
 */

namespace frontend\business;

use common\models\BusinessCheck;

class BusinessCheckUtil
{
    /**
     * 根据id后去审核记录
     * @param $business_check_id
     */
    public static function GetBusinessCheckById($business_check_id)
    {
        return BusinessCheck::findOne(['business_check_id'=>$business_check_id]);
    }

    /**
     * 根据id后去审核记录
     * @param $business_check_id
     */
    public static function GetBusinessCheckByRelate_id($relate_id,$business_type=1)
    {
        return BusinessCheck::findOne(['and',['relate_id'=>$relate_id,'business_type'=>$business_type]]);
    }
    /**
     * 获取初级认证审核信息模型
     * @param $record_id，初级认证信息id
     */
    public static function GetBusinessCheckModelForBaseCertification($record_id)
    {
        $checkAttrs = array(
            'relate_id'=>$record_id, //借款单保存后再做处理
            'business_type'=>'8',//1美愿基金借款打赏  2 账户余额提现  4 美愿基金提现  8初级认证  16中级认证
            'status'=>'0',
            'check_result_status'=>'0',
            'create_time'=>date('Y-m-d H:i:s')
        );
        //审核记录
        $checkBusinessModel = new BusinessCheck();
        $checkBusinessModel->attributes = $checkAttrs;
        return $checkBusinessModel;
    }

    /**
     * 获取中级认证审核模型
     * @param $record_id
     * @return BusinessCheck
     */
    public static function GetBusinessCheckModelForIntermediateCertification($record_id)
    {
        $checkAttrs = array(
            'relate_id'=>$record_id, //中级认证记录id
            'business_type'=>'16',//1美愿基金借款打赏  2 账户余额提现  4 美愿基金提现  8初级认证  16中级认证
            'status'=>'0',
            'check_result_status'=>'0',
            'create_time'=>date('Y-m-d H:i:s')
        );
        //审核记录
        $checkBusinessModel = new BusinessCheck();
        $checkBusinessModel->attributes = $checkAttrs;
        return $checkBusinessModel;
    }


    /**
     * 获取美元基金借款审核模型
     * @param $record_id
     * @return BusinessCheck
     */
    public static function GetBusinessCheckModelForFundBorrow()
    {
        $checkAttrs = array(
            'relate_id'=>'', //中级认证记录id
            'business_type'=>'3',//1美愿基金借款打赏  2 账户余额提现  3 美愿基金提现  8初级认证  16中级认证
            'status'=>'0',
            'check_result_status'=>'0',
            'create_time'=>date('Y-m-d H:i:s')
        );
        //审核记录
        $checkBusinessModel = new BusinessCheck();
        $checkBusinessModel->attributes = $checkAttrs;
        return $checkBusinessModel;
    }

    /**
     * 获取审核记录模型
     * @param $check_type
     * @param $relate_id
     * @param $user
     */
    public static function GetBusinessCheckModelNew($check_type,$relate_id,$user)
    {
        $checkAttrs = array(
            'relate_id'=>$relate_id, //中级认证记录id
            'business_type'=>$check_type,//1美愿基金借款打赏  2 账户余额提现  3 美愿基金提现  8初级认证  16中级认证
            'status'=>'0',
            'check_result_status'=>'0',
            'create_time'=>date('Y-m-d H:i:s'),
            'create_user_id'=>$user->client_id,
            'create_user_name'=>$user->nick_name,
        );
        //审核记录
        $checkBusinessModel = new BusinessCheck();
        $checkBusinessModel->attributes = $checkAttrs;
        return $checkBusinessModel;
    }


    /**
     * 通过审核表ID得到票提现审核记录信息
     * @param $relate_id
     * @return null|static
     */
    public static function GetBusinessCheckInfo($relate_id,$status=0){
         $result = BusinessCheck::findOne(['business_type'=>1 ,'relate_id'=>$relate_id,'check_result_status'=>$status]);
        return $result;
    }

    /**
     * 通过审核表ID得到票提现审核记录信息
     * @param $relate_id
     * @return null|static
     */
    public static function GetFinanceBusinessCheckInfo($relate_id,$status=1){
        $result = BusinessCheck::findOne(['business_type'=>1 ,'relate_id'=>$relate_id,'check_result_status'=>$status]);
        return $result;
    }


} 