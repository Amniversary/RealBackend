<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/26
 * Time: 18:13
 */

namespace frontend\controllers\FuckActions;




use common\components\tenxunlivingsdk\TimRestApi;
use yii\base\Action;

class ClearGroupAction extends Action
{
    public function run($client_no)
    {
        set_time_limit(0);
        $sql = '
        select room_id,l.living_id,r.other_id
        from mb_client c
        INNER JOIN mb_living l ON c.client_id = l.living_master_id
        INNER JOIN mb_chat_room r ON  l.living_id = r.living_id
        where client_no = :ld';
        $rst = \Yii::$app->db->createCommand($sql,[':ld'=>strval($client_no)])->queryOne();
        $group_id = strval($rst['other_id']);
        if(!TimRestApi::group_destroy_group($group_id,$error))
        {
            print_r($error);
            echo "<br />";
            exit;
        }
        echo $client_no;
        echo "<br />";
        echo $group_id;
        echo "<br />";
        echo "ok\n";
    }

} 