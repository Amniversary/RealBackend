<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/17
 * Time: 17:25
 */
namespace frontend\testcase\v2;


/**
 * 退出直播间
 * Class TestQuitRoom
 * @package frontend\testcase\v2
 */
class TestQuitRoom
{
    public static function  QuitRoom($data_params)
    {
        $data = [
            "unique_no" => $data_params['unique_no'],
            "register_type" => $data_params['register_type'],
            "living_id" => $data_params['living_id']
        ];
        $rstData = Common::PackagingParams($data_params['token'],$data_params['sign_key'],$data_params['crypt_key'],'quit_room',$data,$data_params['device_no'],
            $data_params['device_type'],'json');

        return $rstData;
    }
}