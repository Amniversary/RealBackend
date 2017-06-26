<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/4/25
 * Time: 15:01
 */

namespace frontend\business;



use backend\business\UserUtil;
use common\components\PhpLock;
use common\components\SystemParamsUtil;
use common\components\UsualFunForStringHelper;
use common\components\WaterNumUtil;
use common\models\OffUserLiving;
use common\models\TicketToCash;
use frontend\business\SaveRecordByransactions\SaveByTransaction\CheckRecordInsertByTrans;
use frontend\business\SaveRecordByransactions\SaveByTransaction\CheckRecordSaveByTrans;
use frontend\business\SaveRecordByransactions\SaveByTransaction\CheckRecordSaveForReward;
use frontend\business\SaveRecordByransactions\SaveByTransaction\CreateTicketToCashByTrans;
use frontend\business\SaveRecordByransactions\SaveByTransaction\CreateTicketToCashPayByTrans;
use frontend\business\SaveRecordByransactions\SaveByTransaction\CreateUserBalanceLogByTrans;
use frontend\business\SaveRecordByransactions\SaveByTransaction\ModifyBalanceByTicketToCash;
use common\components\wxpay\lib\WxPayConfig;
use frontend\business\SaveRecordByransactions\SaveByTransaction\RefundMoneyByTrans;
use frontend\business\SaveRecordByransactions\SaveByTransaction\UpdateBalanceRecordTrans;
use yii\db\Query;
use yii\log\Logger;

class TicketToCashUtil
{
    /**
     * 获取提现记录
     * @param $user_id
     * @param $page_no
     * @param $page_size
     */
    public static function GetTicketToCashList($user_id,$page_no,$page_size)
    {
        if(empty($page_no))
        {
            $page_no = 1;
        }
        if(empty($page_size))
        {
            $page_size = 10;
        }
        $offset = ($page_no -1) * $page_size;
        $query = new Query();
        $query->from(TicketToCash::tableName())
            ->select(['create_time','cash_type','real_cash_money','status'])
            ->where(['user_id'=>$user_id])
            ->offset($offset)
            ->limit($page_size)
            ->orderBy('record_id desc');
        return $query->all();
    }

    /**
     * 票提现
     * @param $data
     * @param $unique_no
     * @param $error
     */
    public static function TicketToCash($data,$user_id,&$error)
    {
        $cash_type = $data['cash_type'];
        $op_unique_no = $data['op_unique_no'];
        $device_type = $data['device_type'];
        $client = ClientUtil::GetClientById($user_id);
        if(!isset($client))
        {
            $error = '用户不存在';
            return false;
        }
        if($client->status === 0)
        {
            $error = '该用户已被禁用';
            return false;
        }
        if($client->is_contract === 1 && $cash_type == '2')
        {
            $error = '非签约用户不能使用支付宝提现';
            return false;
        }
        $real_money = 0.00;
        if($cash_type == '1')
        {
            $goods_ticket_to_cash_id=$data['goods_ticket_to_cash_id'];
            if(empty($goods_ticket_to_cash_id) || intval($goods_ticket_to_cash_id) <=0 )
            {
                $error ='提现商品id为空';
                return false;
            }
            $goods_ticket_to_cash =GoodsTicketToCashUtil::GetGoodsTicketToCashById($goods_ticket_to_cash_id);
            if(!isset($goods_ticket_to_cash))
            {
                $error ='提现商品不存在';
                \Yii::getLogger()->log($error.' id:'.$goods_ticket_to_cash_id,Logger::LEVEL_ERROR);
                return false;
            }
            $real_money = $goods_ticket_to_cash->result_money;
            $ticket_num = $goods_ticket_to_cash->ticket_num;
        }
        else
        {
            if($client->is_contract !== 2)
            {
                $error = '非签约用户不能使用支付宝模式提现';
                return false;
            }
            $real_money = $data['money_value'];
            $rate = intval($client->cash_rite);
            if($rate < 1)
            {
                $error = '签约率异常';
                return false;
            }
            $ticket_num = sprintf('%0.0f',($real_money*$rate));
        }
        $goods = [
            'ticket_num' => $ticket_num,
            'result_money' => $real_money,
        ];
        $model = self::GetNewModel($cash_type,$client->client_id,$goods,$op_unique_no);
        if($cash_type == '2')
        {
            $model->cash_rate = $rate;//签约率
        }
        $tranactions = [];
        $tranactions[] = new CreateTicketToCashPayByTrans($model);
        $userBalance = BalanceUtil::GetUserBalanceByUserId($client->client_id);
        if(!isset($userBalance))
        {
            $error = '账户信息不存在';
            \Yii::getLogger()->log($error.' :user_id'.$client->client_id,Logger::LEVEL_ERROR);
            return false;
        }
        //判断用户是否被冻结
        if($userBalance['freeze_status']==2){
            //冻结用户不能票转豆
            $error = '账号冻结请联系客服';
            return false;
        }
        $params = [
            'ticket_num'=>$ticket_num
        ];
        $tranactions[] = new ModifyBalanceByTicketToCash($userBalance,$params);//减去相应的票数
        $params = [
            'op_value'=>$ticket_num,
            'operate_type'=>4,
            'unique_id'=>$op_unique_no,
            'device_type'=>$device_type,
            'field'=>'ticket_count',
        ];
        $tranactions[] = new CreateUserBalanceLogByTrans($userBalance,$params);

        $businessCheck = BusinessCheckUtil::GetBusinessCheckModelNew('1','',$client);
        $tranactions[] = new CheckRecordSaveByTrans($businessCheck);

        if(!SaveByTransUtil::RewardSaveByTransaction($tranactions,$error))
        {
            return false;
        }
        return true;
    }

