<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2015/12/24
 * Time: 9:38
 */

namespace frontend\business;



use common\components\GameRebotsHelper;
use common\models\GamePeriphery;
use common\models\GameSeat;
use frontend\business\SaveRecordByransactions\SaveByTransaction\NiuNiuGameGoldsAccountAddByTrans;
use frontend\business\SaveRecordByransactions\SaveByTransaction\NiuNiuGameGoldsAccountSubByTrans;
use frontend\business\SaveRecordByransactions\SaveByTransaction\NiuNiuGameGrabPeripherySaveByTrans;
use frontend\business\SaveRecordByransactions\SaveByTransaction\NiuNiuGameGrabSeatSaveByTrans;
use frontend\business\SaveRecordByransactions\SaveByTransaction\NiuNiuGameGrabSeatWinNumSaveByTrans;
use frontend\business\SaveRecordByransactions\SaveByTransaction\NiuNiuGameOtherSaveByTrans;
use frontend\business\SaveRecordByransactions\SaveByTransaction\NiuNiuGameResultRecordSaveByTrans;
use yii\db\Query;
use yii\helpers\Console;
use yii\log\Logger;

class NiuNiuGameGrabSeatUtil
{
    /**
     * 通过ID得到位置信息
     * @param $record_id
     * @return null|static
     */
    public static function GetGameSeatInfoById($record_id)
    {
        return GameSeat::findOne(['record_id' => $record_id]);
    }

    /**
     * 通过游戏ID座位号得到位置信息
     * @param $record_id
     * @return null|static
     */
    public static function GetGameSeatInfoBySeatNum($game_id,$seat_num)
    {
        return GameSeat::findOne(['game_id' => $game_id,'seat_num' => $seat_num]);
    }

    /**
     * 检测庄家是否存在
     * @param $record_id
     * @return null|static
     */
    public static function CheckGameGrabBanker($game_id)
    {
        return GameSeat::findOne(['game_id' => $game_id,'is_banker' => 2]);
    }

    /**
     * 通过用户id和游戏ID得到位置信息
     * @param $game_id
     * @param $user_id
     * @return null|static
     */
    public static function GetGameSeatInfoByGameIdAndUserId($game_id,$user_id)
    {
        return GameSeat::findOne(['and',['game_id' => $game_id,'user_id' => $user_id]]);
    }

    /**
     * 通过用户id和游戏ID得到外围信息
     * @param $game_id
     * @param $user_id
     * @return null|static
     */
    public static function GetGamePeripheryInfoByGameIdAndUserId($game_id,$user_id)
    {
        return GamePeriphery::findOne(['and',['game_id' => $game_id,'user_id' => $user_id]]);
    }

    /**
     * 通过用户ID得到位置信息
     * @param $record_id
     * @return null|static
     */
    public static function GetGameSeatInfoByUserId($user_id)
    {
        return GameSeat::findOne(['user_id' => $user_id]);
    }
    /**
     * 通过游戏ID得到座位信息
     * @param $game_id
     * @return array
     */
    public static function GetGameSeatInfo($game_id)
    {
        $query = (new Query())
            ->select(['record_id','game_id','user_id','seat_num','seat_status','is_banker','is_robot','is_living_master','poker_info','poker_result','win_num','chip_num','base_num','multiple','is_normal'])
            ->from('mb_game_seat')
            ->where('game_id=:gid',[':gid' => $game_id])
            ->all();
        return $query;
    }

    /**
     * 通过游戏ID和座位号得到座位信息
     * @param $game_id
     * @param $seat_num
     * @return array|bool
     */
    public static function GetGameSeatByGameIdAndSeatNumInfo($game_id,$seat_num)
    {
        $query = (new Query())
            ->select(['record_id','game_id','user_id','seat_num','seat_status','is_banker','is_robot','is_living_master','poker_info','poker_result','win_num','chip_num','base_num','multiple'])
            ->from('mb_game_seat')
            ->where('game_id=:gid and seat_num=:snum',[':gid' => $game_id,':snum' => $seat_num])
            ->one();
        return $query;
    }

