<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/22
 * Time: 11:13
 */

namespace console\controllers\BackendActions;


use common\components\tenxunlivingsdk\TimRestApi;
use common\models\AuthorizationList;
use frontend\business\JobUtil;
use yii\base\Action;
use yii\db\Query;
use yii\log\Logger;

class TestBhAction extends Action
{
    public function run()
    {

        print_r(\Yii::$app->basePath);
        exit;
        $query = (new Query())->select(['backend_user_id'])->from('wc_user')->all();
        foreach ($query as $item) {
            $get = \Yii::$app->cache->get('app_backend_'.$item['backend_user_id']);
            if($get != false){
                $decode = json_decode($get,true);
                if(!empty($decode)){
                    $AuthInfo = AuthorizationList::findOne(['record_id'=>$decode['record_id']]);
                    $data = $AuthInfo->attributes;
                    $data['backend_user_id'] = $decode['backend_user_id'];
                    print_r($data);
                    \Yii::$app->cache->set('app_backend_'.$decode['backend_user_id'],json_encode($data));
                }
            }
        }

        echo "ok";
        exit;
        if(!TimRestApi::group_forbid_send_msg('@TGS#3TYTIZKEW',strval(1663777),0,$error))
        {
            return false;
        }
        echo "ok\n";

        exit;
        \Yii::$app->cache->delete('activity_girl_cache');
        echo 'ok';
        exit;
        set_time_limit(0);
        $living_id = [  //直播成员满 常用直播清除
            '22697181',
            '21973406',
            '24213940',
            '23737365',
            '23681930',
            '22164094',
            '23669265',
            '24165102',
            '23579421',
            '21959816',
            '23588354',
            '20765605',
            '24422383',
            '23249085',
            '21818474',
            '24389883',
            '20836711'
        ];
        foreach($living_id as $ld)
        {
            $sql = '
            select room_id,l.living_id,r.other_id
            from mb_client c
            INNER JOIN mb_living l ON c.client_id = l.living_master_id
            INNER JOIN mb_chat_room r ON  l.living_id = r.living_id
            where client_no = :ld';
            $rst = \Yii::$app->db->createCommand($sql,[':ld'=>$ld])->queryOne();
            $group_id = strval($rst['other_id']);
            if(!TimRestApi::group_destroy_group($group_id,$error))
            {
                print_r($error);
                echo "\n";
                continue;
            }
            echo $ld;
            echo $group_id;
            echo "\n";
            echo "ok\n";
        }




        exit;
        $query = (new Query())
            ->select(['cr.room_id','cr.living_id','rm.user_id'])
            ->from('mb_chat_room cr')
            ->innerJoin('mb_chat_room_member rm','cr.room_id = rm.group_id')
            ->where('rm.owner = 2')
            ->orderBy('cr.living_id')
            ->all();
        $sql = 'insert into mb_living_admin (living_id,admin_id) VALUES ';
        $man = count($query);
        $i = 0;
        foreach ($query as $key)
        {
            $sql.= sprintf('(%d,%d)',$key['living_id'],$key['user_id']);
            if($i >= $man)
            {
                $sql.= ';';
            }
            else
            {
                $sql.= ',';
            }
            $i++ ;
            echo "$i \n";
        }
        \Yii::$app->db->createCommand($sql)->execute();
        echo "ok\n";



        exit;
        set_time_limit(0);
        ini_set('memory_limit', '2G');
        //测试服务器
        $begin = 10000001;
        $end = 15000000;
        //正式服务器
        $begin = 20000001;
        $end = 25000000;
        //本次测试
        $begin = 30000001;
        $end = 35000000;
        //生成房间号
        $begin = 20000001;
        $end = 25000000;
        //生成1到99999999的所有整数
        $codes = [];
        for ($i = $begin; $i <= $end; $i++)
        {
            $codes[] = sprintf('%08s',$i);
        }
        shuffle($codes);
        $len = count($codes);
        for ($i = 0; $i < $len; $i++)
        {
            //加入异步任务处理
            $data=[
                'room_no'=>$codes[$i]
            ];
            if(!JobUtil::AddJob('room_unique_no',$data,$error))
            {
                \Yii::getLogger()->log('job save error:'.$error,Logger::LEVEL_ERROR);
            }
            echo $i."\n";
        }

        echo 'ok';
    }
} 