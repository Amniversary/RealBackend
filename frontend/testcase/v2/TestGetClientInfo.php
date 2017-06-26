<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/17
 * Time: 17:25
 */
namespace frontend\testcase\v2;

/**
 * 获取个人信息
 * Class TestGetClientInfo
 * @package frontend\testcase\v2
 */
class TestGetClientInfo
{
    public static function  GetClientInfo($data_params)
    {
        $data = [
            "unique_no" => $data_params['unique_no'],
            "register_type" => $data_params['register_type'],
            "user_id" => $data_params['user_id'],
            "fields" => [],
        ];
        $rstData = Common::PackagingParams($data_params['token'],$data_params['sign_key'],$data_params['crypt_key'],'get_client_info',$data,$data_params['device_no'],
            $data_params['device_type'],'json');

        return $rstData;
    }
}