    /**
     * 获取提现新模型
     * @param $cash_type
     * @param $user_id
     * @param $ticket
     * @param $real_cash_money
     * @param $goods
     * @param $op_unique_no
     * @return TicketToCash
     */
    public static function GetNewModel($cash_type,$user_id,$goods,$op_unique_no)
    {
        $model = new TicketToCash();
        $model->cash_type= $cash_type;
        $model->user_id= $user_id;
        $model->ticket_num = $goods['ticket_num'];
        $model->real_cash_money = $goods['result_money'];
        $model->status = 1;
        $model->create_time = date('Y-m-d H:i:s');
        $model->op_unique_no = $op_unique_no;
        return $model;
    }

    /**
     * 微信票提现
     */
    public static function WxTicketToCash($unionid,$open_id,$goods_id,$user_id,&$error)
    {
        $client = ClientUtil::GetClientById($user_id);
        if(!isset($client))
        {
            $error = '用户信息不存在';
            return false;
        }
        if($client->status === 0)
        {
            $error = '该用户已被禁用';
            return false;
        }
        //暂时去除实名认证 2016-06-13 刘亦菲
        //加上实名认证 2016-10-17 刘亦菲

        $white_user_id = OffUserLiving::findOne(['client_no'=>$client->client_no]);

        if(empty($white_user_id))
        {
            if ($client->is_centification <= 1)
            {
                $error = '非认证用户不支持微信提现';
                return false;
            }
        }

        //获取票商品信息
        $goods = GoodsTicketToCashUtil::GetGoodsTicketToCashById($goods_id);
        $cash_money = GoodsTicketToCashUtil::GetStatisticsCashMoney($user_id);
        $cash_money += $goods_id->result_money;
        $max_money = SystemParamsUtil::GetSystemParam('wx_pay_over_money',true,'value2');
       // \Yii::getLogger()->log('cash_money:'.$cash_money.'  | max_money:'.$max_money,Logger::LEVEL_ERROR);
        if($cash_money > $max_money)
        {
            $error = '今日提现金额已超出上限，请选择小金额提现或明天再来哟~';
            return false;
        }

        $cash_type = 1;
        $op_unique_no = UsualFunForStringHelper::CreateGUID();
        //生成提现模型
        $model = self::GetNewModel($cash_type,$user_id,$goods,$op_unique_no);
        $tranactions = [];
        $extend_params = [
            'user_id' => $user_id,
            'open_id' => $open_id,
            'other_id'=> $unionid,
        ];
        $tranactions[] = new CreateTicketToCashByTrans($model,$extend_params);
        //获取用户账户信息
        $userBalance = BalanceUtil::GetUserBalanceByUserId($client->client_id);
        if(!isset($userBalance))
        {
            $error = '账户信息不存在';
            \Yii::getLogger()->log($error.' :user_id'.$client->client_id,Logger::LEVEL_ERROR);
            return false;
        }
        //判断用户是否被冻结
        if($userBalance['freeze_status']==2){
            //冻结用户不能票转豆
            $error = '账号冻结请联系客服';
            return false;
        }
        $params = [
            'ticket_num'=>$goods->ticket_num, //1
        ];

        $tranactions[] = new ModifyBalanceByTicketToCash($userBalance,$params);
        $params = [
            'op_value'=>$goods->ticket_num,
            'operate_type'=>4,
            'unique_id'=>$unionid,
            'device_type'=>$client->device_type,
            'field'=>'ticket_count',
        ];

        $tranactions[] = new CreateUserBalanceLogByTrans($userBalance,$params);
        $businessCheck = BusinessCheckUtil::GetBusinessCheckModelNew('1','',$client);
        $tranactions[] = new CheckRecordSaveByTrans($businessCheck);

        if(!SaveByTransUtil::RewardSaveByTransaction($tranactions,$error))
        {
            return false;
        }
        return true;
    }