    /**
     * 通过游戏ID和座位号得到座位信息
     * @param $game_id
     * @param $seat_num
     * @return array|bool
     */
    public static function GetGameByGameIdAndSeatNumInfo($game_id,$seat_num)
    {
        $query = (new Query())
            ->select(['ga.gold_account_balance','ng.record_id game_id','gs.record_id','ng.game_status','living_id','ga.user_id','seat_num','seat_status','is_banker','is_robot','is_living_master','poker_info','poker_result','win_num','chip_num','base_num','multiple'])
            ->from('mb_game_seat gs')
            ->innerJoin('mb_niuniu_game ng','ng.record_id=gs.game_id')
            ->innerJoin('mb_golds_account ga','ga.user_id=gs.user_id')
            ->where('gs.game_id=:gid and gs.seat_num=:snum',[':gid' => $game_id,':snum'=>$seat_num])
            ->one();
        return $query;
    }

    /**
     * 通过游戏ID和用户ID得到座位信息
     * @param $game_id
     * @param $uer_id
     * @return array|bool
     */
    public static function GetGameSeatByGameIdAndUserIdInfo($game_id,$uer_id)
    {
        $query = (new Query())
            ->select(['ga.gold_account_balance','ng.record_id game_id','gs.record_id','ng.game_status','living_id','ga.user_id','seat_num','seat_status','is_banker','is_robot','is_living_master','poker_info','poker_result','win_num','chip_num','base_num','multiple'])
            ->from('mb_game_seat gs')
            ->innerJoin('mb_niuniu_game ng','ng.record_id=gs.game_id')
            ->innerJoin('mb_golds_account ga','ga.user_id=gs.user_id')
            ->where('gs.game_id=:gid and gs.user_id=:uid',[':gid' => $game_id,':uid'=>$uer_id])
            ->one();
        return $query;
    }

    /**
     * 设置座位缓存信息
     * @param $error
     * @param $outInfo
     * @param $game_id
     * @param int $cache_time
     * @return bool
     */
    public static function GetGameSeatCacheInfo(&$error,&$outInfo,$game_id,$cache_time = 300)
    {
        $query_result = self::GetGameSeatInfo($game_id);
        $outInfo = $query_result;
        $query_result = json_encode($query_result);
        $cache = \Yii::$app->cache->set('mb_api_game_grab_seats_'.$game_id,$query_result,$cache_time);
        if(!$cache)
        {
            \Yii::getLogger()->log('座位列表缓存失败   query_result_list==:'.var_export($query_result,true),Logger::LEVEL_ERROR);
            $error = '座位列表缓存失败';
            return false;
        }
        return true;
    }

