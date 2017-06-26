<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/4/25
 * Time: 15:57
 */
namespace frontend\zhiboapi\v3;

use common\components\SystemParamsUtil;
use common\models\Comment;
use common\models\FaceStatistic;
use common\models\GoldsAccount;
use common\models\OffUserLiving;
use frontend\business\ApiCommon;
use frontend\business\ClientInfoUtil;
use frontend\business\ClientUtil;
use frontend\business\DynamicUtil;
use frontend\business\GoldsAccountUtil;
use frontend\business\IntegralAccountUtil;
use frontend\zhiboapi\IApiExcute;
use yii\log\Logger;

/**
 * 获取个人信息协议
 * Class ZhiBoGetClientInfo
 * @package frontend\zhiboapi\v3
 */
class ZhiBoGetClientInfo implements IApiExcute
{

    function excute_action($dataProtocal, &$rstData,&$error, $extendData= array())
    {
        $userInfo = [];
        $uniqueNo = $dataProtocal['data']['unique_no'];
        if(!ApiCommon::GetLoginInfo($uniqueNo,$sysLoginInfo,$error))
        {
            return false;
        }
            //\Yii::getLogger()->log('data:'.var_export($dataProtocal,true),Logger::LEVEL_ERROR);

        $fields = [
            'user_id'=>'client_id as user_id',
            'nick_name'=>'nick_name',
            'client_no'=>'client_no',
            'alipay_no'=>'alipay_no',
            'pic'=>'IFNULL(nullif(main_pic,\'\'),bc.pic) as pic',
            'icon_pic'=>'IFNULL(nullif(icon_pic,\'\'),bc.pic) as icon_pic',
            'level_id'=>'level_id',
            'level_pic'=>'ls.level_pic',
            'sex'=>'sex',
            'color'=>'color',
            'level_bg'=>'level_bg',
            'font_size'=>'font_size',
            'age'=>'age',
            'city'=>'bc.city',
            'sign_name'=>'sign_name',
            'send_ticket_count'=>'send_ticket_count',
            'attention_num'=>'attention_num',
            'funs_num'=>'funs_num',
            'ticket_count_sum'=>'ticket_count_sum',
            'ticket_count'=>'ticket_count',
            'ticket_real_sum'=>'ticket_real_sum',
            'today_ticket_num'=>'IFNULL(real_ticket_num,0) as today_ticket_num',
            'bean_balance'=>'bean_balance',
            'virtual_bean_balance'=>'virtual_bean_balance',
            'cash_rite'=>'cash_rite',
            'is_bind_weixin'=>'is_bind_weixin',
            'is_bind_alipay'=>'is_bind_alipay',
            'is_contract'=>'is_contract',
            'real_validate'=>'is_centification as real_validate',
            'is_attention'=>'is_attention',
            'is_black'=>'is_black',
            'is_live'=>'IFNULL(ll.status,1) as is_live',
            'first_reward'=>'first_reward',
            'living_id'=>'ll.living_id',
            'group_id'=>'IFNULL(group_id, \'\') as group_id',
            'is_join'=>'is_join',
            'tx_group_id'=>'IFNULL(tx_group_id, \'\') as tx_group_id',
            'group_name'=>'IFNULL(group_name, \'\') as group_name',
            'group_pic'=>'IFNULL(fg.pic, \'\') as group_pic',
//            'private_status' => 'if(ifnull(lp.private_id,0) = 0,0,1) as private_status',
            //'advance_notice'=>'IFNULL(advance_notice, \'\') as advance_notice',
            'create_time' => 'bc.create_time'
        ];
        $userFinish = 2;
        $filesInput = $dataProtocal['data']['fields'];
        $dataInfo = $filesInput;
        if(!is_array($dataInfo))
        {
            $dataInfo = [];
        }
        if(!is_array($filesInput))
        {
            $filesInput = [];
        }
        $joint = [      //额外获取拼接的参数
            'is_attention',
            'is_black',
            'today_ticket_num',
            'cash_rite',
            'first_reward',
            'is_join',
        ];
        $back = [];
        foreach($joint as $un)
        {
            if(in_array($un,$filesInput))
            {
                $back[] = $un;
            }
        }
        $clientInfo = [ //必须检测的字段
            'nick_name',
            'pic',
            'sex',
        ];
        if(!empty($filesInput)){
            foreach($clientInfo as $c){
                if(!in_array($c,$filesInput))
                {
                    $filesInput[] = $c;
                }
            }
        }
        $filedRst = [];
        if(!empty($filesInput))
        {
            foreach($filesInput as $field)
            {
                if(!isset($fields[$field]))
                {
                    $error = '请求的字段不存在';
                    return false;
                }
                $filedRst[] = $fields[$field];
            }
        }
        else
        {
            foreach($fields as $field)
            {
                $filedRst[] = $field;
            }
            $back = $joint;
        }

        $unset = [          //需要删除的参数
            'is_attention',
            'is_black',
            'first_reward',
            'is_join',
        ];
        foreach($unset as $t)
        {
            $set_false = array_search($t,$filedRst);
            if($set_false !== false)
            {
                unset($filedRst[$set_false]);
            }
        }

        $to_user = $sysLoginInfo['user_id'];
        if(!empty($dataProtocal['data']['user_id'])){
            $to_user = $dataProtocal['data']['user_id'];
        }
        $self_user_id = $sysLoginInfo['user_id'];
        $client_type = $sysLoginInfo['client_type'];
        if(!ClientInfoUtil::GetUserData($filedRst,$to_user,$self_user_id,$back,$userInfo,$error))
        {
            return false;
        }
        if(empty($userInfo['nick_name']) || empty($userInfo['pic']) || empty($userInfo['sex']))
        {
            $userFinish = 1;
        }
//        $code_type = SystemParamsUtil::GetSystemParam('get_system_white_off',true);
//        if($code_type == 1)
//        {
//            $is_off = OffUserLiving::findOne(['client_no'=>$userInfo['client_no']]);
//            if(isset($is_off))
//            {
//                $userInfo['real_validate'] = 2;
//            }
//            else
//            {
//                $userInfo['real_validate'] = 1;
//            }
//        }
        $userInfo['user_finish'] = $userFinish;
        $userInfo['is_super'] = 0;
        if($client_type == 2)
        {
            $userInfo['is_super'] = 1;
        }
        //\Yii::getLogger()->log('nick_name:'.$userInfo['nick_name'].' pic:'.$userInfo['pic'].' sex:'.$userInfo['sex'].' user_finish:'.$userFinish.' user_id:'.$self_user_id.' userinfo:'.var_export($userInfo,true),Logger::LEVEL_ERROR);
        if(empty($userInfo['first_reward']))
        {
            unset($userInfo['first_reward']);
        }
        if(!empty($dataInfo))
        {
            foreach($clientInfo as $c)
            {
                if(!in_array($c,$dataInfo)){
                    unset($userInfo[$c]);
                }
            }
        }

        //金币帐户信息

        $goldsAccountInfo = GoldsAccountUtil::GetGoldsAccountInfoByUserId($userInfo['user_id']);

        $userInfo['gold_account_info'] = $goldsAccountInfo;

        //积分帐户信息
        $integralAccountInfo = IntegralAccountUtil::GetIntegralAccountInfoByUserId($userInfo['user_id']);
        $userInfo['integral_account_info'] = $integralAccountInfo;

        //获取用户个人相册信息中的前三张图片
        $pic_list = DynamicUtil::GetDynamicListInfo($userInfo['user_id'],$userInfo['user_id'],1,10);
        $pic_lists = [];
        foreach($pic_list as $v)
        {
            $s = array_search($v, $pic_list);
            if($s < 3)
            {
                if($v['dynamic_type'] == 2 && $v['is_reward'] == 0)
                {
                    $pic_lists[$s]['pic'] = $v['dim_pic'];
                }
                else
                {
                    $pic_lists[$s]['pic'] = $v['pic'];
                }
                $pic_lists[$s]['dynamic_type'] = $v['dynamic_type'];
            }
        }

        $userInfo['pic_list_three'] = $pic_lists;
        $userInfo['pic_num'] = DynamicUtil::GetDynamicNum($userInfo['user_id']);
        $userInfo['shumei_switch'] = SystemParamsUtil::GetSystemParam('shumei_switch',true,'value1'); //数美敏感词开关
        $off_user = OffUserLiving::findOne(['client_no' => $userInfo['client_no']]);
        if(empty($off_user))   //白名单用户跳过认证
        {
            switch($userInfo['real_validate'])
            {
                case 2:
                    $user_res_time = strtotime($userInfo['create_time']);
                    $user_old_time = strtotime('2017-01-23');//23号后才上的人脸识别
                    if($user_res_time <= $user_old_time) //判断未上人脸识别之前已经通过高级认证的用户不需要再次认证
                    {
                        $userInfo['real_validate'] = 3;
                    }
                    break;
                case 3:
                    $face_info = FaceStatistic::findOne(['user_device_id' => strval($sysLoginInfo['user_id'])]);//查不到数据，说明是未上人脸代码之前的
                    if(empty($face_info))
                    {
                        $userInfo['real_validate'] = 2;
                    }
                    break;
                case 4:
                    $userInfo['real_validate'] = 2;
                    break;
                case 5:
                    $userInfo['real_validate'] = 1;
                    break;
                default:
                    $userInfo['real_validate'] = 1;
            }
        }
        else
        {
            $userInfo['real_validate'] = 3;
        }


//        \Yii::getLogger()->log('个人信息接口返回信息back:'.var_export($userInfo,true),Logger::LEVEL_ERROR);
        $rstData['has_data'] = '1';
        $rstData['data_type'] = 'json';
        $rstData['data'] = $userInfo;

        return true;
    }
} 