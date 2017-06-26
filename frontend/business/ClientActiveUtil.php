<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/4/26
 * Time: 10:48
 */

namespace frontend\business;


use common\models\ClientActive;
use frontend\business\RongCloud\SystemMessageUtil;

class ClientActiveUtil
{
    /**
     * 根据用户id获取活跃记录
     * @param $user_id
     */
    public static function GetClientActiveInfoByUserId($user_id)
    {
        return ClientActive::findOne(['user_id'=>$user_id]);
    }

    /**
     * 根据用户等级记录id 获取等级信息
     * @param $activeId
     * @return ClientActive|null
     */
    public static function GetClientActiveById($activeId)
    {
        return ClientActive::findOne(['active_id'=>$activeId]);
    }

    /**
     * 更新前端用户等级
     * @param $ClientActive
     */
    public static function UpdateClientLevel($ClientActive)
    {
        $NewActive =  self::GetClientActiveById($ClientActive->active_id);
        if($ClientActive->level_no !== $NewActive->level_no){
            $data = [
                'key_word'=>'send_level_im',
                'levelNo'=>$NewActive->level_no,
                'userId'=>$NewActive->user_id,
            ];
            if(!JobUtil::AddImJob('tencent_im',$data,$error)){
                \Yii::error($error.' 更新等级im队列失败');
            }
        }
    }
}