    /**
     * 通过ID得到票提现表mb_ticket_to_cash数据
     * @param $record_id
     * @return null|static
     */
    public static function GetTicketToCashById($record_id){
        $query = TicketToCash::findOne('record_id='.$record_id);
        return $query;
    }

    /**
     * 得到票提现表和用户基本信息
     * @param $record_id
     * @return array|bool
     */
    public static function GetTickToCashAndUserById($record_id){
        $query = (new Query())->from(['mb_ticket_to_cash t'])->select(['c.nick_name','c.phone_no','t.*'])
            ->leftJoin('mb_client c','c.client_id=t.user_id')
            ->where('t.record_id=:rid',[
            ':rid' => $record_id
        ])->one();
        return $query;
    }


    /**
     * 得到用户绑定支付宝信息
     * @param $user_id
     * @return array|bool
     */
    public static function CheckBindAlipay($record_id){
        $query = (new Query())
            ->select(['c.phone_no','c.nick_name','a.alipay_no','a.identity_no','a.real_name','t.*'])
            ->from('mb_client c')
            ->innerJoin('mb_alipay_for_cash a','a.user_id=c.client_id')
            ->innerJoin('mb_ticket_to_cash t','t.user_id=c.client_id')
            ->where('t.record_id=:rid',[':rid'=>$record_id])
            ->one();
        return $query;
    }

    /**
     * 得到用户绑定微信信息
     * @param $record_id
     * @return array|bool
     */
    public static function CheckBindWeChat($record_id){
        $query = (new Query())
            ->select(['c.phone_no','c.nick_name','o.remark1 as other_id','t.*'])
            ->from('mb_client c')
            ->innerJoin('mb_client_other o','o.user_id=c.client_id and o.remark1 is not null')
            ->innerJoin('mb_ticket_to_cash t','t.user_id=c.client_id')
            ->where('t.record_id=:rid',[':rid'=>$record_id])
            ->one();
        return $query;
    }

    /**
     * 打款失败，回滚到未审核
     * @param $record_id
     * @param $business_check_id
     * @param $error
     * @return bool
     * @throws \yii\db\Exception
     */
    public static function SetRollBackNotReviewed($record_id,$business_check_id,&$error)
    {
        $trans = \Yii::$app->db->beginTransaction();
        $sql = 'update mb_ticket_to_cash set status=1 WHERE record_id=:rid';
        $query = \Yii::$app->db->createCommand($sql,[':rid' => $record_id])->execute();
        $sql_b = 'update mb_business_check set status=0,check_result_status=0 WHERE business_check_id=:bcid';
        $query_b = \Yii::$app->db->createCommand($sql_b,[':bcid' => $business_check_id])->execute();
        if(($query <= 0) && ($query_b <= 0))
        {
            $error = '回滚到未审核失败';
            $trans->rollBack();
            return false;
        }
        $trans->commit();
        return true;
    }