    /**
     * 保存用户位置信息
     * @param $seat_obj
     * @param $error
     * @return bool
     */
    public static function UpdateSeatInfo($seat_obj,&$error)
    {
        if(!$seat_obj instanceof GameSeat)
        {
            $error = '不是位置信息模型';
            return false;
        }
        if(!$seat_obj->save())
        {
            $error = '用户位置信息保存失败';
            return false;
        }
        return true;
    }
    /**
     * 上座
     * @param $game_id
     * @param $user_id
     * @param $living_id
     * @param $error
     * @param int $is_robot
     * @return bool
     */
    public static function DoGameGrabSeat($game_id,$user_id,$living_id,$device_type,&$error,$is_robot = 1)
    {
        if(!is_object($user_id) && !is_array($user_id) && empty($user_id))
        {
            $error = '用户上座参数错误';
            return false;
        }

        $living_info = LivingUtil::GetClientLivingInfo($living_id);
        if(!$living_info)
        {
            $error = '直播信息不存在';
            return false;
        }
        $im_user_datas = [];
        $only_user_id = 0;
        $rst = \Yii::$app->cache->get('niuniu_game_info_'.$living_id);
        if($rst === false)
        {
            $error = '抢座位缓存不存在 $living_id===:'.$living_id;
            return false;
        }
        $rst = json_decode($rst,true);
        foreach($user_id as $id)
        {
            $only_user_id = $id;
            $user_info = ClientUtil::GetClientById($id);
            if(empty($user_info))
            {
                $error = '用户信息不存在';
                return false;
            }
            foreach($rst['poker_info'] as &$seat)
            {
                if($seat['seat_status'] == 1)    //1 未上座  2 已上座
                {
                    $seat_info = self::GetGameSeatInfoBySeatNum($rst['game_id'],$seat['seat_num']);
                    $seat_info->base_num = 100;
                    $seat_info->multiple = 1;
                    $multiple = 1;
                    $base_num = 100;
                    $seat_info->user_id = $id;
                    $seat_info->is_robot = $is_robot;
                    $seat_info->game_id = $game_id;
                    $seat_info->seat_status = 2;
                    if(!self::UpdateSeatInfo($seat_info,$error))
                    {
                        \Yii::getLogger()->log($error.'  $seat_info===:'.var_export($seat_info,true),Logger::LEVEL_ERROR);
                        \Yii::getLogger()->flush(true);
                        continue;
                    }

                    //更新缓存信息
                    $seat['user_id'] = $id;
                    $seat['base_num'] = $base_num;
                    $seat['is_robot'] = $is_robot;
                    $seat['seat_status'] =2;
                    $seat['nick_name'] = $user_info->nick_name;
                    $seat['pic'] = (empty($user_info->middle_pic)?$user_info->pic:$user_info->middle_pic);
                    $seat['multiple'] = $multiple;
                    $seat['device_type'] = $device_type;

                    //IM消息数据
                    $im_user_datas[] = [
                        'user_id' => $id,
                        'game_id' => $game_id,
                        'base_num' => $base_num,
                        'seat_num' => $seat['seat_num'],
                        'multiple' => $multiple,
                        'nick_name' => $user_info->nick_name,
                        'pic' => (empty($user_info->middle_pic)?$user_info->pic:$user_info->middle_pic),
                        'seat_status' => 2,
                        'is_robot' => $is_robot,
                    ];
                    break;
                }
            }
        }
        //更新缓存信息
        $cache_info = json_encode($rst);
        $cache_out = \Yii::$app->cache->set('niuniu_game_info_'.$living_id,$cache_info,5*60);
        if(!$cache_out)
        {
            $error = '抢座位缓存更新失败  $cache_info===:'.var_export($rst);
            return false;
        }

        if(!empty($im_user_datas))
        {
            /***向群发送抢座消息***/
            $im_data = [
                'key_word'=>'game_grab_seat_im',
                'user_id' => $only_user_id,
                'other_id' => $living_info['other_id'],
                'im_user_datas' => $im_user_datas
            ];
            if(!JobUtil::AddImJob('tencent_im',$im_data,$error))
            {
                \Yii::getLogger()->log('牛牛游戏抢座im消息发送失败：im_data===:'.var_export($im_data,true),Logger::LEVEL_ERROR);
                \Yii::getLogger()->flush(true);
                return false;
            }
        }
        return true;
    }

