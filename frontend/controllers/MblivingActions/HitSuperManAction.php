<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/1/22
 * Time: 19:07
 */

namespace frontend\controllers\MblivingActions;


use common\models\HitSuperMan;
use frontend\business\ActivityUtil;
use yii\base\Action;

class HitSuperManAction extends Action
{
    public function run()
    {
        $rst = ['code'=>'1','msg'=>''];
        $datas = \Yii::$app->request->post();
        $superMan = $datas['superMan'];
        $hit_number =  $datas['hit_number'];

        $num = \yii::$app->cache->get($superMan.'num');

        //拿到用户的姓名
        if(empty($superMan))
        {
            //去数据库中拿到所有人姓名
            $sqaa  = HitSuperMan::findAll('');
            $data = [];

            foreach ($sqaa as $vv)
            {
                $s = array_search($vv,$sqaa);
                $data[$s]['name'] = $vv['man_name'];
                $data[$s]['num'] = \yii::$app->cache->get($vv['man_name'].'num');
            }

            $rst['code'] = '2';
            $rst['msg'] = $data;
            echo json_encode($rst);
            exit;
        }

        //判断是否有次数
        if(empty($hit_number))
        {
            $num++;
        }
        else{
            $num += $hit_number;
        }

        \yii::$app->cache->set($superMan.'num',$num);

        //最后判断次数大于200，就像数据库中插入
        if(!($num % 200))
        {
            $sql = 'INSERT IGNORE INTO mb_hit_super_man (man_name,hit_num)VALUES (:mn,:hn) ';
            \Yii::$app->db->createCommand($sql,[
                ':mn' => $superMan,
                ':hn' => $num
            ])->execute();

            $updatesql = 'update mb_hit_super_man set hit_num=hit_num+200 WHERE man_name =::mn';
            \Yii::$app->db->createCommand($updatesql,[
                ':mn' => $superMan,
            ])->execute();
        }

        $rst['code'] = '0';
        $rst['msg'] = $num;
        echo json_encode($rst);
        exit;
    }
}