<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 15-12-13
 * Time: 下午9:36
 */

namespace frontend\zhiboapi\v3;

use frontend\business\ChatFriendsUtil;
use frontend\business\ChatPersonGroupUtil;
use frontend\zhiboapi\IApiExcute;
use frontend\business\ApiCommon;
use yii\log\Logger;

/**
 * Class 获取管理员
 * @package frontend\zhiboapi\v3
 */
class ZhiBoGetManagerList implements IApiExcute
{
    /**
     * 检查参数合法性
     * @param string $error
     */
    private function check_param_ok($dataProtocal,&$error='')
    {
        $fields = ['unique_no','register_type'];//'wish_type_id',
        $fieldLabels = ['唯一id','登录类型'];//'愿望类别id',
        $len =count($fields);
        for($i = 0; $i <$len; $i ++)
        {
            if (!isset($dataProtocal['data'][$fields[$i]]) || empty($dataProtocal['data'][$fields[$i]]))
            {
                $error = $fieldLabels[$i] . '，不能为空';
                return false;
            }
        }
        return true;
    }

    public function excute_action($dataProtocal, &$rstData,&$error, $extendData= array())
    {
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
        //\Yii::getLogger()->log('self_user_id:'.$loginInfo['user_id'].' user_id:'.$passParams['user_id'],Logger::LEVEL_ERROR);
        $friendsList = ChatPersonGroupUtil::GetChatRoomManager($user_id);
        //$out = json_encode($friendsList);
        $rstData['has_data'] = '1';
        $rstData['data_type']="jsonarray";
        $rstData['data']=$friendsList;
        //\Yii::getLogger()->log(var_export($out, true),Logger::LEVEL_ERROR);
        //根据经度、纬度获取地理信息
        return true;
    }
} 