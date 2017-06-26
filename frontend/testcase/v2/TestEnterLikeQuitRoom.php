<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/18
 * Time: 12:51
 */

namespace frontend\testcase\v2;
use frontend\testcase\IApiExcute;
use yii\log\Logger;

/**
 * 用户进入直播间，点赞（随机10-300个),退出直播间
 * Class EnterRomClickLikeSendGiftQuitRoom
 * @package frontend\testcase\v2
 */

class TestEnterLikeQuitRoom implements  IApiExcute
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

        $quit_room = TestQuitRoom::QuitRoom($user_info);
        \Yii::getLogger()->log('模拟测试退出直播间返回信息====>' . var_export($quit_room, true), Logger::LEVEL_ERROR);

        return true;
    }
}