    /**
     * 抢庄家
     * @param $game_id
     * @param $living_id
     * @param $seat_info
     * @param $device_type
     * @param $outInfo
     * @param $error
     * @return bool
     */
    public static function SetGameGrabBankerInfo($game_id,$living_id,$device_type,$user_id,&$outInfo,&$error)
    {
        $rst = \Yii::$app->cache->get('niuniu_game_info_'.$living_id);
        if($rst === false)
        {
            $error = '抢庄家缓存不存在 game_id===:'.$living_id;
            return false;
        }
        $rst = json_decode($rst,true);
        $living_info = LivingUtil::GetClientLivingInfo($living_id);
        if(!$living_info)
        {
            $error = '直播信息不存在  $living_id===:'.$living_id;
            return false;
        }

        $transActions = [];
        $seat_num = ['game_id' => $game_id];
        $banker_info = [];
        $robots_info = [];
        foreach($rst['poker_info'] as $seat)
        {
            $seat_num['seat'.$seat['seat_num']] = $seat['win_num'];
            if($seat['user_id'] == $user_id)
            {
                $banker_info = $seat;
                $banker_info['game_id'] = $game_id;
            }
            if($seat['is_robot'] == 2)
            {
                $robots_info[] = $seat;
            }

            $seat_data = [
                'is_banker' => $seat['is_banker'],
                'multiple' => $seat['multiple'],
                'win_num' => $seat['win_num'],
                'game_id' => $game_id,
                'user_id' => $seat['user_id'],
                'chip_num' => $seat['chip_num'],
                'seat_num' => $seat['seat_num']
            ];
            $transActions[] = new NiuNiuGameGrabSeatWinNumSaveByTrans($seat_data);     //座位信息更新
        }
        if(empty($banker_info))
        {
            $error = '用户座位信息不存在';
            return false;
        }

        $my_account_info = GoldsAccountUtil::GetGoldsAccountInfoByUserId($banker_info['user_id']);   //查用户金币
        $goldaccount_info = [
            'gold_account_id' => $my_account_info['gold_account_id'],
            'user_id' => $banker_info['user_id'],
            'device_type' => $device_type,
            'operateValue' => abs($banker_info['chip_num'])     //底数*倍数*胜负场次
        ];

        if($banker_info['win_num'] > 0)
        {
            $transActions[] = new NiuNiuGameGoldsAccountAddByTrans($goldaccount_info);   //添加金币
        }
        else
        {
            $transActions[] = new NiuNiuGameGoldsAccountSubByTrans($goldaccount_info);   //减少金币
        }

        $transActions[] = new NiuNiuGameResultRecordSaveByTrans($seat_num);   //游戏胜负记录
        $lose_num = 0;
        $win_num = 0;
        switch($banker_info['win_num'])      //胜利多少场，输了多少场
        {
            case -1 :
                $lose_num = 2;
                $win_num = 1;
                break;
            case -3 :
                $lose_num = 3;
                $win_num = 0;
                break;
            case 1 :
                $lose_num = 1;
                $win_num = 2;
                break;
            case 3 :
                $lose_num = 0;
                $win_num = 3;
                break;
        }

        $banker_info['win_money'] = (($banker_info['chip_num'] > 0)?$banker_info['chip_num']:0);
        $banker_info['lose_money'] = (($banker_info['chip_num'] < 0)?$banker_info['chip_num']:0);
        $banker_info['win_chip_num'] = ($banker_info['chip_num']*(-1));
        $banker_info['statistic_num'] = ($banker_info['chip_num']*(-1));
        $banker_info['lose_num'] = $lose_num;
        $banker_info['win_num'] = $win_num;
        $banker_info['chip_player_num'] = abs($banker_info['chip_num']);
        $banker_info['chip_num'] = abs($banker_info['chip_num']);

        $transActions[] = new NiuNiuGameOtherSaveByTrans($banker_info);    //其他信息更新

        if(!SaveByTransUtil::RewardSaveByTransaction($transActions,$error,$outInfo))
        {
            return false;
        }

        //机器人写入默认筹码结果
        foreach($robots_info as $robot)
        {
            $seat_info = self::GetGameSeatInfoBySeatNum($game_id,$robot['seat_num']);
//            $seat_info = self::GetGameSeatInfoById($robot['record_id']);
            if(empty($seat_info))
            {
                $error = '机器人座位信息不存在 info ===:'.var_export($robot,true);
                return false;
            }
            $seat_info->chip_num = $robot['chip_num'];
            $seat_info->win_num = $robot['win_num'];
            if(!self::UpdateSeatInfo($seat_info,$error))
            {
                \Yii::getLogger()->log($error.'  $seat_info===:'.var_export($seat_info,true),Logger::LEVEL_ERROR);
                return false;
            }

        }

        /***向群发送抢庄家消息***/
//        $im_data = [
//            'key_word'=>'game_grab_banker_im',
//            'user_id' => $seat_data['user_id'],
//            'seat_num' => $seat_data['seat_num'],
//            'other_id' => $living_info['other_id']
//        ];
//        \Yii::getLogger()->log('抢座im   $im_data===:'.var_export($im_data,true),Logger::LEVEL_ERROR);
//        \Yii::getLogger()->flush(true);
//        if(!JobUtil::AddImJob('tencent_im',$im_data,$error))
//        {
//            \Yii::getLogger()->log('牛牛游戏抢庄家im消息发送失败：im_data===:'.var_export($im_data,true),Logger::LEVEL_ERROR);
//            \Yii::getLogger()->flush(true);
//            return false;
//        }

        return true;
    }

