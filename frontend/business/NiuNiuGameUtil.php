<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2015/12/24
 * Time: 9:38
 */

namespace frontend\business;



use common\models\NiuniuGame;
use frontend\business\SaveRecordByransactions\SaveByTransaction\AddNiuNiuGameSaveByTrans;
use frontend\business\SaveRecordByransactions\SaveByTransaction\NiuNiuGameGoldsAccountAddByTrans;
use frontend\business\SaveRecordByransactions\SaveByTransaction\NiuNiuGameGoldsAccountSubByTrans;
use frontend\business\SaveRecordByransactions\SaveByTransaction\NiuNiuGameMultipleUpdateSaveByTrans;
use frontend\business\SaveRecordByransactions\SaveByTransaction\NiuNiuGameOtherSaveByTrans;
use yii\db\Query;
use yii\log\Logger;

class NiuNiuGameUtil
{
    /**
     * 初始化游戏记录 座位记录信息
     * @param $data
     * @param $outAll
     * @param $error
     * @return bool
     */
    public static function CreateGameInfo($data, &$outAll, &$error)
    {
        $living = LivingUtil::GetLivingById($data['living_id']);
        //\Yii::getLogger()->log('$living_id:'.$data['living_id'].'aa:'.var_export($living,true),Logger::LEVEL_ERROR);
        if(!isset($living))
        {
            $error = '直播间信息不存在';
            return false;
        }
        if($living->status == 0)
        {
            $error = '直播间已关闭，游戏创建失败';
            return false;
        }

        $data = [
            'game_id' => $data['game_id'],
            'living_master_id' => $living->living_master_id,
            'living_id' => $living->living_id,
        ];
        //生成游戏牌信息
        $poker_info = self::GetNiuNiuGamePokerInfo();
        $params = [
            'seat_info' => '',
            'poker_info' => $poker_info,
            'game_name' => $living->game_name,
        ];
        //获取上一局座位信息
        //$up_game_id = \Yii::$app->cache->get('up_game_info_' . $living->living_id);

        /*if($up_game_id !== false)
        {
            $seat_info = NiuNiuGameGrabSeatUtil::GetGameSeatInfo($up_game_id);//前一局游戏的座位信息
            $params['seat_info'] = $seat_info;
        }*/
        //生成游戏数据模型
        $model = self::CreateNiuNiuGameModel($data);
        //初始化游戏信息和初始化座位信息
        $transAction[] = new AddNiuNiuGameSaveByTrans($model, $params);
        if(!SaveByTransUtil::RewardSaveByTransaction($transAction, $error, $out))
        {
            return false;
        }
        //将上局游戏id 进行缓存
        //\Yii::$app->cache->set('up_game_info_' . $living->living_id, $out['record_id'], 5 * 60);
        //返回结果
        $now_seat_all = NiuNiuGameUtil::GetNiuNiuGameInfo($out['record_id']);
        $outAll = [
            'game_id' => $out['record_id'],
        ];
        foreach($now_seat_all as $seat)
        {
            $poker = json_decode($seat['poker_info'], true);
            $outAll['poker_info'][$seat['seat_num']] = [
                'user_id' => $seat['user_id'],
                'pic' => $seat['pic'],
                'nick_name' => $seat['nick_name'],
                'seat_status' => $seat['seat_status'],
                'is_banker' => $seat['is_banker'],
                'is_robot' => $seat['is_robot'],
                'is_living_master' => $seat['is_living_master'],
                'is_win' => 0,
                'seat_num'=>$seat['seat_num'],
                'chip_num'=>$seat['chip_num'],
                'base_num' => $seat['base_num'],
                'multiple' => $seat['multiple'],
                'poker_result' => $seat['poker_result'],
                'poker1' => $poker['poker1'],
                'poker2' => $poker['poker2'],
                'poker3' => $poker['poker3'],
                'poker4' => $poker['poker4'],
                'poker5' => $poker['poker5'],
            ];
        }
        //保存缓存游戏信息
        $data_All = json_encode($outAll);
        $cache_ok = \Yii::$app->cache->set('niuniu_game_info_'.$living->living_id,$data_All,5 * 60);
        if($cache_ok === false)
        {
            $error = '游戏信息处理异常 - 1';
            return false;
        }
        //\Yii::getLogger()->log('开始游戏缓存数据:'.var_export($outAll,true).' living_id:'.$living->living_id,Logger::LEVEL_ERROR);
        return true;
    }

