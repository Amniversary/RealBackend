<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 15-12-13
 * Time: 下午9:36
 */

namespace frontend\zhiboapi\v3;

use frontend\business\ClientUtil;
use frontend\zhiboapi\IApiExcute;
use frontend\business\ApiCommon;

/**
 * Class 根据id或昵称搜索用户
 * @package frontend\zhiboapi\v3
 */
class ZhiBoSearchUser implements IApiExcute
{
    /**
     * 检查参数合法性
     * @param string $error
     */
    private function check_param_ok($dataProtocal,&$error='')
    {
        if(!isset($dataProtocal['data']['key_word']))
        {
            $error = '关键字，不能为空';
            return false;
        }
        $fields = ['unique_no','register_type','page_no','page_size'];//'wish_type_id',
        $fieldLabels = ['唯一id','登录类型','页码','每页记录数'];//'愿望类别id',
        $len =count($fields);
        for($i = 0; $i <$len; $i ++)
        {
            if (!isset($dataProtocal['data'][$fields[$i]]) || empty($dataProtocal['data'][$fields[$i]])) {
                $error = $fieldLabels[$i] . '，不能为空';
                return false;
            }
        }
        if(intval($dataProtocal['data']['page_no']) <= 0)
        {
            $error = '页码数不正确';
            return false;
        }
        if(intval($dataProtocal['data']['page_size']) <= 0)
        {
            $error = '页记录数不正确';
            return false;
        }
        return true;
    }

    public function excute_action($dataProtocal, &$rstData,&$error, $extendData= array())
    {
//\Yii::getLogger()->log(var_export($dataProtocal, true),Logger::LEVEL_ERROR);
        $error = '';
        if(!$this->check_param_ok($dataProtocal, $error))
        {
            return false;
        }
        $deviceNo = '';
        $uniqueNo= '';
        $registerType='';
        $deviceType='';
        if(!ApiCommon::GetBaseInfoFromProtocol($dataProtocal, $deviceNo, $uniqueNo,$registerType,$deviceType,$error))
        {
            return false;
        }
        $loginInfo = null;
        if(!ApiCommon::GetLoginInfo($uniqueNo,$loginInfo, $error))
        {
            return false;
        }
        $user_id  = $loginInfo['user_id'];
        $passParams = $dataProtocal['data'];
        unset($passParams['unique_no']);
        unset($passParams['register_type']);
        $page_no = $passParams['page_no'];
        $page_size = $passParams['page_size'];
        $key_word = $passParams['key_word'];

        $friendsList = ClientUtil::SearchUser($key_word,$page_no,$page_size,$user_id);
        //$out = json_encode($friendsList);
        $rstData['has_data'] = '1';
        $rstData['data_type']="jsonarray";
        $rstData['data']=$friendsList;
        //\Yii::getLogger()->log(var_export($out, true),Logger::LEVEL_ERROR);
        //根据经度、纬度获取地理信息
        return true;
    }
} 