    /**
     * 打款失败，设置打款失败状态
     * @param $record_id
     * @param $fail_info
     * @param $error
     * @return bool
     * @throws \yii\db\Exception
     */
    public static function SetCashFail($record_id,$fail_info,&$error)
    {
        $sql = 'update mb_ticket_to_cash set fail_status=1,remark2=:finfo WHERE record_id=:rid';
        $query = \Yii::$app->db->createCommand($sql,[':rid' => $record_id,':finfo' => $fail_info])->execute();
        if($query <= 0)
        {
            $error = '打款失败设置状态失败';
            return false;
        }
        return true;
    }

    /**
     * 票审核提现表基本信息判断
     * @param $record_id
     * @param $outinfo
     * @param $error
     * @return bool
     */
    public static function CheckBaseInfo($record_id,&$outinfo,&$error){
        if(empty($record_id))
        {
           $error='审核记录id为空，数据异常';
            return false;
        }

        $checkRecord = self::GetTickToCashAndUserById($record_id);
        if(!isset($checkRecord))
        {
            $error = '审核记录不存在，数据异常';
            \Yii::getLogger()->log('审核记录不存在，数据异常,record_id:'.$record_id, Logger::LEVEL_ERROR);
            return false;
        }
        $outinfo['cash_type'] = $checkRecord['cash_type'];
        $outinfo['status'] = $checkRecord['status'];
        $outinfo['op_unique_no'] = $checkRecord['op_unique_no'];
        $outinfo['ticket_num'] = $checkRecord['ticket_num'];
        $outinfo['op_unique_no'] = $checkRecord['op_unique_no'];
        $outinfo['user_id'] = $checkRecord['user_id'];
        $outinfo['real_cash_money'] = $checkRecord['real_cash_money'];
        $outinfo['nick_name'] = $checkRecord['nick_name'];
        return true;
    }

    /**
     * 票审核提现表基本信息判断
     * @param $params
     * @param $outinfo
     * @param $error
     * @return bool
     */
    public static function CheckRecordInfo($params,&$outinfo,&$error){
        $fields = ['record_id','backend_user_id'];
        $fieldLabels = ['审核记录id','用户id'];
        $len = count($fields);
        for($i = 0; $i <$len; $i ++)
        {
            if(!isset($params[$fields[$i]]))
            {
                $error = $fieldLabels[$i].'不能为空';
                return false;
            }
        }
        $user_id = $params['backend_user_id'];
        $user = UserUtil::GetUserByUserId($user_id);
        if(!isset($user))
        {
            $error = '后台用户不存在';
            return false;
        }

        $outinfo['username'] = $user->username;
        $outinfo['backend_user_id'] = $user->backend_user_id;
        return true;
    }


    /**
     * 票提现审核拒绝
     * @param $params
     * @param $error
     * @return bool
     */
    public static function CheckRefuse($params,&$error){
        if(!self::CheckRecordInfo($params,$outinfo,$error))
        {
            return false;
        }

        if(!self::CheckBaseInfo($params['record_id'],$outinfo,$error))
        {
            return false;
        }

        $arr = [2,3,4,6];
        $check_result_status = 1;
        if($params['check_rst'] == 4 || $params['check_rst'] == 6){
            $arr = [3,4,6];
            $check_result_status = 0;
        }
        if(in_array($outinfo['status'],$arr))
        {
            $error = '该记录已经审核过了';
            return false;
        }
        //如果是运营审核
        if($params['check_rst'] == 4){
            $business_info = BusinessCheckUtil::GetBusinessCheckInfo($params['record_id']);
            if(empty($business_info)){
                $error = '审核记录不存在';
                return false;
            }
        }
        //如果是财务审核
        if($params['check_rst'] == 6)
        {
            $business_info = BusinessCheckUtil::GetFinanceBusinessCheckInfo($params['record_id']);
            if(empty($business_info)){
                $error = '审核记录不存在';
                return false;
            }
        }

        $all_params = [
            'business_check_id' => $business_info->business_check_id,
            'record_id' => $params['record_id'],
            'refuesd_reason' => $params['refuesd_reason'],
            'check_rst' => $params['check_rst'],
            'check_result_status' => $check_result_status,
            'check_user_id' => $outinfo['backend_user_id'],
            'check_user_name' => $outinfo['username'],
            'create_user_id' => $outinfo['user_id'],
            'create_user_name' => $outinfo['nick_name'],
            'check_no' => 0,
        ];
        //审核记录更新
        $transActions[] = new CheckRecordInsertByTrans($all_params);

        //拒绝,退款操作
        if($params['check_rst'] == 4 || $params['check_rst'] == 6){
            $banlances_object = BalanceUtil::GetUserBalanceByUserId($outinfo['user_id']);
            $transActions[] = new RefundMoneyByTrans($banlances_object,$outinfo['ticket_num']); //退款操作
            $op_unique_no = md5(uniqid('meibo',true));
            $extend_params = [
                'unique_id' => $op_unique_no,
                'device_type' => 3,
                'op_value' => $outinfo['ticket_num'],
                'relate_id' => '',
                'field' => 'ticket_count',
                'operate_type' => 13,
            ];
            $transActions[] = new UpdateBalanceRecordTrans($banlances_object,$extend_params);
            $transActions[] = new CreateUserBalanceLogByTrans($banlances_object,$extend_params);
        }

        if (!RewardUtil::RewardSaveByTransaction($transActions, $outInfo, $error)) {
            return false;
        }

        return true;

    }


