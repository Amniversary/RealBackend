<?php
namespace frontend\zhiboapi\v3;

use frontend\business\ChatFriendsUtil;
use frontend\zhiboapi\IApiExcute;
use frontend\business\ApiCommon;

/**
 * Class 获取通讯录关注列表
 * @package frontend\zhiboapi\v3
 */
class ZhiBoGetContactsAttentions implements IApiExcute
{
    /**
     * 检查参数合法性
     * @param string $error
     */
    private function check_param_ok($dataProtocal,&$error='')
    {
        $fields = ['unique_no','register_type'];
        $fieldLabels = ['唯一id','登录类型'];
        $len =count($fields);
        for($i = 0; $i <$len; $i ++)
        {
            if (!isset($dataProtocal['data'][$fields[$i]]) || empty($dataProtocal['data'][$fields[$i]])) {
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
        if(!ApiCommon::GetLoginInfo($dataProtocal['data']['unique_no'],$loginInfo,$error))
        {
            return false;
        }
        $user_id  = $loginInfo['user_id'];
        $friendsList = ChatFriendsUtil::GetContactsAttentions($user_id,$loginInfo['user_id']);
        $rstData['has_data'] = '1';
        $rstData['data_type']="jsonarray";
        $rstData['data']=$friendsList;
        return true;
    }
} 