    /**
     * 创建牛牛游戏记录模型
     * @param $data
     */
    public static function CreateNiuNiuGameModel($data)
    {
        $model = new NiuniuGame();
        $model->game_id = $data['game_id'];
        $model->living_id = $data['living_id'];
        $model->living_master_id = $data['living_master_id'];
        $model->game_status = 1;
        $model->is_normal = 1;
        $model->create_time = date('Y-m-d H:i:s');

        return $model;
    }

    /**
     * 根据游戏id 获取游戏记录和座位信息
     * @param $game_id
     */
    public static function GetNiuNiuGameInfo($game_id)
    {
        $query = (new Query())
            ->select(['ng.record_id as game_id', 'living_id', 'game_status', 'seat_num', 'seat_status', 'is_banker', 'is_robot', 'is_living_master', 'poker_result','win_num', 'chip_num', 'base_num', 'multiple', 'poker_info', 'IFNULL(user_id,\'\') as user_id', 'IFNULL(nick_name,\'\') as nick_name', 'IFNULL(nullif(middle_pic,\'\'),pic) as pic'])
            ->from('mb_niuniu_game ng')
            ->innerJoin('mb_game_seat gs', 'ng.record_id = gs.game_id')
            ->leftJoin('mb_client bc', 'bc.client_id = gs.user_id')
            ->where('ng.record_id = :rd', [':rd' => $game_id])
            ->all();


        return $query;
    }


    /**
     * @param $living_id
     * @param $user_id
     * @return array|null|\yii\db\ActiveRecord
     */
    public static function GetNiuNiuGameByLivingId($living_id, $user_id)
    {
        return NiuniuGame::find()->where('living_id = :ld and living_master_id = :md and game_status < 5', [':ld' => $living_id, ':md' => $user_id])->one();
    }

    /**
     * 通过id获取游戏信息
     * @param $game_id
     * @return null|static
     */
    public static function GetNiuNiuGameById($game_id)
    {
        return NiuniuGame::findOne(['record_id' => $game_id]);
    }

    /**
     * 保存牛牛游戏信息
     * @param $niuniu_game
     * @param $error
     * @return bool
     */
    public static function SaveNiuNiuGame($niuniu_game, &$error)
    {
        if (!$niuniu_game instanceof NiuniuGame)
        {
            $error = '不是牛牛游戏模型';
            return false;
        }
        if(!$niuniu_game->save())
        {
            $error = '游戏保存失败';
            return false;
        }
        return true;
    }

    /**
     * 从配置文件读取扑克牌信息 乱序返回4副卡牌 每副 5张 , 返回牌大小结果poker_result
     * @return mixed
     */
    public static function GetNiuNiuGamePokerInfo()
    {
        $arr = require(__DIR__ . '/../../common/config/NiuNiuGamePokerInfo.php');
        shuffle($arr);
        shuffle($arr);
        $poker = array_slice($arr, 0, 20);
        $poker_info = array_chunk($poker, 5);

        $rst = [];
        foreach($poker_info as $info)
        {
            $s = array_search($info, $poker_info);
            $card = [];
            foreach($info as $i)
            {
                $card[] = $i['poker'];
            }
            $poker_info[$s]['poker_result'] = NiuNiuGameHelper::JudgeCowCow($card);
        }
        return $poker_info;
    }

    /**
     * 生成52张牛牛扑克牌信息;
     * @return array
     */
    public static function CreatePokerInfo()
    {
        $arr = [];
        $temp1 = ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12', '13'];
        $temp2 = [1 => '黑桃', 2 => '红桃', 3 => '梅花', 4 => '方块'];

        foreach ($temp1 as $v)
        {
            foreach ($temp2 as $j => $a)
            {
                $arr[] = [
                    'poker' => $v,
                    'shape' => $j,
                ];
            }
        }
        //$arr[] = '小王Joker';
        //$arr[] = '大王Joker';

        return $arr;
    }