    /**
     * 支付宝提现审核
     * @param $TicketToCash
     * @param $error
     * @return bool
     */
    public static function SaveAlipayTicketToCash($params,&$error){
        if(!self::CheckRecordInfo($params,$outinfo,$error)){
            return false;
        }

        if(!self::CheckBaseInfo($params['record_id'],$outinfo,$error)){
            return false;
        }

        if(in_array($outinfo['status'],[3,4,6]))
        {
            $error = '该记录已经处理过了';
            return false;
        }

        $client_info = ClientUtil::GetClientById($outinfo['user_id']);
        if(empty($client_info)){
            $error = '该用户不存在';
            return false;
        }

        $bind_alipay_info = TicketToCashUtil::CheckBindAlipay($params['record_id']);
        if(empty($bind_alipay_info)){
            $error = '该用户未绑定支付宝';
            return false;
        }

        $banlances_master_object = BalanceUtil::GetUserBalanceByUserId($outinfo['user_id']); //提现用户账户信息
        if(empty($banlances_master_object)){
            $error = '该用户账户信息不存在';
            return false;
        }

        $all_params = [
            'record_id' => $params['record_id'],
            'refuesd_reason' => $params['refuesd_reason'],
            'check_rst' => $params['check_rst'],
            'check_result_status' => 1,
            'check_user_id' => $outinfo['backend_user_id'],
            'check_user_name' => $outinfo['username'],
            'create_user_id' => $outinfo['user_id'],
            'create_user_name' => $outinfo['nick_name'],
            'finace_ok_time' => date('Y-m-d H:i:s'),
            'check_no' => 0,
        ];
        //审核记录更新
        $transActions[] = new CheckRecordInsertByTrans($all_params);

        if (!RewardUtil::RewardSaveByTransaction($transActions, $outInfo, $error)) {
            return false;
        }

        return true;
    }


    /**
     * 生成随机字符串
     * @param int $len
     * @return null|string
     */
    public static function GetNonceStr($len = 32){
        $nonce_str = null;
        $strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
        $max = strlen($strPol)-1;

        for($i=0;$i<$len;$i++){
            $nonce_str.=$strPol[rand(0,$max)];
        }

        return $nonce_str;
    }

    /**
     * 生成微信红包签名
     * @param $params
     * @return string
     */
    public static function SetWeChatSign($params){
        $stringA = '';
        ksort($params);
        foreach($params as $key=>$val){
            if(!empty($val)){
                $stringA .= $key."=".$val."&";
            }
        }
        $stringSignTemp = $stringA."key=".WxPayConfig::KEY;
        $sign = strtoupper(md5($stringSignTemp));
        return $sign;
    }

    /**
     * 数组转xml
     * @param $arr
     * @return string
     */
    public static  function  arrayToXml($arr){
        $xml = "<xml>";

        foreach($arr as $key=>$val){
            if(empty($val)){
                $val = ' ';
            }
            $xml .= "<".$key.">".$val."</".$key.">";
        }

        $xml .= "</xml>";
//        header('Content-Type:text/xml; charset=utf-8');
//        echo $xml;
//        exit;
        return $xml;
    }