    /**
     * 保存外围信息
     * @param $data_param
     * @param $error
     * @return bool
     */
    public static function DoGameGrabPerlphery($data_param,&$error)
    {
        $living_info = LivingUtil::GetClientLivingInfo($data_param['living_id']);
        if(!$living_info)
        {
            $error = '直播信息不存在';
            return false;
        }
        $user_info = ClientUtil::GetClientById($data_param['user_id']);
        if(empty($user_info))
        {
            $error = '用户信息不存在';
            return false;
        }
        $periphery_info = [
            'user_id' => $data_param['user_id'],
            'game_id' => $data_param['game_id'],
            'base_num' => $data_param['base_num'],
            'multiple' => $data_param['multiple'],
            'seat_num' => $data_param['seat_num'],
            'is_win' => $data_param['is_win'],
            'win_money_num' => $data_param['win_money_num'],
        ];
        $transActions[] = new NiuNiuGameGrabPeripherySaveByTrans($periphery_info);       //保存用户外围信息
        $goldaccount_info = [
            'gold_account_id' => $data_param['gold_account_id'],
            'user_id' => $data_param['user_id'],
            'device_type' => $data_param['device_type'],
            'operateValue' => abs($data_param['win_money_num'])
        ];
        if($data_param['win_num'] > 0)
        {
            $transActions[] = new NiuNiuGameGoldsAccountAddByTrans($goldaccount_info);   //添加金币
        }
        else
        {
            $transActions[] = new NiuNiuGameGoldsAccountSubByTrans($goldaccount_info);   //减少金币
        }
        $other_params = [
            'multiple' => $data_param['multiple'],
            'game_id' => $data_param['game_id'],
            'user_id' => $data_param['user_id'],
            'chip_num' => abs($data_param['win_money_num']),
            'win_money' => ($data_param['win_money_num'] > 0)?$data_param['win_money_num']:0,
            'lose_money' => ($data_param['win_money_num'] < 0)?$data_param['win_money_num']:0,
            'win_chip_num' => $data_param['win_money_num']*(-1),
            'statistic_num' => $data_param['win_money_num']*(-1),
            'lose_num' => ($data_param['win_num'] < 0)?1:0,
            'win_num' => ($data_param['win_num'] > 0)?1:0,
            'chip_player_num' => 0,
        ];
        $transActions[] = new NiuNiuGameOtherSaveByTrans($other_params);    //其他信息更新
        if(!SaveByTransUtil::RewardSaveByTransaction($transActions,$error,$outInfo))
        {
            return false;
        }
        return true;
    }

    /**
     * 得到外围返回结果
     * @param $game_id
     * @param $seat_num
     * @param $base_num
     * @param $multiple
     * @param $error
     * @return array|bool
     */
    public static function GetReturnGrabPerlphery($seat_num,$base_num,$multiple,$living_id,&$error)
    {
        $rst = \Yii::$app->cache->get('niuniu_game_info_'.$living_id);
        if($rst === false)
        {
            $error = '游戏信息处理异常4';
            return false;
        }
        $rst = json_decode($rst,true);
        $out_info = [];
        foreach($rst['poker_info'] as $seat)
        {
            if($seat['seat_num'] == $seat_num)
            {
                $is_win = ($seat['win_num'] > 0)?1:(-1);
                $out_info['is_win'] = ($seat['win_num'] > 0)?1:2;
                $out_info['seat_num'] = $seat['seat_num'];
                $out_info['win_num'] = $seat['win_num'];
                $out_info['win_money_num'] = $base_num*$multiple *$is_win;
                break;
            }
        }

        return $out_info;
    }