    /**
     * 更新玩家叫倍信息
     * @param $dataAll
     * @param $game_seat_info
     * @param $gold_balance
     * @param $error
     * @return bool
     */
    public static function UpdateGamePlayerMultiple($dataAll, $game_seat_info, &$error)
    {
        $living = LivingUtil::GetClientLivingInfo($dataAll['living_id']);

        //获取游戏缓存
        $game_info = \Yii::$app->cache->get('niuniu_game_info_'.$dataAll['living_id']);
        $game_All = json_decode($game_info,true);
        if(empty($game_All))
        {
            $error = '同步游戏记录信息失败';
            \Yii::getLogger()->log($error.': 叫倍游戏缓存信息不存在:'.var_export($game_All,true),Logger::LEVEL_ERROR);
            return false;
        }

        $is_int = '';
        foreach($game_All['poker_info'] as $s)
        {
            if($s['user_id'] == $dataAll['user_id'])
            {
                if($s['win_num'] > 0)
                {
                    $is_int = 1;
                }
                else
                {
                    $is_int = 2;
                }
                break;
            }
        }
        //\Yii::getLogger()->log('叫倍玩家数据信息:'.var_export($dataAll,true).'is_int:'.$is_int,Logger::LEVEL_ERROR);
        $dataAll['is_int'] = $is_int;
        //\Yii::getLogger()->log('叫倍信息插入之前===:'.var_export($dataAll,true),Logger::LEVEL_ERROR);
        //\Yii::getLogger()->flush(true);
        //\Yii::getLogger()->log('叫倍信息插入之前poker_info===:'.var_export($game_All['poker_info'],true),Logger::LEVEL_ERROR);
        //\Yii::getLogger()->flush(true);
        $transActions = new NiuNiuGameMultipleUpdateSaveByTrans($dataAll, $game_seat_info);

        if(!$transActions->SaveRecordForTransaction($error, $out))
        {
            return false;
        }
        //重新设置游戏缓存信息
        foreach($game_All['poker_info'] as &$i)
        {
            if($i['user_id'] == $dataAll['user_id'])
            {
                $i['multiple'] = $dataAll['multiple'];
                $i['is_win'] = (($is_int == 1) ? 2 : 1);
                $i['chip_num'] = (($is_int == 1) ? $game_seat_info->base_num * $dataAll['multiple'] : $game_seat_info->base_num * $dataAll['multiple'] * -1);
            }
        }
        \Yii::getLogger()->log('玩家叫倍信息更新缓存:'.var_export($game_All,true).'  living_id:'.$dataAll['living_id'],Logger::LEVEL_ERROR);
        $rsl = json_encode($game_All);
        if(\Yii::$app->cache->set('niuniu_game_info_'.$dataAll['living_id'],$rsl,5 * 60) === false)
        {
            $error = '游戏信息处理异常 - 3';
            return false;
        }
        $data = [
            'key_word' => 'game_multiple_im',
            'user_id' => $dataAll['user_id'],
            'game_id' => $dataAll['game_id'],
            'base_num' => $game_seat_info->base_num,
            'seat_num' => $game_seat_info->seat_num,
            'multiple' => $dataAll['multiple'],
            'other_id' => $living['other_id'],
        ];

        //向客户端发送im消息
        if(!JobUtil::AddImJob('tencent_im', $data, $error))
        {
            return false;
        }

        return true;
    }

    /**
     * 查询出范围内的异常数据 create_time大于5分钟，状态值不正确
     * @param $limit
     * @param $offset
     * @return array
     */
    public static function SelectNiuNiuGameIsNormalInfo($limit,$offset)
    {
          $query = (new Query())
              ->select(['record_id','game_id','living_id','living_master_id','game_status','is_normal','create_time'])
              ->from('mb_niuniu_game')
              ->where('is_normal=:no and game_status<:status and UNIX_TIMESTAMP(create_time) < :time',[
                  ':no' => 1,
                  ':status' => 5,
                  ':time' => (time()-300)
              ])
              ->limit($limit)
              ->offset($offset)
              ->all();
        return $query;
    }

