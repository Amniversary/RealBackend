<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/9
 * Time: 9:44
 */

namespace frontend\zhiboapi\v3;


use frontend\business\ApiCommon;
use frontend\business\ClientInfoUtil;
use frontend\zhiboapi\IApiExcute;
use yii\log\Logger;

class ZhiBoGetMultiClientInfo implements IApiExcute
{
    function excute_action($dataProtocal, &$rstData,&$error, $extendData= array())
    {
        $userInfo = [];
        $uniqueNo = $dataProtocal['data']['unique_no'];
        if(!ApiCommon::GetLoginInfo($uniqueNo,$sysLoginInfo,$error))
        {
            return false;
        }
        //\Yii::getLogger()->log('dataInfo_:'.var_export($dataProtocal,true),Logger::LEVEL_ERROR);
        $fields = [
            'user_id'=>'client_id as user_id',
            'nick_name'=>'nick_name',
            'client_no'=>'client_no',
            'alipay_no'=>'alipay_no',
            'pic'=>'IFNULL(main_pic,pic) as pic',
            'icon_pic'=>'icon_pic',
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
        ];

        $filesInput = $dataProtocal['data']['fields'];
        $dataInfo = $filesInput;
        if(!is_array($dataInfo)) {
            $dataInfo = [];
        }
        if(!is_array($filesInput)) {
            $filesInput = [];
        }
        $joint = [      //额外获取拼接的参数
            'is_attention',
            'is_black',
            'today_ticket_num',
            'cash_rite',
            'first_reward',
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
            'user_id',
            'nick_name',
            'pic',
            'sex',
        ];
        if(!empty($filesInput))
        {
            foreach($clientInfo as $c)
            {
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
            'first_reward'
        ];
        foreach($unset as $t)
        {
            $set_false = array_search($t,$filedRst);
            if($set_false !== false)
            {
                unset($filedRst[$set_false]);
            }
        }
        $user_params = $dataProtocal['data']['user_id'];
        $to_user = $sysLoginInfo['user_id'];
        $user_list ='';
        if(!empty($user_params))
        {
            if(is_array($user_params))
            {
                $user_list = implode(',',$user_params);
            }
            $to_user = $user_list;
        }
        $self_user_id = $sysLoginInfo['user_id'];
        if(!ClientInfoUtil::GetUserDataParams($filedRst,$to_user,$self_user_id,$back,$userInfo,$error))
        {
            return false;
        }
        foreach($userInfo as $in){
            $s = array_search($in,$userInfo);
            $in['userFinish'] = 2;
            if(empty($in['nick_name']) || empty($in['pic']) || empty($in['sex']))
            {
                $in['userFinish'] = 1;
            }
            $userInfo[$s] = $in;
        }
        foreach($userInfo as $t)
        {
            $s = array_search($t,$userInfo);
            if(empty($t['first_reward']))
            {
                unset($t['first_reward']);
            }
            $userInfo[$s] = $t;
        }
        if(!empty($dataInfo))
        {
            foreach($userInfo as $us)
            {
                $s = array_search($us,$userInfo);
                foreach($clientInfo as $c)
                {
                    if(!in_array($c,$dataInfo))
                    {
                        unset($us[$c]);
                    }
                    $userInfo[$s] = $us;
                }
            }
        }
        //\Yii::getLogger()->log('back:'.var_export($userInfo,true),Logger::LEVEL_ERROR);
        $rstData['has_data'] = '1';
        $rstData['data_type'] = 'json';
        $rstData['data'] = $userInfo;

        return true;
    }
} 