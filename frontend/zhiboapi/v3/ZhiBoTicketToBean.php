<?php
/**
 * Created by PhpStorm.
 * User: hlq
 * Date: 16-05-11
 * Time: 下午5:30
 */

namespace frontend\zhiboapi\v3;

use backend\business\ToBeanGoodsUtil;
use frontend\business\ApiCommon;
use frontend\business\BalanceUtil;
use frontend\business\RewardUtil;
use frontend\business\SaveRecordByransactions\SaveByTransaction\CreateUserBalanceLogByTrans;
use frontend\business\SaveRecordByransactions\SaveByTransaction\SetTicketToBeanByTrans;
use frontend\business\SaveRecordByransactions\SaveByTransaction\TicketToBeanByTrans;
use frontend\zhiboapi\IApiExcute;


/**
 * Class 票转豆
 * @package frontend\zhiboapi\v3
 */
class ZhiBoTicketToBean implements IApiExcute
{
    private $params = [];
    /**
     * 检查参数合法性
     * @param string $error
     */
    private function check_param_ok($dataProtocal,&$error='')
    {
        //基本登录信息检测
//        if(!ApiCommon::CheckLogin($dataProtocal,$error))
//        {
//            return false;
//        }
        $goods_info = ToBeanGoodsUtil::GetBeanGoodsById($dataProtocal['data']['bean_goods_id']);
        if(empty($goods_info->record_id))
        {
            $error = '票提现商品不存在';
            return false;
        }

        if($goods_info->status == 0){
            $error = '该商品已经被禁用了';
            return false;
        }

        if(!ApiCommon::GetLoginInfo($dataProtocal['data']['unique_no'],$loginInfo, $error))
        {
            return false;
        }
        $user_id =  $loginInfo['user_id'];
        $my_balance_info = BalanceUtil::GetUserBalanceByUserId($user_id);

        //判断用户是否被冻结
        if($my_balance_info['freeze_status']==2)
        {
            //冻结用户不能票转豆
            $error = '账号冻结请联系客服';
            return false;
        }
        if($my_balance_info->ticket_count < $goods_info->ticket_num && ($my_balance_info->ticket_count - $goods_info->ticket_num) < 0)
        {
            $error = '可转换票数不够';
            return false;
        }

        $this->params = [
            'user_id' => $user_id,
            'op_unique_no' =>$dataProtocal['data']['op_unique_no'],
            'bean_goods_id' => $dataProtocal['data']['bean_goods_id'],
            'ticket_num' => $goods_info->ticket_num,  //票数
            'bean_num' => $goods_info->bean_num,  //豆数
            'balance_object' => $my_balance_info
        ];

        return true;
    }



    public function excute_action($dataProtocal, &$rstData,&$error, $extendData= array())
    {
        $error = '';
        if(!$this->check_param_ok($dataProtocal,$error)){
            return false;
        }
        //执行票转豆
        $money_params = [
            'bean_num' =>$this->params['bean_num'],
            'ticket_num' =>$this->params['ticket_num'],
            'user_id' => $this->params['user_id'],
        ];
        $transActions[] = new TicketToBeanByTrans($this->params['balance_object'],$money_params);
        $transActions[] = new SetTicketToBeanByTrans($this->params);

        //余额日志写入
        $extend_params = [
            'unique_id' => $this->params['op_unique_no'],
            'device_type' => $dataProtocal['device_type'],
            'op_value' => $this->params['ticket_num'],
            'relate_id' => '',
            'operate_type' => 2,
            'field' => 'ticket_count'
        ];
        $transActions[] = new CreateUserBalanceLogByTrans($this->params['balance_object'],$extend_params);
        $extend_params['operate_type'] = 3;
        $extend_params['field'] = 'bean_balance';
        $extend_params['op_value'] = $this->params['bean_num'];
        $transActions[] = new CreateUserBalanceLogByTrans($this->params['balance_object'],$extend_params);

        if(!RewardUtil::RewardSaveByTransaction($transActions,$outInfo, $error))
        {
            return false;
        }

        $rstData['has_data'] = '1';
        $rstData['data_type'] = 'string';
        $rstData['data'] = "";

        return true;
    }
}