    /**
     * 查找出单局游戏的所有外围玩家
     * @param $game_id
     * @param $limit
     * @param $offset
     * @return array
     */
    public static function SelectGameToPeripheryUsers($game_id,$limit,$offset)
    {
        $query = (new Query())
            ->select(['record_id','user_id','game_id','base_num','multiple','seat_num','is_win','win_money_num'])
            ->from('mb_game_periphery')
            ->where('game_id=:gid',[
                ':gid' => $game_id
            ])
            ->limit($limit)
            ->offset($offset)
            ->all();
        return $query;
    }

    /**
     * 查询游戏玩家的总榜
     * @return array
     */
    public static function TotalGameResultRanking(){
        /*
        $query = (new Query())
            ->select(['re.user_id','sum(re.win_money) + sum(re.lose_money) as win_money','IFNULL(nullif(mc.icon_pic,\'\'),mc.pic) as pic','mc.nick_name as name','mc.sex'])
            ->from('mb_game_user_record re')
            ->innerJoin('mb_client mc','mc.client_id = re.user_id')
            ->andFilterWhere(['mc.is_inner'=>1])
            ->groupBy('re.user_id')
            ->having(['>','win_money',0])
            ->orderBy('win_money desc')
            ->limit(30)
            ->all();*/

        $SQL = " SELECT m.user_id,m.win_money,IFNULL(nullif(mc.icon_pic,''),mc.pic) as pic, `mc`.`nick_name` AS `name`, `mc`.`sex`  from (
                    SELECT `re`.`user_id`, sum(re.win_money + re.lose_money)  as win_money
                    FROM `mb_game_user_record` `re`
                    GROUP BY `re`.`user_id`
                    ORDER BY `win_money` DESC LIMIT 30 ) as m
                    INNER JOIN `mb_client` `mc` ON mc.client_id = m.user_id ";

        $query = \Yii::$app->db->createCommand($SQL)->queryAll();

