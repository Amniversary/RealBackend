<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 15-12-13
 * Time: 下午9:36
 */

namespace frontend\zhiboapi\v3;

use frontend\business\ChatGroupUtil;
use frontend\business\ClientUtil;
use frontend\business\EnterRoomNoteUtil;
use frontend\business\LivingGuessUtil;
use frontend\business\LivingPrivateUtil;
use frontend\business\LivingUtil;
use frontend\business\RobotUtil;
use frontend\zhiboapi\IApiExcute;
use frontend\business\ApiCommon;
use frontend\business\NiuNiuGameUtil;
use yii\log\Logger;


/**
 * Class 七牛进入房间
 * @package frontend\zhiboapi\v3
 */
class ZhiBoQiNiuEnterRoom implements IApiExcute
{

    /**
     * 检查参数合法性
     * @param string $error
     */
    private function check_param_ok($dataProtocal,&$error='')
    {
        $fields = ['living_id'];//'wish_type_id',
        $fieldLabels = ['直播id'];//'愿望类别id',
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
        $living_id = $passParams['living_id'];
        $room_no = $passParams['room_no'];



        $living = LivingUtil::GetLivingTicketGuess($living_id, $user_id);


        if (!empty($living['private_views']) || !empty($living['ticket_views']))
        {
            $living['living_type'] = 1;
        }
        if(!in_array($living['living_type'],[3,4]))
        {
            /*$guess_info = LivingGuessUtil::IsOfGuess($room_no,$user_id);
            if(!isset($guess_info))
            {
                $error = '';
                return false;
            }*/
            if (!empty($living['limit_num']))
            {
                if ($living['person_count'] >= $living['limit_num'])
                {
                    //直播间人数已满
                    $error = '直播间人数已满';
                    return false;
                }
            }
        }
        /*$living_info = LivingUtil::GetClientLivingInfo($living_id);

        //判断用户是否已经进入过直播间
        $roominfo = \Yii::$app->cache->get('roominfo_'.$living_info['room_no'].'user_'.$user_id);
        if($roominfo != false)
        {
            $roominfo = json_decode($roominfo,true);
            if($roominfo['user_id'] == $user_id && $roominfo['room_no'] == $living_info['room_no'])
            {
                $living_info['living_type'] = 1;
            }
        }

        //设置缓存
        $rst = [
            'user_id' => $user_id,
            'room_no' => $living_info['room_no'],
        ];
        \Yii::$app->cache->set('roominfo_'.$living_info['room_no'].'user_'.$user_id,json_encode($rst),172800);


        //判断直播间类型，当是密码直播时需要设置限制
        //1是普通直播
        if(in_array($living_info['living_type'],[3,4]))
        {
            $living_personnum = LivingUtil::GetClientLivingInfo($living_id);

            if($living_personnum['person_count'] >= $living_personnum['limit_num'])
            {
                //直播间人数已满
                $error = '直播间人数已满';
                \Yii::getLogger()->log('进入直播间返回数据：'.var_export($rstData,true),Logger::LEVEL_ERROR);
                return false;
            }
        }*/

        if($loginInfo['client_type'] != 2)
        {
            if(!LivingUtil::CheckMoreEnterRoom($living['living_type'],$room_no,$error))
            {
                return false;
            }
        }

        /*if($living_info['living_type'] == 4)  //门票直播
        {
            if(!LivingUtil::SetLivingPasswrodTicketView($room_no,$user_id,$error))
            {
                return false;
            }
        }

        if($living_info['living_type'] == 3) //私密直播
        {
            if(!LivingPrivateUtil::CheckPrivatePassword($room_no,$user_id,$error))
            {
                return false;
            }
        }*/
//        \Yii::getLogger()->log('进入直播间living_enter_room_living_id==:'.$living_id.'   user_id==:'.$user_id,Logger::LEVEL_ERROR);
        $owner = 0;
        $is_police = ($loginInfo['client_type'] == '2'? '1' : '0');
        //\Yii::getLogger()->log('enter room,user_id:'.$user_id.' time:'.strval(time()),Logger::LEVEL_ERROR);
        //开始计算经验或处理上次的经验
        if(!ChatGroupUtil::QiNiuEnterRoom($living_id,$user_id,$deviceType,$owner,$error,0,$is_police))
        {
            return false;
        }

        $is_robot = 0;
        $rstRobot = [];

        if(!in_array($living['living_type'],[3,4,5])) //私密直播、门票直播、假直播过滤机器人
        {
            $robotInfo = RobotUtil::GetRobotPeopleParams($error['living_master_id']);
            $is_robot = 1;
            if (intval($robotInfo['audience_robot_no']) <= 0) {
                $is_robot = 0;
            }

            //进入直播间的同时，获取机器人的信息
            $robotinfo = RobotUtil::GetRobotInfoForUpdateLiving($living_id, $user_id);
            if ($robotinfo) {
                $rstRobot = $robotinfo;
            } else {
                $rstRobot = "";
            }
        }

        $sysMsg = EnterRoomNoteUtil::GetSystemMsgToArray();
        $cache_game_info = \Yii::$app->cache->get('niuniu_game_'.$living_id);
        $game_info_cache = \Yii::$app->cache->get('niuniu_game_info_'.$living_id);
        $is_game = 2;
        $game_info = json_decode($cache_game_info,true);
        if(($cache_game_info === false) || ($game_info['game_status'] > 5) || empty($game_info) || (($game_info['game_status'] < 5) && ($game_info_cache === false)))
        {
            $is_game = 1;
        }

        //入直播间的同时，获取直播间的游戏信息
        $gameinfo = NiuNiuGameUtil::GetGameInfoForQiNiuEnterRoom( $living_id );
        if( $gameinfo ){
            $rstGame = $gameinfo;
        }else{
            $rstGame = "";
        }


        $rstData['has_data']='1';
        $rstData['data_type']='json';
        $rstData['data'] = [
            'owner'=>strval($owner),
            'is_attention'=>$error['attention'],
            'system_msg'=>$sysMsg,
            'is_police' =>strval($is_police),
            'is_robot'  =>strval($is_robot),
            'is_game'   => strval($is_game),
            'robotinfo' =>$rstRobot,
            'gameinfo'  =>$rstGame
        ];
        //根据经度、纬度获取地理信息

        return true;
    }
}