    /**
     * curl
     * @param $url
     * @param $vars
     * @param int $second
     * @param array $aHeader
     * @return bool|mixed
     */
    public static  function curl_post_ssl($url, $vars, $second=30,$aHeader=array())
    {
        $ch = curl_init();
        //超时时间
        curl_setopt($ch,CURLOPT_TIMEOUT,$second);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
        //这里设置代理，如果有的话
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);
        //cert 与 key 分别属于两个.pem文件
        //请确保您的libcurl版本是否支持双向认证，版本高于7.20.1
        curl_setopt($ch,CURLOPT_SSLCERT,'/var/www/mibo-php/common/components/wxpay/cert/apiclient_cert.pem');
        curl_setopt($ch,CURLOPT_SSLKEY,'/var/www/mibo-php/common/components/wxpay/cert/apiclient_key.pem');

//        curl_setopt($ch,CURLOPT_SSLCERT,'E:\xampp\htdocs\mibo-php\branches\version1\common\components\wxpay\cert\apiclient_cert.pem');
//        curl_setopt($ch,CURLOPT_SSLKEY,'E:\xampp\htdocs\mibo-php\branches\version1\common\components\wxpay\cert\apiclient_key.pem');
//        curl_setopt($ch,CURLOPT_SSLCERT,WxPayConfig::SSLCERT_PATH);
//        curl_setopt($ch,CURLOPT_SSLKEY,WxPayConfig::SSLKEY_PATH);
        if( count($aHeader) >= 1 ){
            curl_setopt($ch, CURLOPT_HTTPHEADER, $aHeader);
        }