        return $query;
    }

    /*
     * 查询游戏玩家的周榜
     */
    public static function WeekGameResultRanking()
    {
        $date = date('Y-m-d');
        $first = 1;
        $w = date('w', strtotime($date));
        $now_start = date('Y-m-d 00:00:00', strtotime("$date -" . ($w ? $w - $first : 6) . ' days'));
        $now_end = date('Y-m-d 23:59:59', strtotime("$now_start +6 days"));
        $start_time = strtotime($now_start);
        $end_time = strtotime($now_end);
        /*
        $query = (new Query())
            ->select(['re.user_id', 'sum(re.win_money) + sum(re.lose_money) as win_money', 'IFNULL(nullif(mc.icon_pic,\'\'),mc.pic) as pic', 'mc.nick_name as name', 'mc.sex'])
            ->from('mb_game_user_record re')
            ->innerJoin('mb_client mc', 'mc.client_id = re.user_id')
            ->andFilterWhere(['mc.is_inner' => 1])
            ->andFilterWhere(['between', 're.remark1', "$start_time", "$end_time"])
            ->andFilterWhere(['>', 'win_money', 0])
            ->groupBy('re.user_id')
            ->orderBy('win_money desc')
            ->limit(30)
            ->all(); */

        $SQL = " SELECT m.user_id,m.win_money,IFNULL(nullif(mc.icon_pic,''),mc.pic) as pic, `mc`.`nick_name` AS `name`, `mc`.`sex`  from (
                    SELECT `re`.`user_id`, sum(re.win_money + re.lose_money)  as win_money
                    FROM `mb_game_user_record` `re`
                    WHERE  (`re`.`remark1` >= '$start_time' and `re`.`remark1` <= '$end_time')
                    GROUP BY `re`.`user_id`
                    ORDER BY `win_money` DESC LIMIT 30 ) as m
                    INNER JOIN `mb_client` `mc` ON mc.client_id = m.user_id ";

        $query = \Yii::$app->db->createCommand($SQL)->queryAll();
        return $query;
    }

            /**
             * 查询游戏玩家的周榜 总结果
             * @param $user_id
             * @return array|bool
             */
    public static function SelfWeekGameResult($user_id){
        $date  = date('Y-m-d');
        $first = 1;
        $w     = date('w',strtotime( $date ));
        $now_start = date('Y-m-d 00:00:00',strtotime("$date -".($w ? $w - $first : 6).' days'));
        $now_end   = date('Y-m-d 23:59:59',strtotime("$now_start +6 days"));
        $start_time = strtotime( $now_start );
        $end_time   = strtotime( $now_end );

        $query = (new Query())
            ->select(['sum(re.win_money) + sum(re.lose_money) as win_money'])
            ->from('mb_game_user_record re')
            ->innerJoin('mb_client mc','mc.client_id = re.user_id')
            ->andFilterWhere(['>','win_money',0])
            ->andFilterWhere(['between','re.remark1',"$start_time","$end_time"])
            ->andFilterWhere(['mc.client_id'=>$user_id])
            ->one();
        return $query['win_money'];
    }


    /**
     * 查询游戏玩家的总榜
     * @param $user_id
     * @return array|bool
     */
    public static function SelfTotalGameResult($user_id){
        $query = (new Query())
            ->select(['sum(re.win_money) + sum(re.lose_money) as win_money'])
            ->from('mb_game_user_record re')
            ->innerJoin('mb_client mc','mc.client_id = re.user_id')
            ->andFilterWhere(['>','win_money',0])
            ->andFilterWhere(['mc.client_id'=>$user_id])
            ->one();
        return $query['win_money'];
    }

    /**
     * 获取游戏的信息ForQiNiuEnterRoom
     * @param $living_id
     * @return array|mixed
     */
    public static function GetGameInfoForQiNiuEnterRoom( $living_id ){
        $cache = \Yii::$app->cache->get('niuniu_game_'.$living_id);
        $cache = json_decode($cache,true);
        $game_info = \Yii::$app->cache->get('niuniu_game_info_'.$living_id);
        $poker_info = json_decode($game_info,true);

        $data = [];
        if(!empty($poker_info))
        {
            $poker_info['sync_time'] = intval($cache['sync_time']);
            $data = $poker_info;
        }
        else
        {
            //用于处理当APP请求关闭游戏时，未收到IM消息缓存被删除，再获取游戏信息接口返回空数据
            if(($cache['game_status'] == 5) || ($cache['game_status'] == 6))
            {
                $now_seat_all =  self::GetNiuNiuGameInfo( $cache['game_id'] );
                if(!empty($now_seat_all))
                {
                    $outAll = [
                        'game_id' => $cache['record_id'],
                        'game_status' => $cache['game_status'],
                        'sync_time' => intval($cache['sync_time'])
                    ];
                    foreach($now_seat_all as $seat)
                    {
                        $poker = json_decode($seat['poker_info'], true);
                        $outAll['poker_info'][$seat['seat_num']] = [
                            'user_id' => $seat['user_id'],
                            'pic' => $seat['pic'],
                            'nick_name' => $seat['nick_name'],
                            'seat_status' => $seat['seat_status'],
                            'is_banker' => $seat['is_banker'],
                            'is_robot' => $seat['is_robot'],
                            'is_living_master' => $seat['is_living_master'],
                            'is_win' => ($seat['win_num']>0?2:1),
                            'seat_num'=>$seat['seat_num'],
                            'chip_num'=>$seat['chip_num'],
                            'base_num' => $seat['base_num'],
                            'multiple' => $seat['multiple'],
                            'poker_result' => $seat['poker_result'],
                            'poker1' => $poker['poker1'],
                            'poker2' => $poker['poker2'],
                            'poker3' => $poker['poker3'],
                            'poker4' => $poker['poker4'],
                            'poker5' => $poker['poker5'],
                            'win_num' => $seat['win_num']
                        ];
                    }
                    $data = $outAll;
                }
            }
        }

        return $data;
    }


}