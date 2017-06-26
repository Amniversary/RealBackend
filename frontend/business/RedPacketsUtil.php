<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 15-12-16
 * Time: 下午9:42
 */

namespace frontend\business;


use common\components\PhpLock;
use common\components\tenxunlivingsdk\TimRestApi;
use common\components\UsualFunForStringHelper;
use common\models\RedPacketMain;
use frontend\business\SaveRecordByransactions\SaveByTransaction\CreateUserBalanceLogByTrans;
use frontend\business\SaveRecordByransactions\SaveByTransaction\ModifyBalanceByAddRealBean;
use frontend\business\SaveRecordByransactions\SaveByTransaction\ModifyBalanceBySubRealBean;
use frontend\business\SaveRecordByransactions\SaveByTransaction\ModifyRedPacketMainByNum;
use frontend\business\SaveRecordByransactions\SaveByTransaction\ModifyRedPacketSonByStatus;
use frontend\business\SaveRecordByransactions\SaveByTransaction\RedPacketSaveByTrans;
use yii\db\Query;
use yii\log\Logger;

class RedPacketsUtil
{
    /**
     * 获取未发放完的红包，进行退还
     * @return array
     */
    public static function GetBackRedPacket()
    {
        //距离发布时间 超过2分钟，未结束
        $query = new Query();
        $query->select(['rps.gu_id','rps.red_packet_no','rpm.client_id'])
            ->from('mb_red_packet_main rpm')
            ->innerJoin('mb_red_packet_son rps','rps.gu_id = rpm.gu_id and rps.status=0')
            ->where(['and','rpm.create_time <= date_add(now(),INTERVAL  -5 MINUTE)','rpm.status=1'])
            ->limit(200);
        return $query->all();
    }

    /**
     * 红包退款
     * @param $gu_id
     * @param $red_packet_no
     * @param $client_id
     * @param $error
     * @return bool
     */
    public static function BackRedPacket($gu_id,$red_packet_no,$client_id,&$error)
    {
        $transActions= [];
        $error='';
        if(empty($gu_id) || empty($red_packet_no))
        {
            $error = '退款参数错误';
            return false;
        }
        $transActions[] = new ModifyRedPacketMainByNum(['gu_id'=>$gu_id]);
        $transActions[] = new ModifyRedPacketSonByStatus(['gu_id'=>$gu_id,'client_id'=>$client_id,'red_packet_no'=>$red_packet_no,'status'=>2]);
        $balance = BalanceUtil::GetUserBalanceByUserId($client_id);
        if(!isset($balance))
        {
            $error = '账户余额信息丢失';
            return false;
        }
        $transActions[] = new ModifyBalanceByAddRealBean($balance,['bean_num'=>'0']);
        $transActions[] = new CreateUserBalanceLogByTrans($balance,[
            'op_value'=>'0',
            'operate_type'=>19,
            'unique_id'=>UsualFunForStringHelper::CreateGUID(),
            'device_type'=>'3',
            'relate_id'=>$gu_id,
            'field'=>'bean_balance'
        ]);
        $outInfo = [];
        if(!SaveByTransUtil::RewardSaveByTransaction($transActions,$error,$outInfo))
        {
            return false;
        }
        $msg = '您发的未领取红包，已退回，豆数:'.$outInfo['op_value'].'个';
        if(!TimRestApi::openim_send_custom_msg($client_id,$msg,$error))
        {
            \Yii::getLogger()->log('发送腾讯云通知消息异常：'.$error,Logger::LEVEL_ERROR);
        }
        return true;
    }

    /**
     * 格式化红包主表信息
     * @param $data
     */
    public static function  GetRedPacketMainModel($data)
    {
        $model = new RedPacketMain();
        $model->gu_id = UsualFunForStringHelper::CreateGUID();
        $model->client_id = $data['user_id'];
        $model->red_packet_num = $data['red_num'];
        $model->red_packet_money = $data['money'];
        $model->red_packet_type = $data['red_type'];
        $model->get_num = 0;
        $model->living_id = $data['living_id'];
        $model->status = 1;
        $model->create_time = date('Y-m-d H:i:s');

        return $model;
    }

    /**
     * 生成红包 并保存事务
     * @param $info
     * @param $out
     * @param $error
     */
    public static function CreateRedPacket($info,&$out,&$error)
    {
        $user_id = $info['user_id'];
        $red_money = $info['red_money'];
        $red_num = $info['red_num'];
        $red_type = $info['red_type'];
        $balance = BalanceUtil::GetUserBalanceByUserId($user_id);
        //TODO: 单笔红包总金额
        $money = $red_money;

        if($balance->freeze_status == 2) {
            $error = '账号冻结请联系客服';
            return false;
        }

        if($money < 100) {
            $error = '红包金额不能小于100朵鲜花';
            return false;
        }
        if($red_type == 2) {
            $money = $red_money * $red_num;
        }

        if($balance->bean_balance < $money) {
            $error = '鲜花余额不足';
            return false;
        }
        $prams = [
            'bean_num'=>$money,
        ];
        //TODO: 扣除发起人用户余额
        $transActions[] = new ModifyBalanceBySubRealBean($balance,$prams);
        $extend_params = [
            'unique_id'=>UsualFunForStringHelper::CreateGUID(),
            'op_value'=>$money,
            'operate_type'=> 17,
            'device_type'=>$info['device_type'],
            'relate_id'=>'',
            'field'=>'bean_balance',
        ];
        //TODO: 生成财务日志log
        $transActions[] = new CreateUserBalanceLogByTrans($balance,$extend_params);

        //TODO: 生成红包
        if($red_type == 1)
        {
            $red_packet = UsualFunForStringHelper::GenRandRePacketsData($red_num,$red_money,$index_max,$error);
            if(!$red_packet) {
                return false;
            }
        }
        $living = LivingUtil::GetLivingByMasterId($user_id);
        $data = [
            'user_id'=>$user_id,
            'red_num'=>$red_num,
            'money'=>$money,
            'red_type'=>$red_type,
            'living_id'=>$living->living_id,
        ];
        //创建红包模型
        $model = RedPacketsUtil::GetRedPacketMainModel($data);
        $extend_params = [
            'red_packet'=>$red_packet, //红包信息
            'index_max'=>$index_max,  //最佳手气
            'red_money'=>$red_money,
        ];
        $transActions[] = new RedPacketSaveByTrans($model,$extend_params);
        if(!RewardUtil::RewardSaveByTransaction($transActions,$out,$error)) {
            return false;
        }

        $red_data = [
            'user_id'=>$user_id,
            'red_packet_id'=>$out['gu_id'],
            'red_num'=>$red_num,
            'red_money'=>$money,
            'user_list'=>[],
            'red_packet_no'=>0,
            'index_max'=>$index_max,
        ];

        $packet_data = json_encode($red_data);

        \Yii::$app->cache->set('red_packet_id_'.$out['gu_id'],$packet_data,60*5);

        return true;
    }

