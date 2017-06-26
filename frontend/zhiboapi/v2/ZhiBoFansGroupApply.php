<?php
/**
 * Created by PhpStorm.
 * User: Zff
 * Date: 2016/9/10
 * Time: 15:00
 */

namespace frontend\zhiboapi\v2;

use common\components\tenxunlivingsdk\TimRestApi;
use common\models\FansGroup;
use frontend\business\ApiCommon;
use frontend\business\FansGroupUtil;
use frontend\zhiboapi\IApiExcute;
use yii\db\Query;
use yii\log\Logger;

class ZhiBoFansGroupApply implements IApiExcute
{
    private function check_param_ok($dataProtocal,&$error='')
    {
        $fields = ['group_id'];
        $fieldLabels = ['粉丝群ID'];
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

    public function excute_action($dataProtocal, &$rstData, &$error, $extendData= array())
    {
        $error = '';
        if(!$this->check_param_ok($dataProtocal,$error))
        {
            return false;
        }

        $group_id = $dataProtocal['data']['group_id'];
        $unique_no = $dataProtocal['data']['unique_no'];
        if(!ApiCommon::GetLoginInfo($unique_no, $sysLoginInfo, $error))
        {
            return false;
        }
        //获得用户的id
        $user_id = $sysLoginInfo['user_id'];
        $nick_name = $sysLoginInfo['nick_name'];
        //$user_id = $dataProtocal['data']['user_id'];

        $group_info = FansGroupUtil::FansGroupApply($group_id, $user_id, $error);

        if(!$group_info)
        {
            return false;
        }
        //\Yii::getLogger()->log('zff :'.$group_id,Logger::LEVEL_ERROR);
        $group = FansGroup::findOne(['group_id'=>$group_id]);
        $group_name = $group->group_name;
        //发送腾讯im消息
        $sendInfo = [
            'type' => 14,
            'user_id' => $user_id,
            'group_id'=>$group_id,
            'group_name'=>$group_name,
            'nick_name'=>$nick_name
        ];

        $text = json_encode($sendInfo);
        //根据群查找管理员
        $admin_list = (new Query())
            ->select(['user_id'])
            ->from('mb_fans_group_member')
            ->where('group_id=:gid and group_member_type !=0', [':gid'=>$group_id])
            ->all();
        $list = [];


        foreach($admin_list as $admin){
            array_push($list, strval($admin['user_id']));
        }

        if(!TimRestApi::openim_batch_send_msg_to_group_admin($text,$list,$error))
        {
            \Yii::getLogger()->log('推送消息失败',Logger::LEVEL_ERROR);
            return false;
        }
//        \Yii::getLogger()->log('____________________________消息发送成功',Logger::LEVEL_ERROR);
        $rstData['errno']='0';
        $rstData['has_data']='0';
        $rstData['data_type']='string';
        $rstData['data']  = '';
        $rstData['errmsg']  = '申请成功';
        return true;
    }
} 