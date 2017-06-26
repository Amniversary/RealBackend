<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/5/11
 * Time: 9:39
 */

namespace console\controllers\BackendActions;



use frontend\business\ClientUtil;
use frontend\business\JobUtil;
use yii\base\Action;

class GenClientThumbPicAction extends Action
{
    public function run()
    {
        //生成用户缩略图
        set_time_limit(0);

        $sql = 'select * from (select client_id, pic from mb_client where pic <> \'\' and (middle_pic is null or icon_pic is null or main_pic is null)
union
select client_id,pic from mb_client where pic <> \'\' and (middle_pic =\'\' or icon_pic = \'\' or main_pic =\'\')) c limit 100';
        $users = \Yii::$app->db->createCommand($sql)->queryAll();
        $len = count($users);
        for($i = 0; $i < $len; $i ++)
        {
            $client_id= $users[$i]['client_id'];
            $pic = $users[$i]['pic'];
            if(!JobUtil::AddPicJob('deal_client_pic',['client_id'=>$client_id,'pic'=>$pic],$error))
            {
                echo 'index:'.$i.' error:'. $error."\n";
            }
            echo 'index:'.$i.' ok'."\n";
        }
        echo "\n".'finish'.strval($len);
        exit;
        $userList = ClientUtil::GetShouldGenPicClients(200);
        $ok_count = 0;
        $fail_count = 0;
        $deal_count = 0;
/*        while($userList > 0)
        {*/
            $start = microtime(true);
            foreach($userList as $user)
            {
                $deal_count ++;
                $error = '';
                if(!ClientUtil::GenClientPicThumb($user['client_id'],$user['pic'],$error))
                {
                    $fail_count ++;
                }
                else
                {
                    $ok_count ++;
                }
                echo 'deal_count:'.strval($deal_count).' ok_count:'.strval($ok_count).' fail_count:'.strval($fail_count)."\n";
            }
            $end = microtime(true);
            $dis = $end - $start;
            echo 'ok time:'.strval($dis)."\n";
/*            $userList = ClientUtil::GetShouldGenPicClients();
        }*/
        echo 'finish work'."\n";
    }
} 