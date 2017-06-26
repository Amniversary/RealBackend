<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/17
 * Time: 17:25
 */
namespace frontend\testcase\v2;
use frontend\testcase\IApiExcute;
use yii\log\Logger;


/**
 * 用户进入直播间，点赞（随机10-300个),送礼物（随机10-300个),发送弹幕信息（10-300）退出直播间
 * Class TestEnterRomClickLikeSendGiftQuitRoomDanmaku
 * @package frontend\testcase\v2
 */
class TestEnterRomClickLikeSendGiftQuitRoomDanmaku implements  IApiExcute
{
    function excute_action($dataProtocal,&$rstData,&$error, $extendData= array())
    {

        $user = new TestApiUserLogin();
        if (empty($user) && !isset($user)) {
            \Yii::getLogger()->log('模拟用户登陆时出现了现有用户已全部登陆完，请重新增加新测试用户>', Logger::LEVEL_ERROR);
            return false;
        }
        $user_info = $user->outInfo;
        $user_info['living_id'] = $dataProtocal['living_id'];
        $enter_room = TestQiNiuEnterRoom::EnterRoom($user_info);
        $user_info['other_id'] = $enter_room['other_id'];
        \Yii::getLogger()->log('模拟测试进入直播间返回信息====>' . var_export($enter_room, true), Logger::LEVEL_ERROR);
        $click_like_max = rand(1, $dataProtocal['click_like_num']);
        for ($i = 1; $i <= $click_like_max; $i++) {
            $click_like = TestClickLike::ClickLike($user_info);
            \Yii::getLogger()->log('模拟测试点赞返回信息====>' . var_export($click_like, true), Logger::LEVEL_ERROR);
        }

        $send_gift_max = rand(1, $dataProtocal['send_gift_num']);
        for ($i = 1; $i <= $send_gift_max; $i++) {
            $user_info['money_type'] = 1;
            $send_gift = TestSendGift::SendGift($user_info);
            \Yii::getLogger()->log('模拟测试送礼物返回信息====>' . var_export($send_gift, true), Logger::LEVEL_ERROR);
        }

        $send_danmaku_max = rand(1, $dataProtocal['danmaku_num']);
        for ($i = 1; $i <= $send_danmaku_max; $i++) {
            $send_danmaku = TestSendDanmaku::SendDanmaku($user_info);
            \Yii::getLogger()->log('模拟测试弹幕返回信息====>' . var_export($send_danmaku, true), Logger::LEVEL_ERROR);
        }

        $send_info_count = 10;
        for($i = 1;$i<=$send_info_count;$i++)
        {
            $send_user_info = TestUserSendInfo::SendUserInfo($user_info);  //直播间用户发送的消息
            \Yii::getLogger()->log('模拟测直播间用户发送消息的返回信息====>' . var_export($send_user_info, true), Logger::LEVEL_ERROR);
        }
        //$quit_room = TestQuitRoom::QuitRoom($user_info);
        //\Yii::getLogger()->log('模拟测试退出直播间返回信息====>' . var_export($quit_room, true), Logger::LEVEL_ERROR);

        return true;
    }
}