    /**
     * 更新座位下注信息
     * @param $data
     * @param $game_seat_info
     * @param $error
     * @return bool
     */
    public static function UpdateGameSeatBaseNum($data,$game_seat_info, &$error)
    {
        //$game_seat_info = self::GetGameSeatByGameIdAndUserIdInfo($data['game_id'],$data['user_id']);
        $living = LivingUtil::GetClientLivingInfo($data['living_id']);
        if($game_seat_info->is_robot == 2)
        {
            $data['base_num'] = 100;
        }

        $sql = 'update mb_game_seat set base_num = :bs,remark1 = :rm WHERE  game_id = :gd AND user_id = :ud';

        $rst = \Yii::$app->db->createCommand($sql,[
            ':bs'=>$data['base_num'],
            ':gd'=>$data['game_id'],
            ':ud'=>$data['user_id'],
            ':rm'=>time()
        ])->execute();

        if($rst <= 0)
        {
            $error = '下注信息更新失败';
            \Yii::getLogger()->log($error.': '.var_export($data,true),Logger::LEVEL_ERROR);
            return false;
        }
        //$rst = \Yii::$app->cache->get('mb_api_game_grab_seats_'.$data['game_id']);
        //重新设置游戏缓存信息
        $game_info = \Yii::$app->cache->get('niuniu_game_info_'.$data['living_id']);
        $game_All = json_decode($game_info,true);
        if(empty($game_All))
        {
            $error = '同步游戏记录信息失败';
            \Yii::getLogger()->log($error.': 下注游戏缓存信息不存在:'.var_export($game_All,true),Logger::LEVEL_ERROR);
            return false;
        }
        foreach($game_All['poker_info'] as &$i)
        {
            if($i['user_id'] == $data['user_id'])
            {
                $i['base_num'] = $data['base_num'];
            }
        }
        $rsl = json_encode($game_All);
        if(\Yii::$app->cache->set('niuniu_game_info_'.$data['living_id'],$rsl,5 * 60) === false)
        {
            $error = '游戏信息处理异常 - 2';
            return false;
        }
        $data = [
            'key_word'=>'game_base_im',
            'user_id'=>$data['user_id'],
            'game_id'=>$data['game_id'],
            'base_num'=>$data['base_num'],
            'seat_num'=>$game_seat_info->seat_num,
            'other_id'=>$living['other_id'],
        ];
        if(!JobUtil::AddImJob('tencent_im',$data,$error))
        {
            return false;
        }

        return true;
    }

    /**
     * 用户离开座位
     * @param $data_params
     * @param $error
     * @return bool
     */
//    public static function DoGameLeaveSeat($data_params,&$error)
//    {
//        $seat_info = NiuNiuGameGrabSeatUtil::GetGameSeatInfoById($data_params['record_id']);
//        if(empty($seat_info))
//        {
//            $error = '用户座位信息不存在';
//            return false;
//        }
//        if($seat_info->seat_status == 1)
//        {
//            $error = '用户已经离开座位';
//            return false;
//        }
//        $seat_info->seat_status = 1;        //状态设置为未上座
//        if(!NiuNiuGameGrabSeatUtil::UpdateSeatInfo($seat_info,$error))
//        {
//            return false;
//        }
//        $user_info = ClientUtil::GetClientById($data_params['user_id']);
//        $living_info = LivingUtil::GetClientLivingInfo($data_params['living_id']);
//        if(empty($user_info))
//        {
//            $error = '用户信息不存在';
//            return false;
//        }
//        /***向群发送离开座位消息***/
//        $im_data = [
//            'key_word'=>'game_leave_seat_im',
//            'user_id' => $living_info['living_master_id'],
//            'seat_num' => $seat_info->seat_num,
//            'nick_name' => $living_info['nick_name'],
//            'pic' => (empty($user_info->middle_pic)?$user_info->pic:$user_info->middle_pic),
//            'seat_status' => 1,
//            'other_id' => $living_info['other_id']
//        ];
//        if(!JobUtil::AddImJob('tencent_im',$im_data,$error))
//        {
//            \Yii::getLogger()->log('牛牛游戏离开座位im消息发送失败：im_data===:'.var_export($im_data,true),Logger::LEVEL_ERROR);
//            return false;
//        }
//        return true;
//    }
    /**
     * 获取主播金币信息
     * @param $game_id
     */
    public static function GetNewGoldParams($game_id)
    {
        $chip_money_info = (new Query())
            ->select(['gold_account_balance','chip_player_num','gold_account_id','ng.living_master_id as user_id','ng.record_id as game_id','ng.living_id'])
            ->from('mb_niuniu_game ng')
            ->leftJoin('mb_game_chip_money gcm','gcm.game_id=ng.record_id')
            ->leftJoin('mb_golds_account ga','ga.user_id=ng.living_master_id')
            ->where('ng.record_id=:gid',[':gid' => $game_id])
            ->one();

        return $chip_money_info;
    }