    /**
     * 执行抢红包
     * @param $red_packet_info
     * @param $UserInfo
     * @param $error
     * @return bool
     */
    public static function DoRedPacket($red_packet_info,$unique_no,&$outinfo,&$error)
    {
        $lock = new PhpLock('red_packet_id_'.$red_packet_info['red_packet_id']); //锁
        $lock->lock();
        $device_type = $red_packet_info['device_type'];
        $red_packet_info = \Yii::$app->cache->get('red_packet_id_'.$red_packet_info['red_packet_id']);
        if(!isset($red_packet_info) || empty($red_packet_info))
        {
            $error = '红包已经抢完啦！';
            return false;
        }
        $red_packet_info = json_decode($red_packet_info,true);
        $login_info = ApiCommon::GetLoginInfo($unique_no,$OutUserInfo,$error);//根据unique_no 获取用户client_id
        if(!$login_info)
        {
            $lock->unlock();
            $error = '用户不存在';
            return false;
        }
        if(in_array($OutUserInfo['user_id'],$red_packet_info['user_list']))
        {
            $lock->unlock();
            $error = '您已经抢过红包了';
            return false;
        }
        if(($red_packet_info['red_packet_no'] >= $red_packet_info['red_num']) || (count($red_packet_info['user_list']) >= $red_packet_info['red_num'])){
            $lock->unlock();
            $error = '红包已经抢完啦';
            return false;
        }
        array_push($red_packet_info['user_list'],$OutUserInfo['user_id']);
        $red_num = $red_packet_info['red_num'];
        $red_packet_no = count($red_packet_info['user_list']);
        $new_packet_info =[
            'user_id' => $red_packet_info['user_id'],
            'red_packet_id'=>$red_packet_info['red_packet_id'],
            'red_num'=>$red_num,
            'red_money'=>$red_packet_info['red_money'],
            'user_list'=>$red_packet_info['user_list'],
            'red_packet_no'=>$red_packet_no,
        ];

        $new_red_packet_info = json_encode($new_packet_info);
        $data = [
            'client_id' => $OutUserInfo['user_id'],
            'gu_id' => $red_packet_info['red_packet_id'],
            'status' =>1,
            'red_packet_no' => $red_packet_no,
            'red_num' => $red_num,
            //'end_time' => $red_packet_info['end_time'],
        ];


        $banlances_object = BalanceUtil::GetUserBalanceByUserId($data['client_id']);
        $transActions[] = new ModifyRedPacketSonByStatus($data);
        $transActions[] = new ModifyRedPacketMainByNum(['gu_id'=>$data['gu_id']]);
        $transActions[] = new ModifyBalanceByAddRealBean($banlances_object,['bean_num' => 0]);
        $extend_params = [
            'unique_id' => UsualFunForStringHelper::CreateGUID(),
            'device_type' => $device_type,
            'op_value' => 0,
            'relate_id' => '',
            'field' => 'bean_balance',
            'operate_type' => 18
        ];
        $transActions[] = new CreateUserBalanceLogByTrans($banlances_object,$extend_params);
        if(!RewardUtil::RewardSaveByTransaction($transActions,$outInfo, $error))
        {
            $lock->unlock();
            return false;
        }

        $cache_set = \Yii::$app->cache->set('red_packet_id_'.$data['gu_id'],$new_red_packet_info,5*60);
        if(!$cache_set)
        {
            $lock->unlock();
            \Yii::getLogger()->log('抢红包更新缓存失败!',Logger::LEVEL_ERROR);
            $error = '红包已经被抢完啦!';
            return false;
        }

        if($data['red_packet_no'] >= $data['red_num'])
        {
            //抢到用户个数等于红包个数，清除缓存记录
            $cache_delete = \Yii::$app->cache->delete('red_packet_id_'.$data['gu_id']);
            if(!$cache_delete)
            {
                $lock->unlock();
                \Yii::getLogger()->log('抢到用户个数等于红包个数,删除缓存失败!',Logger::LEVEL_ERROR);
                $error = '红包已经被抢完啦!';
                return false;
            }
        }
        $lock->unlock();
        $outinfo['op_value'] = $outInfo['op_value'];
        $outinfo['lucky'] = $outInfo['lucky'];
        $outinfo['client_id'] = $OutUserInfo['user_id'];
        return true;
    }


} 