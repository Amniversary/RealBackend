<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/25
 * Time: 11:43
 */

namespace frontend\business\SendImMessage;


use common\components\getui\GeTuiUtil;
use frontend\business\AttentionUtil;
use frontend\business\LivingPrivateUtil;
use yii\helpers\Console;
use yii\log\Logger;

/**
 * 作者：何必涵
 * 修改：王伟 2017-1-11 15：21
 * Class SetGetuiIm
 * @package frontend\business\SendImMessage
 */
class SetGetuiIm implements ImExcute
{
    public function excute_im($jobData,&$error,$params =[])
    {
        if(!($jobData instanceof \stdClass))
        {
            $error = '不是json对象，数据异常';
            return false;
        }

        \Yii::getLogger()->log('测试create',Logger::LEVEL_ERROR);

        $page = 1;
        $page_size = 50;
        $private_info = LivingPrivateUtil::GetLivingPrivateByLivingMasterId($jobData->user_id);
        $my_friends_id = AttentionUtil::GetFunForGeTui($jobData->user_id,1,50);
        $fCount = count($my_friends_id);
        fwrite(STDOUT, Console::ansiFormat("---create_living_im--".var_export($fCount,true)."\n", [Console::FG_GREEN]));
        while($fCount > 0)
        {
            //穿透消息字符个数限制在255，不存用json格式，只用字符串拼接，节约字符串
            $text_content = '5-'.strval($jobData->user_id).'-'.$jobData->group_id.'-'.strval($jobData->living_id).'-'.$jobData->nick_name;

            $getui_ids =[];
            foreach($my_friends_id as $key=>$fv)
            {
                if($private_info->private_id > 0){         //过滤私密直播
                    continue;
                }
                $getui_ids[] =['cid'=>$fv['getui_id'],'alias'=>$fv['unique_no']];
            }
            $show_content = sprintf('您的好友[%s]正在直播，快去瞅瞅吧！',$jobData->nick_name);
            if(count($getui_ids) > 0)
            {
                if(!GeTuiUtil::PushListMessage($show_content,$text_content,$getui_ids,$jobData->app_id,$error))  //个推群发，最多50个人
                {
                    fwrite(STDOUT, Console::ansiFormat("---create_living_im--  error:$error".' content:'.$text_content."\n", [Console::FG_GREEN]));
                }
                else
                {
                    fwrite(STDOUT, Console::ansiFormat("---create_living_im-- ok -- ".' content:'.$text_content."\n", [Console::FG_GREEN]));
                }
            }
            $page ++;
            $my_friends_id = AttentionUtil::GetFunForGeTui($jobData->user_id,$page,$page_size);
            $fCount = count($my_friends_id);
        }

        return true;
    }
} 