    /**
     * 离开坐位回收机器人、主播提成处理
     * @param $game_id
     * @param $error
     * @return bool
     */
    public static function GameSetRobot($game_id,$device_type,$game_status,&$error)
    {

        $game_info = NiuNiuGameUtil::GetNiuNiuGameById($game_id);
        $living_info = LivingUtil::GetClientLivingInfo($game_info->living_id);
        if($game_status == 5)
        {
            $cache = \Yii::$app->cache->get('niuniu_game_info_'.$game_info->living_id);
            $cache = json_decode($cache,true);
            if(empty($cache))
            {
                $error = '同步游戏记录信息失败';
                \Yii::getLogger()->log($error.': 结束游戏状态5 缓存信息不存在:'.var_export($cache,true),Logger::LEVEL_ERROR);
                return false;
            }
            $transActions = [];
            foreach($cache['poker_info'] as $games)
            {
                if($games['is_robot'] == 1 && $games['is_banker'] == 1)
                {
                    $gold_balance = GoldsAccountUtil::GetGoldsAccountModleByUserId($games['user_id']);
                    $gold_data = [
                        'gold_account_id' => $gold_balance->gold_account_id,
                        'user_id' => $gold_balance->user_id,
                        'device_type' => $games['device_type'],
                        'operateValue' => $games['base_num'] * $games['multiple'],
                    ];
                    if($games['win_num'] > 0)
                    {
                        $is_int = 1;
                    }
                    else
                    {
                        $is_int = 2;
                    }
                    if($is_int == 1)
                    {
                        $transActions[] = new NiuNiuGameGoldsAccountAddByTrans($gold_data);
                    }
                    else
                    {
                        $transActions[] = new NiuNiuGameGoldsAccountSubByTrans($gold_data);
                    }

                    // 统计游戏数据
                    $other_data = [
                        'user_id' => $games['user_id'],
                        'game_id' => $cache['game_id'],
                        'win_num' => (($is_int == 1) ? 1 : 0),
                        'lose_num' => (($is_int == 2) ? 1 : 0),
                        'win_money' => (($is_int == 1) ? $games['base_num'] * $games['multiple'] : 0),
                        'lose_money' => (($is_int == 2) ? $games['base_num'] * $games['multiple'] : 0),
                        'win_chip_num' => (($games['win_num']*$games['base_num'] * $games['multiple']) * -1),
                        'statistic_num' => (($games['win_num']*$games['base_num'] * $games['multiple']) * -1),
                        'chip_player_num' => abs($games['base_num'] * $games['multiple']),
                        'chip_num' => abs($games['base_num'] * $games['multiple'])
                    ];
                    $transActions[] = new NiuNiuGameOtherSaveByTrans($other_data);
                }
            }

            if(!SaveByTransUtil::RewardSaveByTransaction($transActions,$error,$out))
            {
                return false;
            }



            $chip_money_info = self::GetNewGoldParams($game_id);
            if($chip_money_info === false)
            {
                $error = '游戏信息错误   sql==:'.(new Query())
                        ->select(['gold_account_balance','chip_player_num','gold_account_id','ng.living_master_id as user_id','ng.record_id as game_id','ng.living_id'])
                        ->from('mb_niuniu_game ng')
                        ->leftJoin('mb_game_chip_money gcm','gcm.game_id=ng.record_id')
                        ->leftJoin('mb_golds_account ga','ga.user_id=ng.living_master_id')
                        ->where('ng.record_id=:gid',[':gid' => $game_id])
                        ->createCommand()->rawSql;
                \Yii::getLogger()->log($error,Logger::LEVEL_ERROR);
                \Yii::getLogger()->flush(true);
                return false;
            }

            if($chip_money_info['chip_player_num'] > 500)  //当单局所有玩家下注总金额超过500游戏币时，系统给主播总游戏币金额的1%作为奖励
            {
                $reward_money = $chip_money_info['gold_account_balance']*0.01;
                if(!GoldsAccountUtil::UpdateGoldsAccountToAdd($chip_money_info['gold_account_id'],$chip_money_info['user_id'],$device_type,5,$reward_money,$error))
                {
                    if(is_array($error))
                    {
                        $error = $error['info'];
                    }
                    return false;
                }
            }

            //状态为5发送结束游戏IM
            $im_data = [
                'key_word'=>'game_finish_game_yesstart_im',
                'data' => $cache,
                'user_id' => $chip_money_info['user_id'],
                'other_id' => $living_info['other_id']
            ];



            if(!JobUtil::AddImJob('tencent_im',$im_data,$error))
            {
                \Yii::getLogger()->log('牛牛游戏主播结束游戏，自动开始下局游戏im消息发送失败：im_data===:'.var_export($im_data,true),Logger::LEVEL_ERROR);
                return false;
            }
        }

        \Yii::$app->cache->delete('niuniu_game_info_'.$game_info->living_id);  //结束直播间，清除游戏的缓存信息
        $living_model = LivingUtil::GetLivingById($game_info->living_id);
        $living_model->game_name = '';
        if(!$living_model->save())
        {
            \Yii::getLogger()->log('牛牛游戏主播结束游戏，清空直播表game_name失败：$living_model===:'.var_export($living_model,true),Logger::LEVEL_ERROR);
        }
        if($game_status == 6)
        {
            /***状态为6结束游戏消息***/
            $im_data = [
                'key_word'=>'game_finish_game_im',
                'user_id' => $game_info->living_master_id,
                'other_id' => $living_info['other_id']
            ];
            //fwrite(STDOUT, Console::ansiFormat('向群发送结束游戏im   $im_data== '.var_export($im_data,true), [Console::FG_GREEN]));
            if(!JobUtil::AddImJob('tencent_im',$im_data,$error))
            {
                \Yii::getLogger()->log('牛牛游戏主播结束游戏，不再自动开始下局游戏im消息发送失败：im_data===:'.var_export($im_data,true),Logger::LEVEL_ERROR);
                return false;
            }
        }

        $query = (new Query())
            ->select(['record_id','client_id','IFNULL(nullif(middle_pic,\'\'),pic) as pic','nick_name','sex','is_robot'])
            ->from('mb_game_seat gs')
            ->innerJoin('mb_client cl','cl.client_id=gs.user_id')
            ->where('gs.is_robot=:robot and gs.seat_status=:sst and gs.game_id=:gid',[
                ':gid' => $game_id,
                ':sst' => 2,
                ':robot' => 2,
            ])->all();
        if(!empty($query))
        {
            $rebotsList = [];
            foreach($query as $robot)
            {
                $seat_obj = self::GetGameSeatInfoById($robot['record_id']);
                $seat_obj->seat_status = 1;
                $seat_obj->is_normal = 1;
                if(!self::UpdateSeatInfo($seat_obj,$error))
                {
                    \Yii::getLogger()->log('离开座位机器人回收失败   error==:'.$error,Logger::LEVEL_ERROR);
                    continue;
                }
                if($robot['is_robot'] == 2)
                {
                    $rebotsList[] = [
                        'client_id'=>$robot['client_id'],
                        'nick_name'=>$robot['nick_name'],
                        'pic'=>$robot['pic'],
                        'sex'=>$robot['sex']
                    ];
                }
            }
            if(!empty($rebotsList))
            {
                if(!GameRebotsHelper::GenRebots($rebotsList,$error))
                {
                    return false;
                }
            }
        }
        return true;
    }

} 