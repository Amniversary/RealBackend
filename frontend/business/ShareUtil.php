<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/1/11
 * Time: 16:35
 */

namespace frontend\business;


use common\components\SystemParamsUtil;
use common\models\ActivityShareInfo;
use yii\log\Logger;

class ShareUtil
{
    /**
     * 获取分享信息
     * @param $wish_id
     * @param $user_id
     * @param $shareInfo
     * @param $error
     * @return array
     */
    public static function GetShareInfoForWish($wish_id,&$shareInfo,&$error)
    {
        $shareInfo = [];
        $wish = WishUtil::GetWishRecordById($wish_id);
        if(!isset($wish))
        {
            $error = '愿望信息不存在';
            return false;
        }
        if($wish->status === 0)
        {
            $error = '该愿望已被取消';
            return false;
        }
        $shareInfo['title'] =$wish->wish_name;;//SystemParamsUtil::GetSystemParam('system_share_wish_title',true);//'新年新气象，和我一起来美愿实现愿望吧！';//$wish->wish_name;
        $shareInfo['content'] = mb_substr($wish->discribtion,0,100,'utf-8') ;//SystemParamsUtil::GetSystemParam('system_share_wish_content',true);//'下载美愿，快来支持我的愿望，您可获得3倍奖金，同时为我赢取1倍奖金！';// $wish->discribtion;
        $rand_num = rand(20000,999999);
        $wish_id =strval(intval($wish->wish_id) + $rand_num + 99);
        $time = time();
        $signStr = self::GetWishViewSign($wish_id, $rand_num, $time);
        $url = sprintf('http://%s/mywish/wishview?wish_id=%s&rand_num=%s&time=%s&sign=%s',
            $_SERVER['HTTP_HOST'],
            $wish_id,
            $rand_num,
            $time,
            $signStr
        );
        $shareInfo['link'] = $url;
        $shareInfo['pic'] = $wish->pic1;// SystemParamsUtil::GetSystemParam('system_share_weixin_pic',true);
        return $shareInfo;
    }

    /**
     * 获取真实id
     * @param $nowId
     * @param $rand_num
     */
    public static function GetRealId($nowId, $rand_num)
    {
        return $nowId - $rand_num - 99;
    }

    /**
     * 获取邀请链接
     * @param $user_id
     * @param $shareInfo
     * @param $error
     * @return array
     */
    public static function GetShareInfoForInvite($user_id,&$shareInfo,&$error)
    {
        $shareInfo = [];
        $user = PersonalUserUtil::GetAccontInfoById($user_id);
        if(!isset($user))
        {
            $error = '用户信息不存在';
            return false;
        }
        $shareInfo['title'] = SystemParamsUtil::GetSystemParam('system_share_invite_title',true);//'美愿，一个帮你实现愿望的app！';
        $shareInfo['content'] = SystemParamsUtil::GetSystemParam('system_share_wish_content',true);//'美愿是一个共享社交金融平台，通过共享愿望的方式，实现每一个愿望。';//sprintf('您的好友【%s】，邀请您一起来玩美愿',$user->nick_name);
        $rand_num = rand(20000,999999);
        $userId =strval(intval($user_id) + $rand_num + 99);
        $time = time();
        $signStr = self::GetInviteSign($userId, $rand_num, $time);
        $url = sprintf('http://%s/mywish/invite?user_id=%s&rand_num=%s&time=%s&sign=%s',
            $_SERVER['HTTP_HOST'],
            $userId,
            $rand_num,
            $time,
            $signStr
            );
        $shareInfo['link'] = $url;
        $shareInfo['pic'] = SystemParamsUtil::GetSystemParam('system_share_weixin_pic',true);
        return true;
    }


    /**
     * 获取愿望签名
     * @param $wish_id
     * @param $rand_num
     * @param $time
     * @return string
     */
    public static function GetWishViewSign($wish_id, $rand_num,$time)
    {
        $sourceStr = sprintf('wish_id=%s&rand_num=%s&time=%s&fosjdosfwe=sfjwjpfsjdf0293jf0wr0jf0we9ur2jh0sdjc0ewjd0jsfoiasjcbhc9we8',
            $wish_id,
            $rand_num,
            $time);
        return sha1($sourceStr);
    }

    /**
     * 获取邀请签名
     * @param $user_id
     * @param $rand_num
     * @param $time
     * @return string
     */
    public static function GetInviteSign($user_id, $rand_num,$time)
    {
        $sourceStr = sprintf('user_id=%s&rand_num=%s&time=%s&fosjdosfwe=sfjwjpfsjdf0293jf0wr0jf0we9ur2jh0sdjc0ewjd0jsfoiasjcbhc9we8',
            $user_id,
            $rand_num,
            $time);
        //\Yii::getLogger()->log('sign:'.$sourceStr, Logger::LEVEL_ERROR);
        return sha1($sourceStr);
    }

    /**
     * 根据分享id 获取分享信息
     * @param $share_id
     * @return null|static
     */
    public static function GetShareInfo($share_id)
    {
        return ActivityShareInfo::findOne(['share_id'=>$share_id]);
    }
}