        curl_setopt($ch,CURLOPT_POST, 1);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$vars);
        $data = curl_exec($ch);
        if($data){
            curl_close($ch);
            return $data;
        }
        else {
            $error = curl_errno($ch);
            echo "call faild, errorCode:$error\n";
            curl_close($ch);
            return false;
        }
    }


    /**
     * 微信企业付款
     * @param $openid
     * @param $ip
     * @param $total_amount
     * @param $nick_name
     */
    public static function SendWeChatPay($openid,$ip,$total_amount,$nick_name,&$error){
        $mch_billno = WaterNumUtil::GenWaterNum(WxPayConfig::MCHID,true, true,date('Ymd'),10);
        $nonce_str = self::GetNonceStr();
        $params['nonce_str'] = $nonce_str;    //随机字符
        $params['partner_trade_no'] = $mch_billno;    //订单号
        $params['mchid'] = WxPayConfig::MCHID;    //商户号
        $params['mch_appid'] = WxPayConfig::APPID;   //公众号appid
        $params['device_info'] = '';   //设备号
        $params['check_name'] = 'NO_CHECK';   //校验用户姓名选项
//        $params['re_user_name'] = $nick_name;   //收款用户姓名
        $params['amount'] = $total_amount*100;   //金额 单位为分
        $params['desc'] = '蜜播微信票提现';   //企业付款描述信息
        $params['openid'] = $openid;   //用户openid
        $params['spbill_create_ip'] = $ip;  //$_SERVER["REMOTE_ADDR"];   //Ip地址
        
        $sign = self::SetWeChatSign($params);
        $params['sign'] = $sign;

        $toXml = self::arrayToXml($params);

        $url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers';  //企业付款接口地址

        $sendResult = self::curl_post_ssl($url,$toXml);
        $res = json_decode(json_encode(simplexml_load_string($sendResult, 'SimpleXMLElement', LIBXML_NOCDATA)),true); //创建 SimpleXML对象
//        var_dump($res->result_code.'openid='.$openid.'----total_count='.$total_amount.'-----nick_anme='.$nick_name);exit;
//        \Yii::getLogger()->log('res=:'.var_export($res,true),Logger::LEVEL_ERROR);
        if($res['result_code'] !== 'SUCCESS'){
//            echo 'openid==:'.$openid.'   mchid==:'.WxPayConfig::MCHID.'    mch_appid==:'.WxPayConfig::APPID;
            \Yii::getLogger()->log('微信打款失败  '.$res['err_code_des'].'   $params===:'.var_export($params,true),Logger::LEVEL_ERROR);
            \Yii::getLogger()->log('微信打款返回错误信息  $res===:'.var_export($res,true),Logger::LEVEL_ERROR);
            $error = $res['err_code_des'];
            return false;
        }
        return true;



    }


    /**
     * 微信红包付款
     * @param $openid
     * @param $total_amount
     * @param $nick_name
     */
    public static function SendWeChatRedPackage($openid,$total_amount,&$error){
        $mch_billno = WaterNumUtil::GenWaterNum(WxPayConfig::MCHID,true, true,date('Ymd'),10);
        //$error= $mch_billno.'-------'.WxPayConfig::MCHID.'--------'.date('Ymd');return false;
        $nonce_str = self::GetNonceStr();
        $params['nonce_str'] = $nonce_str;    //随机字符
        $params['mch_billno'] = $mch_billno;    //订单号
        $params['mch_id'] = WxPayConfig::MCHID;    //商户号
        $params['wxappid'] = WxPayConfig::APPID;   //公众号appid
        $params['send_name'] = '蜜播';   //商户名称
        $params['re_openid'] = $openid;   //用户openid
        $params['total_amount'] = $total_amount;   //付款金额
        $params['wishing'] = '感谢您使用蜜播微信提现！';   //红包祝福语
        $params['total_num'] = 1;   //红包发放总人数
        $params['client_ip'] = $_SERVER["REMOTE_ADDR"];   //Ip地址
        $params['act_name'] = '蜜播微信提现';   //活动名称
        $params['remark'] = '赶快使用蜜播吧';   //备注

        $sign = self::SetWeChatSign($params);
        $params['sign'] = $sign;

        $toXml = self::arrayToXml($params);

        $url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers';  //企业付款接口地址

        $sendResult = self::curl_post_ssl($url,$toXml);
        $res = json_decode(json_encode(simplexml_load_string($sendResult, 'SimpleXMLElement', LIBXML_NOCDATA)),true); //创建 SimpleXML对象
//        var_dump($res->result_code.'openid='.$openid.'----total_count='.$total_amount.'-----nick_anme='.$nick_name);exit;
        if($res['result_code'] !== 'SUCCESS'){
            $error = $res['err_code_des'];
            return false;
        }
        return true;


    }


    /**
     * 微信提现审核
     * @param $params
     * @param $error
     * @return bool
     */
    public static function SaveWeChatTicketToCash($params,&$error){
        if(!self::CheckRecordInfo($params,$outinfo,$error)){
            return false;
        }
        /*$phpLock = new PhpLock('check_ticket_to_cash_id_'.$params['record_id']);
        $phpLock->lock();*/
        if(!self::CheckBaseInfo($params['record_id'],$outinfo,$error)){
            return false;
        }

        if(in_array($outinfo['status'],[3,4]))
        {
            $error = '该记录已经处理过了';
            return false;
        }

        $client_info = ClientUtil::GetClientById($outinfo['user_id']);
        if(empty($client_info)){
            $error = '该用户不存在';
            return false;
        }

        $bind_wechat_info = TicketToCashUtil::CheckBindWeChat($params['record_id']);
        if(empty($bind_wechat_info)){
            $error = '该用户未绑定微信';
            return false;
        }


        $banlances_master_object = BalanceUtil::GetUserBalanceByUserId($outinfo['user_id']); //提现用户账户信息
        if(empty($banlances_master_object)){
            $error = '该用户账户信息不存在';
            return false;
        }

        $check_result_status = 1;
        $finance_remark = '';
        //发送微信红包
        $is_send_wechat = TicketToCashUtil::SendWeChatPay($params['other_id'],$params['spbill_create_ip'],$outinfo['real_cash_money'],$outinfo['nick_name'],$error);
        if(!$is_send_wechat){
            //微信打款失败,退款操作
            $finance_remark = $error;
            return false;
//            $transActions[] = new RefundMoneyByTrans($outinfo['user_id'],$outinfo['ticket_num']); //退款操作
//
//            $banlances_object = BalanceUtil::GetUserBalanceByUserId($outinfo['user_id']);
//
//            $op_unique_no = md5(uniqid('meibo',true));
//            $extend_params = [
//                'unique_id' => $op_unique_no,
//                'device_type' => 3,
//                'op_value' => $outinfo['ticket_num'],
//                'relate_id' => '',
//                'field' => 'ticket_count',
//                'operate_type' => 13,
//            ];
//            $transActions[] = new CreateUserBalanceLogByTrans($banlances_object,$extend_params);
//            $check_result_status = 0;
//
//            $finance_remark = '打款失败，已退款';
        }

        $all_params = [
            'record_id' => $params['record_id'],
            'refuesd_reason' => $params['refuesd_reason'],
            'finance_remark' => $finance_remark,
            'check_rst' => $params['check_rst'],
            'check_result_status' => $check_result_status,
            'check_user_id' => $outinfo['backend_user_id'],
            'check_user_name' => $outinfo['username'],
            'create_user_id' => $outinfo['user_id'],
            'create_user_name' => $outinfo['nick_name'],
            'finace_ok_time' => date('Y-m-d H:i:s'),
            'check_no' => 0,
        ];
        //审核记录更新
        $transActions[] = new CheckRecordInsertByTrans($all_params);

        if (!RewardUtil::RewardSaveByTransaction($transActions, $outInfo, $error)) {
            return false;
        }

        return true;
    }

    /**
     * 处理微信批量打款
     * @param $sentData
     * @param $error
     * @return bool
     */
    public static function WeChatRechargeSaveTicketToCash($sentData,&$error)
    {
        if(!TicketToCashUtil::CheckBaseInfo($sentData->record_id,$outinfo,$error))
        {
            return false;
        }

        if(!isset($sentData->check_rst) || !in_array($sentData->check_rst,[2,3,4]))
        {
            $error = 'record_id:'.$sentData->record_id.'打款结果值异常';
            return false;
        }

        if(in_array($outinfo['status'],[3,4]))
        {
            $error = 'record_id:'.$sentData->record_id.'该记录已经处理过了';
            return false;
        }

        $params = [
            'record_id'=>$sentData->record_id,
            'backend_user_id'=>$sentData->backend_user_id,
            'refuesd_reason'=>$sentData->refuesd_reason,
            'check_rst'=>$sentData->check_rst,
            'user_id'=>$sentData->user_id,
            'spbill_create_ip'=>$sentData->spbill_create_ip,
        ];

        $bind_wechat_info = TicketToCashUtil::CheckBindWeChat($sentData->record_id);
        if(empty($bind_wechat_info))
        {
            $error = 'user_id :'.$sentData->user_id.'该用户未绑定微信';
            return false;
        }

        $params['other_id'] = $bind_wechat_info['other_id'];
        if(!TicketToCashUtil::SaveWeChatTicketToCash($params,$error))
        {
            return false;
        }

        return true;
    }

    /**
     * 处理支付宝批量打款
     * @param $sentData
     * @param $error
     * @return bool
     */
    public static function AlipayRechargeSaveTicketToCash($sentData,&$error)
    {
        if(!TicketToCashUtil::CheckBaseInfo($sentData->record_id,$outinfo,$error))
        {
            return false;
        }

        if(!isset($sentData->check_rst) || !in_array($sentData->check_rst,[2,3,4]))
        {
            $error = 'record_id:'.$sentData->record_id.'打款结果值异常';
            return false;
        }

        if(in_array($outinfo['status'],[3,4]))
        {
            $error = 'record_id:'.$sentData->record_id.'该记录已经处理过了';
            return false;
        }

        $params = [
            'record_id'=>$sentData->record_id,
            'backend_user_id'=>$sentData->backend_user_id,
            'refuesd_reason'=>$sentData->refuesd_reason,
            'check_rst'=>$sentData->check_rst,
            'user_id'=>$sentData->user_id,
        ];

        $bind_alipay_info = TicketToCashUtil::CheckBindAlipay($sentData->record_id);
        if(empty($bind_alipay_info))
        {
            $error = 'user_id :'.$sentData->user_id.'该用户未绑定支付宝';
            return false;
        }

        if(!TicketToCashUtil::SaveAlipayTicketToCash($params,$error))
        {
            $rst['msg'] = $error;
            echo json_encode($rst);
            exit;
        }

        return true;
    }

}